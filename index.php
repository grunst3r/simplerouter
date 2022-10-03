<?php
include('vendor/autoload.php');

use Luigu\GustRouter\Router;


header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");

$router = new Router;

$router->name('home')->get('/', function() use($router){
    $url = $router->route('buscador',['slug' => 'enviar-contenido']);
    return '<form action="'.$url.'" method="post">
    <label for="fname">First name:</label><br>
    <input type="text" id="fname" name="fname"><br>
    <label for="lname">Last name:</label><br>
    <input type="text" id="lname" name="lname">
    <p>
    <input type="checkbox" id="vehicle1" name="vehicle[]" value="Bike">
    <label for="vehicle1"> I have a bike</label><br>
    <input type="checkbox" id="vehicle2" name="vehicle[]" value="Car">
    <label for="vehicle2"> I have a car</label><br>
    <input type="checkbox" id="vehicle3" name="vehicle[]" value="Boat">
    <label for="vehicle3"> I have a boat</label>
    <p>
    <input type="submit" value="Submit">
  </form>';

});

$router->name('buscador')->post('/buscador/{slug}', function($slug){
    return $slug;
});

$router->name('blog')->get('/blog', function($get){
    return "hola mundo del blog <br/><br/>";
});


$router->group('/admin', function() use($router){
    $router->name('admin')->get('/', function(){
        return "hola mundo mundo";
    });
    $router->name('admin.blog')->post('/blogs', function(){
        return "hola mundo mundo";
    });
});

$router->name('blogfxD')->get('/blog/{id}-{slug}', function($get){
    return '<p>:: <b> ---- '. $get['seo'].'</b></p>';
});
$router->name('blogfxD')->post('/blog/{id}-{slug}', function($get){
    return '<p>:: <b> ---- '. $get['seo'].'</b></p>';
});


$router->get('/blog---------------------------------', function(){
    return '<p>:: <b> ---- </b></p>';
});




$router->setError(function(){
    return "<h1>Nuevo error 404</h1>";
});

$router->run();