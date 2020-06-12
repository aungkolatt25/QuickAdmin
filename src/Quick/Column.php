<?php
namespace Quick\Quick;
use Quick\Quick\Traits\Attribute;
use Quick\Quick\Traits\ArrayImplement;
use Arr;

class Column implements \ArrayAccess{
    use Attribute;
    use ArrayImplement;

    private $quickdata;
    private $relation;
    private $attributes = [];
    private $many = false;

    public function __construct($quickdata, $attributes, \Quick\Quick\Relation $relation = null){
        $this->quickdata = $quickdata;
        $this->attributes = $attributes;
        $this->relation = $relation;
    }

    public function __get($attribute){
        return $this->getAttribute($attribute);
    }

    public function isRelationType(){
        return Arr::get($this->attributes,"relation", "") != "";
    }

    public function getRelation(){
        return $this->relation;
    }

    public function assign(\Quick\QuickModel $model, $value){
        $model->{$this->getName()} = $value;
    }

    /*
        Get Name
    */
    public function getName(){
        return $this->attributes["name"];
    }

    /*
        Getting Request Name
    */
    public function getRequestName(){
        $more = $this->isRelationType()?"[]":"";
        return isset($this->attributes["requestName"])?
                $this->attributes["requestName"].$more:
                $this->attributes["name"].$more;
    }

    public function getRequestAcessName(){
        if($this->many !== false){
            return $this->getRequestName().".$this->many";
        }
        return $this->getRequestName();
    }

    public function getRequestNameForMany(){
        return $this->getRequestName()."[]";
    }

    public function getValidationRuleName(){
        return str_replace("[]",".*",$this->getRequestName());
    }

    public function getValidationRuleNameForMany(){
        return str_replace("[]",".*",$this->getRequestNameForMany());
    }

    public function getRname(){
        if($this->isRelationType()){
            if($this->getRelation()->type == "belongsToMany"){
                return $this->attributes["rname"];
            }
        }
        
        return $this->attributes["name"];
    }

    /*
        @ Summary 
        @ Name, Not Relation Key's Name
        @ Get name
    */
    public function getValueName(){
        if(isset($this->attributes["rname"]) && isset($this->attributes["name"]))
            return $this->attributes["rname"];
        return $this->attributes["name"];
    }

    public function getType(){
        return $this->attributes["type"];
    }

    public function getUI($stage, $data = null){
        //Load Custom
        $customView = $this->quickdata->file.".{$stage}.".$this->getName();
        
        $loadDefault = function($stage){
            $relation = $this->getRelation()?$this->getRelation()->type:"";
            $firstView = "quick::UI.normal.{$stage}.".$this->getType();
            if($relation && view()->exists($firstView."_$relation")){
                return $firstView."_$relation";
            }
            return $firstView;
        };
        //Load Custom or General
        $view = view()->exists($customView)?$customView:$loadDefault($stage);
        $all = ["column"=>$this, "quickdata"=>$this->quickdata];
        if(is_array($data))
            $all = array_merge($data,$all);
        
        return view($view, $all);
    }

    public function getValue($data, $isTarget=false){
        $callback = $this->setValue;
        
        if(!$this->isRelationType())
            return $callback?$callback($data):$this->getValueUserable($data->{$this->name});
        if($isTarget)
            return $callback?$callback($data):($this->getValueUserable($data->{$this->rname})??"");
        $relation = $data->relation($this->relation->id, $this->quickdata)->getResults();

        if($relation instanceof \Illuminate\Database\Eloquent\Collection){
            return $callback?$callback($relation):$relation;
        }
        if($callback){
            return $callback($relation);
        }
        if($relation){
            return $this->getValueUserable($relation->{$this->getValueName()});
        }
        return "";
    }

    public function isVisible($visible){
        return collect(Arr::get($this->attributes, "visible",[]))->contains($visible);
    }

    public function getInputType(){
        return $this->attributs["type"];
    }

    public function getClass(){
        $class = $this->attributes["class"]??"";
        return config("quick.template.controlcss")." $class";
    }
    
    public function getValueAccessable(){
        return request($this->getRequestAcessName(), null);
    }

    public function getValueUserable($data){
        return $data;
    }

    public function bindSearchLogic($builder, $operator, $value){
        return $builder->where($this->getRname(),"like", "%$value%");
    }

    public function getRules($stage = ""){
        $rules = $this->rules??"";
        if($this->rules && is_array($this->rules)){
            $rules = $this[$stage]??"";
        }
        return is_array($rules)?$rules:$rules;
    }

    public function setManyIndex($index){
        $this->many = $index;
    }
}