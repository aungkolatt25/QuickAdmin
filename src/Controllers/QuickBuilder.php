<?php

namespace Quick\Controllers;

use Illuminate\Http\Request;
use Quick\Quick\QuickData;
use Quick\Quick\QuickModel;
use DB;
use Arr;
use JsValidator;
use Validator;

class QuickBuilder extends \App\Http\Controllers\Controller
{
    protected $quickdata;
    protected $model;

    public function __construct(){
        $this->quickdata = QuickData::get($this->getFileName());
        $this->model = $this->quickdata->getModel();
    }

    public function getFileName(){
        return request()->segment(2);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $builder = $this->getListData($request);
        $quickdata = $this->quickdata;
        $datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        return view("quick::general.list", compact("datas", "quickdata"));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $builder = $this->buildSearch($this->getListData($request));
        $quickdata = $this->quickdata;
        $datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        return view("quick::general.list", compact("datas", "quickdata"));
    }

    public function buildSearch($builder){
        $joined = [];
        foreach($this->quickdata->getVisibleColumns("search") as $column){
            //Join $
            $builder = $builder->when($column->getValueAccessable(), function($builder, $value) use($column){
                if($column->isRelationType()){
                    return $builder->whereHas($column["relation"], function($builder) use($column, $value){
                        return $column->bindSearchLogic($builder, '=', $value);
                    });
                }
                return $column->bindSearchLogic($builder, '=' , $value);
            });
        }
        
        return $builder;
    }

    /** 
     * @return Builder
     * */
    public function getListData(Request $request){
        return $this->model;
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

    public function createRule(){
        $columns = $this->quickdata->getVisibleColumns("create");
        $rules = $columns->flatMap(function($column){
            return [$column->getRequestName()=>$column->getRules("create")];
        });
        
        return $rules->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->createRule());
        $columns = $this->quickdata->getVisibleColumns("create");
        foreach($columns as $column){
            if(
                $column->isRelationType() && 
                ( $column->getRelation()->type == "belongsTo" || $column->getRelation()->type == "belongsToMany")
            )
                continue;
            if(Arr::get($column->options??[],"relatedDatas", false))
                continue;
            $requestValue = $column->getValueAccessable();
            $this->model->{$column->getName()} = $requestValue;
        }
        DB::beginTransaction();
        try{
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $name, $id)
    {
        //$builder = $this->getListData($request);
        $quickdata = $this->quickdata;
        $data = $this->model->find($id);
        $jsValidator = JsValidator::make($this->editRule());
        //$datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        return view("quick::general.edit", compact( "quickdata", "data","jsValidator"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $name, $id)
    {
        $validator = Validator::make($request->all(), $this->editRule());
        if ($validator->fails()) { return back()->withErrors($validator->errors()); }
        
        $columns = $this->quickdata->getVisibleColumns("edit");
        $model = $this->model->find($id);
        foreach($columns as $column){
            if($column->isRelationType() && $column->getRelation()->type == "belongsTo")
                continue;
            if(Arr::get($column->options??[],"relatedDatas", false))
                continue;
            $requestValue = $column->getValueAccessable();
            $model->{$column->getRname()} = $requestValue;
        }
        $model->save();
        return redirect(qurl($this->quickdata->file));
    }

    public function editRule(){
        $columns = $this->quickdata->getVisibleColumns("create");
        $rules = $columns->flatMap(function($column){
            return [$column->getRequestName()=>$column->getRules("create")];
        });
        
        return $rules->toArray();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $name, $id)
    {
        $this->model->findOrFail($id)->delete();
        return back();
    }
}
