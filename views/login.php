<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <h2 class="text-center mb-4">Login</h2>
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post" action="<?= url('/login') ?>">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require 'layout.php'; ?>