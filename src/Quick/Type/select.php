<?php
namespace Quick\Quick\Type;

class select extends \Quick\Quick\Column{

    public function bindSearchLogic($builder, $operator, $value){
        return $builder->where($this->getRname(),"=", $value);
    }
}