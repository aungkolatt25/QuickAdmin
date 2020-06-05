<?php
namespace Quick\Quick\Traits;
use Arr;
trait Attribute{
    public function getAttribute($attribute){
        return Arr::get($this->attributes,$attribute,null);
    }
}