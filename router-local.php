<?php

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$path = str_replace('\\', '/', $path);

if (preg_match('~(?:^|/)(?:\.[^/]*|vendor|node_modules|composer\.(?:json|lock)|[^/]+\.(?:sql(?:\.zst)?|zip|tar(?:\.zst)?|bak))(?:/|$)~i', $path)) {
    http_response_code(404);
    return true;
}

$site = getenv('LAUNION_LOCAL_SITE');
if ($site === 'portal' && preg_match('~^/website(?:/|$)~i', $path)) {
    http_response_code(404);
    return true;
}

if (in_array($site, ['cms', 'website'], true) && !is_file($_SERVER['DOCUMENT_ROOT'] . $path)) {
    require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
    return true;
}

return false;
