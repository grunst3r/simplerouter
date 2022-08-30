<?php
include('vendor/autoload.php');

use Luigu\GustRouter\Controllers\Home;
use Luigu\GustRouter\Request;
use Luigu\GustRouter\Router;



$router = new Router;

$router->name('home')->get('/', [Home::class,'index']);

$router->name('home')->post('/buscador', function($post){
    return $post;
});

$router->name('blog')->get('/blog/{seo}', function($get){
    return '<p>:: <b> ---- '. $get['seo'].'</b></p>';
});

$router->name('blog')->get('/blog', function($get){
    return "hola mundo del blog <br/><br/>". $get['sub'];
});



$router->group('/admin', function() use($router){
    $router->name('admin')->get('/', function(){
        return "hola mundo mundo";
    });
    $router->name('admin.blog')->get('/blogs', function(){
        return "hola mundo mundo";
    });
});


$router->setError(function(){
    return "<h1>Nuevo error 404</h1>";
});

print_r($router->route('blog',['seo' => 'holaaa']));

$router->run();