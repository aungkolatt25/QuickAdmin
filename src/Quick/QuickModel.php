<?php

namespace Quick\Quick;

use Illuminate\Database\Eloquent\Model;
use Quick\Quick\QuickData;
use Arr;

class QuickModel extends Model
{
    public $quickdata;

    public function __construct($arr = []){
        parent::__construct($arr);
        if(session()->get("table", [])){
            $data = session()->get("table");
            session()->forget("table");
            $this->makeModel(QuickData::getLoaded($data));
        }
    }
    //
    public static function make(QuickData $data){
        $modelName = Arr::get($data->getData(),"model","\Quick\Quick\QuickModel()");
        try{
            eval('$model = new '.$modelName.';');
            $model->makeModel($data);
        }
        catch(\Exception $e){
            throw $e;
        }
        return $model;
    }

    public function makeModel(QuickData $data){
        $this->quickdata = $data;
        $this->table = $data->getTable();
        foreach($data->getOptions() as $key => $value){
            $this->{$key} = $value;
        }

        foreach(Arr::get($data, "relations", []) as $relation){
            $this->makeRelation($relation["related"], $relation["type"],$relation["localKey"], $relation["foreignKey"]);
        }
    }

    public function getQuickData(){
        $this->quickdata = $this->quickdata?$this->quickdata:QuickData::get($this->table);
        return $this->quickdata;
    }

    public function getQuickQuery(){
        session()->forget("table");
        session()->put("table", $this->getQuickData()->getData());
        $builder = $this->query();
        return $builder;
    }

    public function relation($relationid, QuickData $quickdata = null){
        $relation = $this->getQuickData()->getRelation($relationid);
        $relation_data = QuickData::get($relation->related);
        $relation_model =  $relation_data->getModel();
        $quick_foreign_key = Arr::get($relation, "foreignKey", $relation_model->getQuickForeignKey());
        $quick_local_key = Arr::get($relation, "localKey", $relation_model->getKeyName());
        
        
        switch($relation->type){
            case "belongsTo":
                return $this->belongsTo($relationid, $quick_foreign_key, $quick_local_key, $relation->related);
            case "hasOne":
                return $this->hasOne($relationid, $quick_foreign_key, $quick_local_key);
            case "hasMany":
                return $this->hasMany($relationid, $quick_foreign_key, $quick_local_key);
            case "belongsToMany":
                $foreignPivotKey = Arr::get($relation, "foreignPivotKey", $this->getQuickForeignKey());
                $relatedPivotKey = Arr::get($relation, "relatedPivotKey", $relation_model->getQuickForeignKey());
                $primaryKey = Arr::get($relation, "primaryKey", $this->getKeyName());
                $relatedKey = Arr::get($relation, "relatedKey", $relation_model->getKeyName());
                $table = Arr::get($relation, "table",$this->joiningTable($relation_model, $this));
                return $this->belongsToMany($relationid, $table, $foreignPivotKey,$relatedPivotKey,$primaryKey, $relatedKey);
        }
    }

    public function getQuickForeignKey()
    {
        return $this->getTable().'_'.$this->getKeyName();
    }

    public function joined($builder, $column){
        static $joined = [];
        //If Not joined,Join
        if(isset($joined[$column["relation"]]))
            return $joined[$column["$relation"]];

        $relationModel = $this->relation($column["relation"])->getRelated();
        $relationTable = $relationModel->getTable();
        $relation_config = $column->getRelation();

        return $builder->leftjoin(
            $relationModel->getTable(), 
            $relationModel->getTable().".".Arr::get($relation_config, "foreignKey", $this->getQuickForeignKey()),
            $this->getTable().".".Arr::get($relation_config, "localKey", $this->getKeyName())
        );
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        foreach(config("quick.query.global",[]) as $key=>$scope)
            static::addGlobalScope($key,$scope);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try{
            $result = $this->getQuickDynamicMethod($method, $parameters);
            if($result)
                return $result;
            return parent::__call($method, $parameters);
        }
        catch(\Exception $e){
            throw $e;
        }
    }

    public function getQuickDynamicMethod($method, $parameters){
        if($this->getQuickData()->hasRelation($method))
            return $this->relation($method);
    }

    public function newInstance($attributes = [], $exists = false)
    {
        if($this->quickdata){
            $model = Self::make($this->quickdata);
            $model->exists = $exists;
            return $model;
        }
        
        return parent::newInstance($attributes, $exists);
    }

    /**
     * Begin querying a model with eager loading.
     *
     * @param  array|string  $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function with($relations)
    {
        return static::query()->with(
            is_string($relations) ? func_get_args() : $relations
        );
    }

    protected function newRelatedInstance($class)
    {
        $relation = $this->getQuickData()->getRelation($class);
        $relation_data = QuickData::get($relation->related);
        $relation_model =  $relation_data->getModel();
        return $relation_model;
    }
}
