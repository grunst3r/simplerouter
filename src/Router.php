<?php 
namespace Luigu\GustRouter;

class Router extends Request{

    protected $routes = [];
    private $name;
    private $domain;
    private $grupo;
    private $error;

    private function addRoute($method, $path, $callback) {
        $path = rtrim($this->getDomain().$this->grupo.$path, '/\\');
        $this->routes[$method][] = [
            "path" => $path,
            "callback" => $callback,
            "name" => $this->name
        ];
        $this->name = '';
        //$this->grupo = '';
    }

    public function getDomain(){
        return ( $this->domain ) ? $this->domain : $_SERVER['HTTP_HOST'];
    }

    public function name($name){
        $this->name = $name;
        return $this;
    }

    public function group($path, $fn){
        $this->grupo = $path;
        call_user_func($fn);
        $this->grupo = '';
    }

    public function domain($path, $fn){
        $this->domain = $path;
        call_user_func($fn);
        $this->domain = '';
    }

    public function get($path, $callback) {
        $this->addRoute("GET", $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    private function patternMatches($pattern, $uri)
    {
      $pattern = preg_replace('/{(.*?)}/', '(.*?)', $pattern);
      return boolval(preg_match_all('#^' . $pattern . '$#', $uri ));
    }

    public function run(){
        $uri = $this->getPath();
        $callback = false;
        $parametos = [];
        
        
        $meto = $this->getMethod();
        
        foreach($this->routes[$meto] as $route){
            if($this->patternMatches($route['path'], $uri)){
                $callback = $route['callback'];
                $preg = str_replace("/","\/",preg_replace('/{(.*?)}/', '(.*?)', $route['path']) );
                preg_match("#^".$preg."$#", $uri, $_paramts);
                preg_match_all('/{(.*?)}/', $route['path'], $names );
                $this->array_kshift($_paramts);
                foreach($names[1] as $k => $par){
                    $parametos[$par] = $_paramts[($k+1)];
                }
            }
        }
        
        $params = $this->getBody();
        $params = array_merge($params, $parametos);
        if(is_array($callback)){
            $controller = new $callback[0];
            $controller->action = $callback[1];
            $view = $controller->{$callback[1]}($params);
        }else{
            if(!empty($callback)){
                $view = call_user_func($callback, $params);
            }else{
                return $this->notFound();
            }
        }
        return $this->view($view);
    }


    private function view($view){
        if(is_array($view) || is_object($view) ){
            header('Content-type: application/json');
            echo json_encode($view);
        }else{
            echo $view;
        }
    }

    public function notFound(){
        header("HTTP/1.0 401 Not Found");
        if(empty($this->error)){
            echo "404 Not Found";
        }else{
            echo call_user_func($this->error);
        }
    }

    public function setError($fn){
        $this->error = $fn;
    }


    private function array_kshift(&$arr){
        list($k) = array_keys($arr);
        $r  = array($k=>$arr[$k]);
        unset($arr[$k]);
        return $r;
    }


    public function listRouters(){
        $lista = [];
        foreach($this->routes as $rutas){
            foreach($rutas as $ruta){
                $lista[] = $ruta;
            }
        }
        return $lista;
    }

    public function route($nombre, $parametos = []){
        $rutas = $this->listRouters();
        foreach($rutas as $ruta){
            if($ruta['name'] == $nombre){
                $pattern = $ruta['path'];
                foreach($parametos as $k => $parameto){
                    $pattern = preg_replace('/{'.$k.'}/', $parameto, $pattern);
                }
                return !empty($pattern) ? $this->https().$pattern : $this->https().$ruta['path'];
            }
        }
    }

    public function https(){
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
            $scheme = 'https://';
        else
            $scheme = 'http://';
        return $scheme;
    }

}