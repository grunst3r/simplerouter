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
        $cont = $fn();
        $this->grupo = '';
        return $cont;
    }

    public function domain($path, $fn){
        $this->domain = $path;
        return $fn();
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
        foreach($this->routes[$this->getMethod()] as $route){
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

        if($callback == false){
            $this->notFound();
        }

        $params = $this->getBody();
        $params = array_merge($params, $parametos);
        if(is_array($callback)){
            $controller = new $callback[0];
            $controller->action = $callback[1];
            $view = $controller->{$callback[1]}($params);
        }else{
            $view = call_user_func($callback, $params);
        }
        $this->view($view);
    }


    private function view($view){
        if(is_array($view)){
            header('Content-type: application/json');
            echo json_encode($view);
        }else{
            echo $view;
        }
    }

    public function notFound(){
        header("HTTP/1.0 404 Not Found");
        if($this->error == null){
            echo "404 Not Found";
        }else{
            echo $this->error;
        }
        exit;
    }

    public function setError($fn){
        $this->error = $fn();
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