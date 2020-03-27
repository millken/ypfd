<?php
declare (strict_types = 1);

namespace App\View;

use \Psr\Http\Message\ResponseInterface as Response;

class View
{

    private $smarty;
    private $response;

    public function __construct($smarty)
    {
        $this->smarty = $smarty;
        $this->response = new \GuzzleHttp\Psr7\Response();
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->smarty->assign($name);
        } else {
            $this->smarty->assign($name, $value);
        }
    }

    public function append($tpl_var, $value = null, $merge = false)
    {
        return $this->smarty->append($tpl_var, $value, $merge);
    }

    public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
    {
        $this->smarty->registerPlugin($type, $tag, $callback, $cacheable, $cache_attr);
    }
    
    public function fetch(string $name, $data=[])
    {
        $this->smarty->assign($data);
        $output = $this->smarty->fetch($name);
        return $output;
    }

    public function display(string $name): Response
    {
        $output = $this->smarty->fetch($name);
        $this->response->getBody()->write($output);
        return $this->response;
    }

    public function render($template, $data = []): Response
    {
        $this->response->getBody()->write($this->fetch($template, $data));
        return $this->response;
    }
}
