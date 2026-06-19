<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$auth = new Auth(storage());
$error = '';

if ($auth->user()) {
    header('Location: /admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validate($_POST['csrf'] ?? null);
    if ($auth->login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TruePoint Admin Login</title>
  <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-login">
  <main class="login-card">
    <h1>TruePoint Admin</h1>
    <p>Sign in to manage pricing, provisioning defaults, and orders.</p>
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <?= Csrf::field() ?>
      <label>Email <input type="email" name="email" required autocomplete="username"></label>
      <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
      <button type="submit">Sign in</button>
    </form>
  </main>
</body>
</html>
