<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

(new Auth(storage()))->logout();
header('Location: /admin/');
exit;
