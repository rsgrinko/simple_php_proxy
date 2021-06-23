<?php 
    /*
        Простейщий прокси на PHP. Написан just-for-fun. Практического применения нет.
        Roman S Grinko <rsgrinko@gmail.com>
    */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

$newPath = ltrim($path, '/');
if ($query) {
    $newPath .= '?' . $query;
}

$base = 'https://it-stories.ru'; // сайт, на котором планируется бродить
$local = '//'.$_SERVER['SERVER_NAME'];
$proxyUrl = $base .'/'. $newPath;

$contents = @file_get_contents($proxyUrl);

$headers = $http_response_header;
$firstLine = $headers[0];

if ($contents === false) {
    header("HTTP/1.1 503 Proxy error");
    die("Proxy error - failed to get content from $proxyUrl");
}
$allowedHeaders = "!^(http/1.1|server:|content-type:|last-modified|access-control-allow-origin|Content-Length:|Accept-Ranges:|Date:|Via:|Connection:|X-|age|cache-control|vary)!i";

foreach ($headers as $header) {
    if (preg_match($allowedHeaders, $header)) {
        header($header);
    }
}

// подменяем ссылки для возможности хождения через прокси и выводим
echo str_replace($base, $local, $contents);