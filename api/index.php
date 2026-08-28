<?php
$path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/';
$root=dirname(__DIR__);
switch($path){
 case '/login-casal.php': require $root.'/login-casal.php'; break;
 case '/setup-casal.php': require $root.'/setup-casal.php'; break;
 case '/casal.php': require $root.'/casal.php'; break;
 case '/convite-casal.php': require $root.'/convite-casal.php'; break;
 case '/api/couple-public.php': require __DIR__.'/couple-public.php'; break;
 case '/api/couple-image.php': require __DIR__.'/couple-image.php'; break;
 default: http_response_code(404); header('Content-Type:text/plain; charset=utf-8'); echo 'Página não encontrada.';
}
