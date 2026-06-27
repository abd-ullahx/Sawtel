<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/') {
    if (file_exists(__DIR__.'/public'.$uri)) {
        return false;
    }
    
    // Support serving files that have /public/ prepended in the URL
    if (str_starts_with($uri, '/public/')) {
        $relativePath = substr($uri, 7); // Strip '/public'
        $filePath = __DIR__.'/public'.$relativePath;
        if (file_exists($filePath) && !is_dir($filePath)) {
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'otf' => 'font/otf',
            ];
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';
            header('Content-Type: '.$contentType);
            readfile($filePath);
            exit;
        }
    }
}

require_once 'index.php';
