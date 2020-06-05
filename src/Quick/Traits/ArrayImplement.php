<?php
namespace Quick\Quick\Traits;

trait ArrayImplement{
    public function offsetExists($index){
        return isset($this->attributes[$index]);
    }

    public function offsetGet($index){
        return $this->attributes[$index];
    }

    public function offsetSet($index, $value){
        $this->attributes[$index] = $value;
    }

    public function offsetUnset($index){
        unset($this->attributes[$index]);
    }
}
    