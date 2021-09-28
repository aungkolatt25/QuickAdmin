<?php
namespace Quick\Quick\Traits;
use Arr;
trait Attribute{
    /**
     * get attribute value
     */
    public function getAttribute($attribute){
        return Arr::get($this->attributes,$attribute,null);
    }
}