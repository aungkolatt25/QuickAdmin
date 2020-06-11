<?php

Route::group(["prefix"=>config("quick.prefix"), 'middleware' => 'web'], function(){
    $methodCall = function($method, $parameters = array()){
        $controller = "Default";
        if(request()->segment("2")){
            $quickdata = \Quick\Quick\QuickData::get(request()->segment("2"));
            $controller = $quickdata->getBindedController();


            $r = new ReflectionMethod($controller, $method);
            $params = [];
            $i = 0;
            foreach ($r->getParameters() as $param) {
                //$param is an instance of ReflectionParameter
                if(!$param->getClass()){
                    $params[$param->getName()] = $parameters[$i++];
                }
            }
        }
        session(["test"=>"t"]);
        return App::call("$controller@$method", $params);
    };
    
    Route::get("/{everything}", function() use($methodCall) {
        return $methodCall("index");
    });

    Route::get("/{everything}/create", function() use($methodCall) {
        return $methodCall("create");
    });


    Route::get("/{everything}/create-many", function() use($methodCall) {
        return $methodCall("createMany");
    });

    Route::post("/{everything}/create-many", function() use($methodCall) {
        return $methodCall("storeMany");
    });

    Route::post("/{everything}/create", function() use($methodCall) {
        return $methodCall("store");
    });

    Route::get("/{everything}/edit/{id}", function($everything, $id) use($methodCall) {
        return $methodCall("edit", [$everything,$id]);
    });

    Route::post("/{everything}/edit/{id}", function($everything, $id) use($methodCall) {
        return $methodCall("update", [$everything,$id]);
    });

    Route::get("/{everything}/delete/{id}", function($everything, $id) use($methodCall) {
        return $methodCall("destroy", [$everything,$id]);
    });

    Route::get("/{everything}/search", function() use($methodCall) {
        return $methodCall("search");
    });
});
