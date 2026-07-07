<?php

declare(strict_types=1);

if (isset($_SERVER['REQUEST_URI'])) {
    $uri = preg_replace('#^/laravel#', '', $_SERVER['REQUEST_URI']) ?: '/';
    $uri = str_replace('/./', '/', $uri);
    if ($uri === '/.' || $uri === '' || substr($uri, -2) === '/.') {
        $uri = '/';
    }
    $_SERVER['REQUEST_URI'] = $uri;
}

require __DIR__ . '/public/index.php';
