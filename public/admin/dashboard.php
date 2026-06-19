<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$auth = new Auth(storage());
$auth->requireAdmin();

$content = storage()->read('content.json', []);
$settings = storage()->read('settings.json', []);
$orders = storage()->read('orders.json', ['orders' => []]);
$notice = $_GET['notice'] ?? '';

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TruePoint Admin Dashboard</title>
  <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
  <header class="admin-header">
    <div>
      <strong>TruePoint Admin</strong>
      <span><?= e($auth->user()['email'] ?? '') ?></span>
    </div>
    <nav>
      <a href="/" target="_blank">View site</a>
      <a href="/admin/logout.php">Logout</a>
    </nav>
  </header>

  <main class="admin-shell">
    <?php if ($notice): ?><div class="notice"><?= e($notice) ?></div><?php endif; ?>

    <section class="panel warning">
      <h2>Launch checklist</h2>
      <ul>
        <li>Change the default admin password immediately.</li>
        <li>Set PayPal credentials in <code>app/config.php</code>.</li>
        <li>Replace placeholder NTRIP host/mountpoint values with TruePoint-provided production values.</li>
        <li>Confirm PHP <code>mail()</code> works on the cPanel account or configure SMTP through the host.</li>
      </ul>
    </section>

    <section class="panel">
      <h2>Business display settings</h2>
      <form method="post" action="/admin/action.php" class="grid-form">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="save_business">
        <label>Display name <input name="display_name" value="<?= e($settings['business']['display_name'] ?? '') ?>"></label>
        <label>Network name <input name="network_name" value="<?= e($settings['business']['network_name'] ?? '') ?>"></label>
        <label>Sales email <input type="email" name="sales_email" value="<?= e($settings['business']['sales_email'] ?? '') ?>"></label>
        <label>Support email <input type="email" name="support_email" value="<?= e($settings['business']['support_email'] ?? '') ?>"></label>
        <label>Tax rate <input type="number" step="0.0001" min="0" max="1" name="tax_rate" value="<?= e($settings['checkout']['tax_rate'] ?? 0) ?>"></label>
        <label class="wide">VAT note <textarea name="vat_note" rows="3"><?= e($settings['business']['vat_note'] ?? '') ?></textarea></label>
        <button type="submit">Save settings</button>
      </form>
    </section>

    <section class="panel">
      <h2>Subscription pricing</h2>
      <form method="post" action="/admin/action.php">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="save_prices">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Plan</th><th>Retail price before tax</th><th>COGS</th><th>Duration days</th><th>Featured</th></tr></thead>
            <tbody>
              <?php foreach (($content['plans'] ?? []) as $plan): ?>
                <tr>
                  <td><?= e($plan['id']) ?></td>
                  <td><input type="number" min="0" step="0.01" name="plans[<?= e($plan['id']) ?>][price]" value="<?= e($plan['price']) ?>"></td>
                  <td><input type="number" min="0" step="0.01" name="plans[<?= e($plan['id']) ?>][cogs]" value="<?= e($plan['cogs']) ?>"></td>
                  <td><input type="number" min="1" step="1" name="plans[<?= e($plan['id']) ?>][duration_days]" value="<?= e($plan['duration_days']) ?>"></td>
                  <td><input type="checkbox" name="plans[<?= e($plan['id']) ?>][featured]" value="1" <?= !empty($plan['featured']) ? 'checked' : '' ?>></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <button type="submit">Save prices</button>
      </form>
    </section>

    <section class="panel">
      <h2>Provisioning defaults</h2>
      <?php $prov = $settings['provisioning'] ?? []; ?>
      <form method="post" action="/admin/action.php" class="grid-form">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="save_provisioning">
        <label>NTRIP host <input name="ntrip_host" value="<?= e($prov['ntrip_host'] ?? app_config('provisioning.ntrip_host')) ?>"></label>
        <label>NTRIP port <input type="number" name="ntrip_port" value="<?= e($prov['ntrip_port'] ?? app_config('provisioning.ntrip_port')) ?>"></label>
        <label>Default mountpoint <input name="default_mountpoint" value="<?= e($prov['default_mountpoint'] ?? app_config('provisioning.default_mountpoint')) ?>"></label>
        <label>Credential prefix <input name="credential_prefix" value="<?= e($prov['credential_prefix'] ?? app_config('provisioning.credential_prefix')) ?>"></label>
        <label>Support email <input type="email" name="support_email" value="<?= e($prov['support_email'] ?? app_config('provisioning.support_email')) ?>"></label>
        <label class="checkbox"><input type="checkbox" name="ntrip_tls" value="1" <?= !empty($prov['ntrip_tls']) ? 'checked' : '' ?>> TLS connection</label>
        <button type="submit">Save provisioning</button>
      </form>
    </section>

    <section class="panel">
      <h2>Orders</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Order</th><th>Status</th><th>Plan</th><th>Customer</th><th>Amount</th><th>Created</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach (array_reverse($orders['orders'] ?? []) as $order): ?>
              <tr>
                <td><code><?= e($order['local_id'] ?? '') ?></code></td>
                <td><?= e($order['status'] ?? '') ?></td>
                <td><?= e($order['plan_id'] ?? '') ?></td>
                <td><?= e(($order['customer']['name'] ?? '') . ' <' . ($order['customer']['email'] ?? '') . '>') ?></td>
                <td><?= e(($order['amount'] ?? '') . ' ' . ($order['currency'] ?? 'EUR')) ?></td>
                <td><?= e($order['created_at'] ?? '') ?></td>
                <td>
                  <form method="post" action="/admin/action.php" class="inline-form">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="mark_order">
                    <input type="hidden" name="local_id" value="<?= e($order['local_id'] ?? '') ?>">
                    <select name="status">
                      <?php foreach (['paid', 'provisioned', 'requires_review', 'cancelled'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($order['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit">Update</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <h2>Change admin password</h2>
      <form method="post" action="/admin/action.php" class="grid-form">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="change_password">
        <label>New password <input type="password" name="password" minlength="12" required></label>
        <label>Confirm password <input type="password" name="password_confirm" minlength="12" required></label>
        <button type="submit">Change password</button>
      </form>
    </section>
  </main>
</body>
</html>
