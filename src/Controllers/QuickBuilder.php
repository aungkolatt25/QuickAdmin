<?php

namespace Quick\Controllers;

use Illuminate\Http\Request;
use Quick\Quick\QuickData;
use Quick\Quick\QuickModel;
use DB;
use Arr;
use JsValidator;
use Validator;
use Quick\Controllers\Traits\Create;
use Quick\Controllers\Traits\Edit;

class QuickBuilder extends \App\Http\Controllers\Controller
{
    use Create;
    use Edit;

    protected $quickdata;
    protected $model;

    public function __construct(){
        $this->quickdata = QuickData::get($this->getFileName());
        $this->model = $this->quickdata->getModel();
    }

    /**
     * Get Filename
     */
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
        $totalData = $this->getTotal($builder);
        $datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        return view("quick::general.list", compact("datas", "quickdata", "totalData"));
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
        $totalData = $this->getTotal($builder);
        $datas = $quickdata->isPaginate()?$builder->paginate():$builder->get();
        return view("quick::general.list", compact("datas", "quickdata", "totalData"));
    }

    /** 
     * Add Search Logic to Builder
     * @return Builder
     * */
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
     * Get Total Data
     */
    public function getTotal($builder){
        $totalBuilder = clone($builder);
        foreach($this->quickdata->getVisibleColumns("list") as $column){
            $callback = $column->total;
            $name = $column->getName();

            if(!($callback??true))
                continue;
                
            if($callback){
                $totalBuilder = $totalBuilder->addSelect(DB::raw($callback." as total_$name"));
                continue;
            }
            
            if($column->type == "integer" && !$column->isRelationType){
                $totalBuilder = $totalBuilder->addSelect(DB::raw("sum($name) as total_".$name));
            }
        }
        //dd($totalBuilder->toSql());
        return $totalBuilder->first();
    }

    /** 
     * Build for List Data 
     * @return Builder
     * */
    public function getListData(Request $request){
        return $this->model;
    }

    /**
     * Validate 
     * @return True/Response
     */
    private function validateProcess($request, $rules){
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) { 
            return back()->withErrors($validator->errors())->withInput($request->all()); 
        }
        return true;
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

    /**
     * Get Assignable Column
     * @return Column
     */
    protected function assignableColumns($stage){
        $columns = $this->quickdata->getVisibleColumns("create");
        return $columns->filter(function($column){
            if(
                $column->isRelationType() && 
                ( $column->getRelation()->type == "belongsTo" || $column->getRelation()->type == "belongsToMany")
            )
                return true;
            if(Arr::get($column->options??[],"relatedDatas", false))
                return false;
            return true;
        });
    }
}
