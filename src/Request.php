<?php 
namespace Luigu\GustRouter;


class Request {
    

    public function __construct()
    {
        return "hola";
    }

    public function getPath(){
        // server url
        $path = $_SERVER['REQUEST_URI'] ?? false;
        $position = strpos($path, '?');

        if($position !== false){
            $path = substr($path, 0, $position);
        }else{
            $path = $path;
        }
        return rtrim($this->inDomain().$path, '/\\');;
    }

    public function inDomain(){
        return $_SERVER['HTTP_HOST'];
    }


    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'] ?? false;
    }

    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function getBody()
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        $data = !$data ? json_decode(file_get_contents('php://input'), true) : $data;
        return !empty($data) ? $data: [];
    }

}