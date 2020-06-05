<?php

namespace Quick\Quick;

use Illuminate\Database\Eloquent\Model;
use Arr;
use Quick\Quick\QuickHelper;

class QuickData
{
    public $file = "";
    protected $data = [];

    //
    public static function get($fileName){

        $file = app_path("Builder/{$fileName}.php");
        if(!file_exists($file))
            throw new \Exception("{$fileName} File Not Found");

        $data = include app_path("Builder/{$fileName}.php");
        $data["name"] = $fileName;
        return Self::getLoaded($data);
    }

    public static function getLoaded($data){
        $quickdata = new QuickData();
        $quickdata->file = $data["name"];
        $quickdata->makeParse($data);
        return $quickdata;
    }

    public function getTitle(){
        return $this->data["sigular"];
    }

    public function getColumn($name){
        $column = collect($this->data["columns"])->filter(function($column) use($name){
            return $column->name == $name;
        });
        return $column?$column->first():[];
    }

    public function getTable(){
        $table = $this->data->first(function($value, $key){
            return $key = "table";
        });
        if(!$table) {
            throw new \Exception("No Table");
        }
            
        return $table;
    }

    public function getOptions(){
        return $this->data["options"]??[];
    }


    public static function makeModel($fileName){
        $quickdata = Self::get($fileName);
        return $quickdata->getModel();
    }

    public function getModel(){
        return \Quick\Quick\QuickModel::make($this);
    }

    public function getData(){
        return $this->data;
    }

    public function getVisibleColumns($visible){
        return collect($this->data["columns"])->filter(function($column) use($visible){
            return $column->isVisible($visible);
        });
    }

    public function isPaginate(){
        return $this->data->get("isPaginate", true);
    }

    public function isTotal(){
        return $this->data->get("isTotal", false);
    }

    public function getCreatedLink(){
        return Arr::get($this->data,"links.create", qurl($this->file."/create"));
    }
    /*

    public function getLink($stage, $data = []){
        $link = Arr::get($this->data,"links.$stage", qurl($this->file."/create"));
        preg_replace_callback
    }
    */

    public function getRelation($relationid){
        $relation = collect($this->data["relations"])->first(function($relation) use($relationid){
            return $relation->id == $relationid;
        });

        if($relation == null)
            throw new \Exception("'$relationid' Relation is not Found in '{$this->file}'");
        return $relation;
    }

    public function hasRelation($relationid){
        $relation = collect($this->data["relations"])->first(function($relation) use($relationid){
            return $relation->id == $relationid;
        });
        return $relation != null;
    }

    public function makeParse($data){
        $this->data = collect($data);

        $this->data["relations"] = collect($data["relations"]??[])->map(function($value){
            return new \Quick\Quick\Relation($value);
        });

        $this->data["columns"] = collect($data["columns"])->map(function($value){
            $relation = null;
            if(isset($value["relation"]))
                $relation = $this->getRelation($value["relation"]);
            eval('$column = new \Quick\Quick\Type\\'.$value["type"].'($this, $value, $relation);');
            return $column;
        });
    }

    public function getBindedController(){
        return $this->data["controller"]??"\Quick\Controllers\QuickBuilder";
    }
}

