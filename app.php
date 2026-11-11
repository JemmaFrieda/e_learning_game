<?php
declare(strict_types=1);

define('APP_NAME', 'Emma Learn');
define('APP_BASE_URL', '/EMMA PROJECT');
define('APP_ENV', 'development');

function app_url(string $path = ''): string
{
    return rtrim(APP_BASE_URL, '/') . '/' . ltrim($path, '/');
}
