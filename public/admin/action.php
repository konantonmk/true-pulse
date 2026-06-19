<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$auth = new Auth(storage());
$auth->requireAdmin();
Csrf::validate($_POST['csrf'] ?? null);

$action = $_POST['action'] ?? '';

try {
    if ($action === 'save_business') {
        storage()->update('settings.json', function (&$settings) {
            $settings['business']['display_name'] = trim((string) ($_POST['display_name'] ?? ''));
            $settings['business']['network_name'] = trim((string) ($_POST['network_name'] ?? ''));
            $settings['business']['sales_email'] = trim((string) ($_POST['sales_email'] ?? ''));
            $settings['business']['support_email'] = trim((string) ($_POST['support_email'] ?? ''));
            $settings['business']['vat_note'] = trim((string) ($_POST['vat_note'] ?? ''));
            $settings['checkout']['tax_rate'] = max(0, min(1, (float) ($_POST['tax_rate'] ?? 0)));
            return true;
        }, []);
        redirect_notice('Business settings saved.');
    }

    if ($action === 'save_prices') {
        $postedPlans = $_POST['plans'] ?? [];
        storage()->update('content.json', function (&$content) use ($postedPlans) {
            foreach ($content['plans'] as &$plan) {
                $id = $plan['id'];
                if (!isset($postedPlans[$id])) {
                    continue;
                }
                $plan['price'] = round((float) ($postedPlans[$id]['price'] ?? $plan['price']), 2);
                $plan['cogs'] = round((float) ($postedPlans[$id]['cogs'] ?? $plan['cogs']), 2);
                $plan['duration_days'] = max(1, (int) ($postedPlans[$id]['duration_days'] ?? $plan['duration_days']));
                $plan['featured'] = !empty($postedPlans[$id]['featured']);
            }
            return true;
        }, []);
        redirect_notice('Pricing saved.');
    }

    if ($action === 'save_provisioning') {
        storage()->update('settings.json', function (&$settings) {
            $settings['provisioning'] = [
                'ntrip_host' => trim((string) ($_POST['ntrip_host'] ?? '')),
                'ntrip_port' => (int) ($_POST['ntrip_port'] ?? 2101),
                'ntrip_tls' => !empty($_POST['ntrip_tls']),
                'default_mountpoint' => trim((string) ($_POST['default_mountpoint'] ?? '')),
                'credential_prefix' => trim((string) ($_POST['credential_prefix'] ?? 'TP')),
                'support_email' => trim((string) ($_POST['support_email'] ?? '')),
            ];
            return true;
        }, []);
        redirect_notice('Provisioning defaults saved.');
    }

    if ($action === 'mark_order') {
        $localId = $_POST['local_id'] ?? '';
        $status = $_POST['status'] ?? 'requires_review';
        storage()->update('orders.json', function (&$data) use ($localId, $status) {
            foreach ($data['orders'] as &$order) {
                if (($order['local_id'] ?? '') === $localId) {
                    $order['status'] = $status;
                    $order['updated_at'] = gmdate('c');
                }
            }
            return true;
        }, ['orders' => []]);
        redirect_notice('Order updated.');
    }

    if ($action === 'change_password') {
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        if (strlen($password) < 12 || $password !== $confirm) {
            redirect_notice('Password must be at least 12 characters and match confirmation.');
        }
        $email = $auth->user()['email'];
        $hash = $auth->hashPassword($password);
        storage()->update('users.json', function (&$data) use ($email, $hash) {
            foreach ($data['users'] as &$user) {
                if (($user['email'] ?? '') === $email) {
                    $user['password'] = $hash;
                }
            }
            return true;
        }, ['users' => []]);
        redirect_notice('Password changed.');
    }

    redirect_notice('Unknown action.');
} catch (Throwable $e) {
    log_event('admin_action_error', ['message' => $e->getMessage(), 'action' => $action]);
    redirect_notice('Admin action failed. Check app/logs/app.log.');
}

function redirect_notice(string $message): never
{
    header('Location: /admin/dashboard.php?notice=' . rawurlencode($message));
    exit;
}
