<?php
namespace Quick\Quick;
use Quick\Quick\Traits\Attribute;
use Quick\Quick\Traits\ArrayImplement;
use Illuminate\Support\Arr;

/**
 * Represent Database Column
 * Render Approprate Html depends on stage
 */
class Column implements \ArrayAccess{
    use Attribute;
    use ArrayImplement;

    private $quickdata;
    private $relation;
    private $attributes = [];
    /**
     * For Bulk Action
     */
    private $many = false;

    public function __construct($quickdata, $attributes, \Quick\Quick\Relation $relation = null){
        $this->quickdata = $quickdata;
        $this->attributes = $attributes;
        $this->relation = $relation;
    }

    /**
     * accessing attribute value as object's attribute
     */
    public function __get($attribute){
        return $this->getAttribute($attribute);
    }

    /**
     * determine column is relation type
     */
    public function isRelationType(){
        return Arr::get($this->attributes,"relation", "") != "";
    }

    /**
     * Get Relation Objection
     * @return Relation
     */
    public function getRelation(){
        return $this->relation;
    }

    /**
     * assign logic
     * Assign value to related model
     */
    public function assign(QuickModel $model, $value){
        $model->{$this->getName()} = $value;
    }

    /**
     * Get Name(Database column name)
     * @return String
     */
    public function getName(){
        return $this->attributes["name"];
    }

    /**
     * Get Request Name
     * Use to send the data
     * If requestName is not defined,use database column name
     * @return String
     */
    public function getRequestName(){
        return isset($this->attributes["requestName"])?
                $this->attributes["requestName"]:
                $this->attributes["name"];
    }

    /**
     * Get Request Name For Bulk Action
     * @return String
     */
    public function getRequestNameForMany(){
        return $this->getRequestName()."[]";
    }

    /**
     * Get the name to acess the the send data
     * @return String
     */
    public function getRequestAcessName(){
        $requestName = $this->getRequestName();
        if($this->many !== false){
            return $requestName.".$this->many";
        }
        return $requestName;
    }

    /**
     * Get Rulename for Validation
     * @return String
     */
    public function getValidationRuleName(){
        return str_replace("[]",".*",$this->getRequestName());
    }

    /**
     * Get Rulename for Bulk Validation
     * @return String
     */
    public function getValidationRuleNameForMany(){
        return str_replace("[]",".*",$this->getRequestNameForMany());
    }

    /**
     * Get Relation Name
     * @return String
     */
    public function getRname(){
        if($this->isRelationType()){
            if($this->getRelation()->type == "belongsToMany"){
                return $this->attributes["rname"];
            }
        }
        
        return $this->attributes["name"];
    }

    /**
     * Get Name to access the value
     * @return String 
     */
    public function getValueName(){
        if(isset($this->attributes["rname"]) && isset($this->attributes["name"]))
            return $this->attributes["rname"];
        return $this->attributes["name"];
    }

    /**
     * Get Column Type
     * @return String
     */
    public function getType(){
        return $this->attributes["type"];
    }

    /**
     * Get UI related with stage
     * @param string $stage
     * @param Model|null $data
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getUI($stage, $data = null){
        //custom view name on stage
        $customView = $this->quickdata->file.".{$stage}.".$this->getName();
        
        $loadDefault = function($stage){
            $relation = $this->getRelation()?$this->getRelation()->type:"";
            $firstView = "quick::UI.normal.{$stage}.".$this->getType();
            if($relation && view()->exists($firstView."_$relation")){
                return $firstView."_$relation";
            }
            return $firstView;
        };
        //Determine customize view or default view
        $view = view()->exists($customView)?$customView:$loadDefault($stage);
        $all = ["column"=>$this, "quickdata"=>$this->quickdata];
        if(is_array($data))
            $all = array_merge($data,$all);
        
        return view($view, $all);
    }

    /**
     * Get value from data
     * @param QuickModel $data
     * @param isTarget $isTarget
     * @return mix
     */
    public function getValue($data, $isTarget=false){
        $callback = $this->setValue;
        
        if(!$this->isRelationType())
            return $callback?$callback($data):$this->getValueUserable($data->{$this->name});
        if($isTarget)
            return $callback?$callback($data, null):($this->getValueUserable($data->{$this->rname})??"");
        $relation = $data->relation($this->relation->id, $this->quickdata)->getResults();

        if($relation instanceof \Illuminate\Database\Eloquent\Collection){
            return $callback?$callback($relation, $data):$relation;
        }
        if($callback){
            return $callback($relation, $data);
        }
        if($relation){
            return $this->getValueUserable($relation->{$this->getValueName()});
        }
        return "";
    }

    /**
     * Get Request Value(Sended Value)
     * @return string|array
     */
    public function getRequestValue(){
        return request($this->getRequestAcessName());
    }

    /**
     * is visible on stage
     * @return boolean
     */
    public function isVisible($visible){
        return collect(Arr::get($this->attributes, "visible",[]))->contains($visible);
    }

    public function getInputType(){
        return $this->attributs["type"];
    }

    /**
     * get value(processable value);
     * @return mix
     */
    public function getValueAccessable(){
        return request($this->getRequestAcessName(), null);
    }

    /**
     * Get Value for User Readable
     * @return mix
     */
    public function getValueUserable($data){
        return $data;
    }
    /**
     * Bind Search Logic On Builder
     * @param Builder
     * @param String $operator
     * @param String $value
     * @return Builder
     */
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

    /**
     * Set Bulk cursor
     * @param int $index
     */
    public function setManyIndex($index){
        $this->many = $index;
    }

    /**
     * Get Control Size on Stage
     * @param string $stage
     * @return string
     */
    public function size($stage = ""){
        $default = $this->quickdata->getPageSettings()->getControlSize($stage.".item");
        if($stage)
            return $this->sizes[$stage]["item"]??$default;
        return $this->sizes["item"]??$default;
    }

    /**
     * Get Label Size on Stage
     * @param string $stage
     * @return string
     */
    public function labelSize($stage = ""){
        $default = $this->quickdata->getPageSettings()->getControlSize($stage.".label-size");
        if($stage)
            return $this->sizes[$stage]["label-size"]??$default;
        return $this->sizes["label-size"]??$default;
    }

    /**
     * Get Input Size on Stage
     * @param string $stage
     * @return string
     */
    public function inputSize($stage = ""){
        $default = $this->quickdata->getPageSettings()->getControlSize($stage.".input-size");
        if($stage)
            return $this->sizes[$stage]["input-size"]??$default;
        return $this->sizes["input-size"]??$default;
    }

    /**
     * Get css class
     * @return string
     */
    public function getClass(){
        $class = $this->attributes["class"]??"";
        return config("quick.template.controlcss")." $class";
    }
}