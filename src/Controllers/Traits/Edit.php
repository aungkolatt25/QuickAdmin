<?php
namespace Quick\Controllers\Traits;
use Request;
use DB;

trait Edit{

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
        $validatedResult = $this->validateProcess($request, $this->editRule());
        if($validatedResult !== true)
            return $validatedResult;
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
}