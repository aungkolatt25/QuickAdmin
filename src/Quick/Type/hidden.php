<?php
namespace Quick\Quick\Type;

class hidden extends \Quick\Quick\Column{

    public function getInputType(){
        return "hidden";
    }
}