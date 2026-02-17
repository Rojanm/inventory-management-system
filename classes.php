<?php
// ==================== CORE CLASSES ====================

abstract class Model {
    protected $db;
    protected $table;
    public function __construct() {
        $this->db = db();
    }
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    public function create($data) {
        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    public function update($id, $data) {
        $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $set WHERE id=:id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function where($conditions) {
        $where = implode(' AND ', array_map(fn($k) => "$k=:$k", array_keys($conditions)));
        $sql = "SELECT * FROM {$this->table} WHERE $where";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll();
    }
}

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) session_start();
    }
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    public static function delete($key) {
        unset($_SESSION[$key]);
    }
    public static function destroy() {
        session_destroy();
    }
    public static function regenerate() {
        session_regenerate_id(true);
    }
}

class AuthMiddleware {
    public static function handle() {
        Session::start();
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }
}

class RoleMiddleware {
    public static function handle($roles) {
        $userRole = Session::get('user_role');
        if (!in_array($userRole, $roles)) {
            die('Access denied');
        }
    }
}

class CsrfMiddleware {
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// ==================== MODELS ====================

class User extends Model {
    protected $table = 'users';
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}

class Product extends Model {
    protected $table = 'products';
    public function withRelations() {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                ORDER BY p.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    public function lowStock() {
        $stmt = $this->db->query("SELECT * FROM products WHERE quantity <= reorder_level");
        return $stmt->fetchAll();
    }
    public function totalValue() {
        $stmt = $this->db->query("SELECT SUM(price * quantity) as total FROM products");
        return $stmt->fetch()['total'] ?? 0;
    }
    public function getStockValueByCategory() {
        $sql = "SELECT c.name, SUM(p.price * p.quantity) as value
                FROM products p
                JOIN categories c ON p.category_id = c.id
                GROUP BY c.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

class Category extends Model {
    protected $table = 'categories';
}

class Supplier extends Model {
    protected $table = 'suppliers';
}

class StockMovement extends Model {
    protected $table = 'stock_movements';
    public function recent($limit = 10) {
        $sql = "SELECT sm.*, p.name as product_name, u.full_name as user_name 
                FROM stock_movements sm
                JOIN products p ON sm.product_id = p.id
                JOIN users u ON sm.user_id = u.id
                ORDER BY sm.movement_date DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    public function getDailySummary($date) {
        $sql = "SELECT 
                    type,
                    SUM(quantity) as total_quantity,
                    COUNT(*) as transaction_count
                FROM stock_movements
                WHERE DATE(movement_date) = ?
                GROUP BY type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
    public function getMonthlySummary($year, $month) {
        $start = "$year-$month-01";
        $end = date('Y-m-t', strtotime($start));
        $sql = "SELECT 
                    DATE(movement_date) as date,
                    type,
                    SUM(quantity) as total_quantity
                FROM stock_movements
                WHERE DATE(movement_date) BETWEEN ? AND ?
                GROUP BY DATE(movement_date), type
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }
}

// ==================== SERVICES ====================

class StockService {
    private $db;
    public function __construct() {
        $this->db = db();
    }
    public function stockIn($productId, $quantity, $userId, $note = '') {
        $this->db->beginTransaction();
        try {
            $product = (new Product())->find($productId);
            if (!$product) throw new Exception("Product not found");
            $prevQty = $product['quantity'];
            $newQty = $prevQty + $quantity;

            $stmt = $this->db->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQty, $productId]);

            $stmt = $this->db->prepare("INSERT INTO stock_movements 
                (product_id, user_id, type, quantity, previous_quantity, new_quantity, reference_note, movement_date) 
                VALUES (?, ?, 'IN', ?, ?, ?, ?, NOW())");
            $stmt->execute([$productId, $userId, $quantity, $prevQty, $newQty, $note]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    public function stockOut($productId, $quantity, $userId, $note = '') {
        $this->db->beginTransaction();
        try {
            $product = (new Product())->find($productId);
            if (!$product) throw new Exception("Product not found");
            if ($product['quantity'] < $quantity) throw new Exception("Insufficient stock");
            $prevQty = $product['quantity'];
            $newQty = $prevQty - $quantity;

            $stmt = $this->db->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQty, $productId]);

            $stmt = $this->db->prepare("INSERT INTO stock_movements 
                (product_id, user_id, type, quantity, previous_quantity, new_quantity, reference_note, movement_date) 
                VALUES (?, ?, 'OUT', ?, ?, ?, ?, NOW())");
            $stmt->execute([$productId, $userId, $quantity, $prevQty, $newQty, $note]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

// ==================== CONTROLLERS ====================

abstract class Controller {
    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . "/views/{$view}.php";
    }
    protected function redirect($url) {
        header("Location: " . APP_URL . $url);
        exit;
    }
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

class AuthController extends Controller {
    public function showLogin() {
        $this->render('login', [
            'csrf' => CsrfMiddleware::generateToken(),
            'error' => null
        ]);
    }
    public function login() {
        if (!$this->isPost()) $this->redirect('/login');
        if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = (new User())->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            Session::start();
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['full_name']);
            Session::set('user_role', $user['role']);
            $this->redirect('/dashboard');
        }
        $this->render('login', [
            'error' => 'Invalid credentials',
            'csrf' => CsrfMiddleware::generateToken()
        ]);
    }
    public function logout() {
        Session::destroy();
        $this->redirect('/login');
    }
}

class DashboardController extends Controller {
    public function index() {
        AuthMiddleware::handle();
        $productModel = new Product();
        $stockModel = new StockMovement();
        $data = [
            'totalProducts' => count($productModel->all()),
            'totalValue' => $productModel->totalValue(),
            'lowStock' => $productModel->lowStock(),
            'recentMovements' => $stockModel->recent(10),
            'categoryValues' => $productModel->getStockValueByCategory()
        ];
        $this->render('dashboard', $data);
    }
}

class ProductController extends Controller {
    public function index() {
        AuthMiddleware::handle();
        $products = (new Product())->withRelations();
        $this->render('products/index', ['products' => $products]);
    }
    public function create() {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);
        if ($this->isPost()) {
            if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');
            $data = [
                'sku' => $_POST['sku'],
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'supplier_id' => $_POST['supplier_id'],
                'price' => $_POST['price'],
                'reorder_level' => $_POST['reorder_level'] ?? 10
            ];
            (new Product())->create($data);
            Session::set('success', 'Product created');
            $this->redirect('/products');
        }
        $categories = (new Category())->all();
        $suppliers = (new Supplier())->all();
        $this->render('products/create', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'csrf' => CsrfMiddleware::generateToken()
        ]);
    }
    public function edit($id) {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);
        $product = (new Product())->find($id);
        if (!$product) {
            Session::set('error', 'Product not found');
            $this->redirect('/products');
        }
        if ($this->isPost()) {
            if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');
            $data = [
                'sku' => $_POST['sku'],
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'supplier_id' => $_POST['supplier_id'],
                'price' => $_POST['price'],
                'reorder_level' => $_POST['reorder_level']
            ];
            (new Product())->update($id, $data);
            Session::set('success', 'Product updated');
            $this->redirect('/products');
        }
        $categories = (new Category())->all();
        $suppliers = (new Supplier())->all();
        $this->render('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'csrf' => CsrfMiddleware::generateToken()
        ]);
    }
    public function delete($id) {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);
        if ($this->isPost()) {
            if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');
            (new Product())->delete($id);
            Session::set('success', 'Product deleted');
        }
        $this->redirect('/products');
    }
}

class StockController extends Controller {
    public function stockIn() {
        AuthMiddleware::handle();
        $stockService = new StockService();
        if ($this->isPost()) {
            if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');
            try {
                $stockService->stockIn(
                    $_POST['product_id'],
                    $_POST['quantity'],
                    Session::get('user_id'),
                    $_POST['reference_note'] ?? ''
                );
                Session::set('success', 'Stock added');
            } catch (Exception $e) {
                Session::set('error', $e->getMessage());
            }
            $this->redirect('/stock/in');
        }
        $products = (new Product())->all();
        $this->render('stock/in', [
            'products' => $products,
            'csrf' => CsrfMiddleware::generateToken()
        ]);
    }
    public function stockOut() {
        AuthMiddleware::handle();
        $stockService = new StockService();
        if ($this->isPost()) {
            if (!CsrfMiddleware::validateToken($_POST['csrf'] ?? '')) die('Invalid CSRF');
            try {
                $stockService->stockOut(
                    $_POST['product_id'],
                    $_POST['quantity'],
                    Session::get('user_id'),
                    $_POST['reference_note'] ?? ''
                );
                Session::set('success', 'Stock removed');
            } catch (Exception $e) {
                Session::set('error', $e->getMessage());
            }
            $this->redirect('/stock/out');
        }
        $products = (new Product())->all();
        $this->render('stock/out', [
            'products' => $products,
            'csrf' => CsrfMiddleware::generateToken()
        ]);
    }
    public function history() {
        AuthMiddleware::handle();
        $movements = (new StockMovement())->recent(1000);
        $this->render('stock/history', ['movements' => $movements]);
    }
}

class ReportController extends Controller {
    public function index() {
        AuthMiddleware::handle();
        $this->render('reports/index');
    }
    public function daily() {
        AuthMiddleware::handle();
        $date = $_GET['date'] ?? date('Y-m-d');
        $summary = (new StockMovement())->getDailySummary($date);
        $this->render('reports/daily', ['date' => $date, 'summary' => $summary]);
    }
    public function monthly() {
        AuthMiddleware::handle();
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');
        $data = (new StockMovement())->getMonthlySummary($year, $month);
        $this->render('reports/monthly', ['year' => $year, 'month' => $month, 'data' => $data]);
    }
    public function lowStock() {
        AuthMiddleware::handle();
        $products = (new Product())->lowStock();
        $this->render('reports/lowstock', ['products' => $products]);
    }
}