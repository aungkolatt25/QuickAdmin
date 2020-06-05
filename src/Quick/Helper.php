<?php
    function qt($value){
        $t = trans("quick::quick.$value");
        return $t=="quick::quick.$value"?$value:$t;
    }

    function qurl($url){
        return 	url(trim(config("quick.prefix"),"/")."/".trim($url,"/"));
    }

    function qhtml($string){
        return new Illuminate\Support\HtmlString($string);
    }

    function QuickComponent($quickdata, $view, $datas = []){
        //Load Custom
        $hookedView = $quickdata->file.".$view";

        //Load Custom or General
        $view = view()->exists($hookedView)?$hookedView:"quick::general.component.$view";
        $datas["quickdata"] = $quickdata;
        return view($view, $datas);
    }