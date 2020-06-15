<?php
namespace Quick\Controllers\Traits;
use DB;
use Request;
use JsValidator;
use Arr;

trait Create{
    private $many = false;
    
    public function createRule(){
        $columns = $this->quickdata->getVisibleColumns("create");
        $rules = $columns->flatMap(function($column){
            return [$column->getValidationRuleName()=>$column->getRules("create")];
        });
        
        return $rules->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$builder = $this->getListData($request);
        $quickdata = $this->quickdata;
        $data = $this->model;
        foreach($quickdata->getRelations() as $relation){
            if($relation->type == "belongsTo"){
                $data->{$relation->foreignKey} = request()->get($relation->foreignKey);
            }
        }
        //$datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        $jsValidator = JsValidator::make($this->createRule());
        return view("quick::general.create", compact( "quickdata","data", "jsValidator"));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedResult = $this->validateProcess(request(), $this->createRule());
        if($validatedResult !== true)
            return $validatedResult;

        
        DB::beginTransaction();
        try{
            $columns = $this->assignableColumns("create");
            $this->prepareSave($this->model, $columns);
            $this->beforeSave($this->model);
            $this->saveLogic($this->model);
            $this->prepareRelateionStore();
            $this->afterSave($this->model);
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
            dd($e);
            return back();
        }
        return redirect(qurl($this->quickdata->file));
    }
    public function saveLogic($model){
        return $this->model->save();
    }

    // Currently Parent Should Be Create First
    public function prepareRelateionStore(){
        //Load Relation Column which are not belongsTo
        $relationColumns = collect($this->quickdata->getVisibleColumns("create"))->filter(function($column){
            return $column->isRelationType() && $column->getRelation()->type != "belongsTo";
        });

        $relationStorages = [];
        $relations = $relationColumns->map(function($column){
            return $column->getRelation();
        });

        foreach($relationColumns as $column){
            $requestValue = $column->getValueAccessable();
            if(is_array($requestValue)){
                foreach($requestValue as $key => $value){
                    if(!isset($relationStorages[$column["relation"][$key]]))
                        $relationStorages[$column["relation"]][$key] = $this->model->relation($column["relation"])->getRelated();
                    ($relationStorages[$column["relation"]][$key])->{$column->getName()} = $value;
                }
            }
            else{
                if(!isset($relationStorages[$column["relation"]]))
                    $relationStorages[$column["relation"]] = $this->model->relation($column["relation"])->getRelated();
                ($relationStorages[$column["relation"]])->{$column->getName()} = $requestValue;
            }
        }
        $relations->map(function($relation) use($relationStorages){
            $this->beforeSaveRelated($relationStorages[$relation->id], $relation->id);
            $this->saveLogicRelated($relation, $this->model, $relationStorages[$relation->id]);
            $this->afterSaveRelated($relationStorages[$relation->id], $relation->id);
        });
        /*
        foreach($relationStorages as $key => $relationObjOrArray){
            $this->beforeSaveRelated($relationObjOrArray, $key);
            if(!is_array($relationObjOrArray))
                $this->model->relation($key)->save($relationObjOrArray);
            else
                $this->model->relation($key)->saveMany($relationObjOrArray);
            $this->beforeSaveRelated($relationObjOrArray, $key);
            
        }*/
    }

    public function saveLogicRelated($relationObj, $model, $relationdata){
        $relationObj->save($model, $relationdata);
    }

    public function prepareSave($model, $columns){
        foreach($columns as $column){
            $column->setManyIndex($this->many);
            $requestValue = $column->getValueAccessable();
            $model->{$column->getName()} = $requestValue;
        }
    }

    public function beforeSave($model){
        return $model;
    }

    public function afterSave($model){
        return $model;
    }

    public function beforeSaveRelated($model, $relation){
        return $model;
    }

    public function afterSaveRelated($model, $relation){
        return $model;
    }
  
    public function createManyRule(){
        $columns = $this->quickdata->getVisibleColumns("create");
        $rules = $columns->flatMap(function($column){
            return [$column->getValidationRuleNameForMany()=>$column->getRules("create")];
        });
        
        return $rules->toArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMany()
    {
        //$builder = $this->getListData($request);
        $quickdata = $this->quickdata;
        $data = $this->model;
        foreach($quickdata->getRelations() as $relation){
            if($relation->type == "belongsTo"){
                $data->{$relation->foreignKey} = request()->get($relation->foreignKey);
            }
        }
        //$datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        $jsValidator = JsValidator::make($this->createManyRule());
        return view("quick::general.create-many", compact( "quickdata","data", "jsValidator"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMany(Request $request)
    {
        $validatedResult = $this->validateProcess(request(), $this->createManyRule());
        if($validatedResult !== true)
            return $validatedResult;

        DB::beginTransaction();
        try{
            $columns = $this->assignableColumns("create");
            $length = count(request($columns->first()->getRequestName()));
            for($i = 0; $i < $length; $i++){
                $this->model = $this->quickdata->getModel();
                $this->many = $i;
                $this->prepareSave($this->model, $columns);
                $this->beforeSave($this->model);
                $this->saveLogic($this->model);
                $this->prepareRelateionStore();
                $this->afterSave($this->model);
            }
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollBack();
            dd($e);
            return back();
        }
        return redirect(qurl($this->quickdata->file));
        
    }
}