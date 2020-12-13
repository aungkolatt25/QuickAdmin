<?php
namespace Quick\Quick\Type;
use Carbon\Carbon;

class date extends \Quick\Quick\Column{
    
    public function getInputType(){
        return "text";
    }

    public function getClass(){
        $class = $this->attributes["class"]??"";
        return config("quick.template.controlcss")." date $class";
    }

    public function getValueAccessable(){
        $data = parent::getValueAccessable();
        if(!$data)
            return "";
        return Carbon::parse($data)->format("Y-m-d");
    }

    public function getValueUserable($data){
        return Carbon::parse($data)->format("d-m-Y");
    }
    /*
        For Search
    */
    public function bindSearchLogic($builder, $operator, $value){
        return $builder->whereDate($this->getRname(),"=", date($value));
    }
}