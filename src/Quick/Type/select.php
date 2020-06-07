<?php
namespace Quick\Quick\Type;

class select extends \Quick\Quick\Column{

    public function bindSearchLogic($builder, $operator, $value){
        return $builder->where($this->getRname(),"=", $value);
    }

    public function getClass(){
        $class = $this->attributes["class"]??"";
        return config("quick.template.controlcss")." select2 $class";
    }
}