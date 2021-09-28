<?php

namespace Quick\Quick;

use Illuminate\Database\Eloquent\Model;
use Arr;
use Quick\Quick\QuickHelper;

/**
 * Change Quick File to QuickData
 * Page Setting and Column
 */
class QuickData
{
    public $file = "";
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $data = [];

    /**
     * Get QuickData Object from QuickFile
     * @param string $filename
     * @return QuickData
     */
    public static function get($fileName){
        $file = app_path("Builder/{$fileName}.php");
        if(!file_exists($file))
            throw new \Exception("{$fileName} File Not Found");

        $data = include app_path("Builder/{$fileName}.php");
        $data["name"] = $fileName;
        return Self::getLoaded($data);
    }

    /**
     * Get QuickData Object from Quick Array
     * @param array
     * @return QuickData
     */
    public static function getLoaded($data){
        $quickdata = new QuickData();
        $quickdata->file = $data["name"];
        $quickdata->makeParse($data);
        return $quickdata;
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle(){
        return $this->data["sigular"];
    }

    /**
     * Get Column using name(database column name)
     * @param string $name
     * @return Column
     */
    public function getColumn($name){
        $column = collect($this->data["columns"])->filter(function($column) use($name){
            return $column->name == $name;
        });
        return $column?$column->first():[];
    }

    /**
     * Get Table Name
     * @return string
     */
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

    /**
     * make QuickModel using filename
     * @param string $fileName
     * @return 
     */
    public static function makeModel($fileName){
        $quickdata = Self::get($fileName);
        return $quickdata->getModel();
    }

    /**
     * Get QuickMoel using QuickData
     * @return QuickModel
     */
    public function getModel(){
        return \Quick\Quick\QuickModel::make($this);
    }

    /**
     * get data
     * @return array
     */
    public function getData(){
        return $this->data;
    }

    /**
     * get visibled columns list
     * @return array
     */
    public function getVisibleColumns($visible){
        return collect($this->data["columns"])->filter(function(Column $column) use($visible){
            return $column->isVisible($visible);
        });
    }

    /**
     * is requested to show the data as paginate
     * @return boolean
     */
    public function isPaginate(){
        return $this->data->get("isPaginate", true);
    }

    /**
     * is requested to show the total
     * @return boolean
     */
    public function isTotal(){
        return $this->data->get("isTotal", false);
    }
    
    /**
     * get relation using relation id
     * @return Relation
     */
    public function getRelation($relationid){
        $relation = collect($this->data["relations"])->first(function($relation) use($relationid){
            return $relation->id == $relationid;
        });

        if($relation == null)
            throw new \Exception("'$relationid' Relation is not Found in '{$this->file}'");
        return $relation;
    }

    /**
     * get all relations
     * @return []
     */
    public function getRelations(){
        $relation = collect($this->data["relations"]??[]);
        return $relation;
    }

    /**
     * Has Relation Define in QuickFile
     * @return boolean
     */
    public function hasRelation($relationid){
        $relation = collect($this->data["relations"])->first(function(Relation $relation) use($relationid){
            return $relation->id == $relationid;
        });
        return $relation != null;
    }

    /**
     * Make Array To Related Object
     */
    public function makeParse($data){
        $this->data = collect($data);

        /**
         * Change each realtion to Relation Object
         */
        $this->data["relations"] = collect($data["relations"]??[])->map(function($value){
            return new Relation($value);
        });

        /**
         * Change each column to Column Object
         */
        $this->data["columns"] = collect($data["columns"])->map(function($value){
            $relation = null;
            if(isset($value["relation"]))
                $relation = $this->getRelation($value["relation"]);
            $column = null;
            eval('$column = new \Quick\Quick\Type\\'.$value["type"].'($this, $value, $relation);');
            return $column;
        });
        /**
         * Change pageSettings array to PageSettings Object
         */
        $this->data["pageSettings"] = new PageSettings($this->data["pageSettings"]??[]);
    }

    /**
     * Get Binded Constroller Name to perform the action
     * @return string
     */
    public function getBindedController(){
        return $this->data["controller"]??"\Quick\Controllers\QuickBuilder";
    }

    /**
     * Get PageSetting Object
     * @return PageSetting
     */
    public function getPageSettings(){
        return $this->data["pageSettings"];
    }
}

