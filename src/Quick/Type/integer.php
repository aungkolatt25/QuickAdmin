<?php
namespace Quick\Quick\Type;

class integer extends \Quick\Quick\Column{
    
    public function getInputType(){
        return "text";
    }
}