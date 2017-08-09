<?php

namespace houdunwang\view;
class Base
{
    private $tpl;
    private $var = [];

    public function make()
    {
        $this->tpl = "./app/" . APP . '/view/' . CONTROLLER . '/' . ACTION . '.php';
//        p($this->tpl);
        return $this;
    }

    public function with($data)
    {
        $this->var = $data;
        return $this;
    }

    public function __toString()
    {
        extract($this->var);
        include $this->tpl;
        return '';
    }
}