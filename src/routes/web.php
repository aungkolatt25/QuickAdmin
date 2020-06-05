<?php

Route::group(["prefix"=>config("quick.prefix")], function(){
    if(request()->segment("2")){
        $quickdata = \Quick\Quick\QuickData::get(request()->segment("2"));
        $controller = $quickdata->getBindedController();
        Route::get("/{everything}", "$controller@index");
        Route::get("/{everything}/create", "$controller@create");
        Route::post("/{everything}/create", "$controller@store");
        Route::get("/{everything}/edit/{id}", "$controller@edit");
        Route::post("/{everything}/edit/{id}", "$controller@update");
        Route::get("/{everything}/delete/{id}", "$controller@destroy");
        Route::get("/{everything}/search", "$controller@search");
    }
});