<?php
namespace Quick\Quick;
use Quick\Quick\Traits\Attribute;
use Quick\Quick\Traits\ArrayImplement;
use Arr;

class PageSettings implements \ArrayAccess{
    use Attribute;
    use ArrayImplement;

    private $quickdata;
    private $relation;
    private $attributes = [];
    private $many = false;
    private $settings;

    public function __construct($settings = []){
        $default = $this->getDefaultSettings();
        $this->settings = $this->bindWithDefaultSettings($default, $settings);
    }

    private function bindWithDefaultSettings($default, $settings){
        if($settings)
            foreach($default as $key => $value){
                if(($settings[$key]??false) && is_array($settings[$key])){
                    $default[$key] = $this->bindWithDefaultSettings($default[$key], $settings[$key]);
                }
                else if($settings[$key]??false){
                    $default[$key] = $settings[$key];
                }
            }
        return $default;
    }

    public function getDefaultSettings(){
        return [
            "links"=>[
                "create"=>qurl(request()->segment(2)."/create")
            ],
            "controlSize"=>[
                "create"=>[
                    "item"=>"col-sm-12",
                    "label-size"=>"col-sm-2",
                    "input-size"=>"col-sm-10"
                ],
                "create-many"=>[
                    "item"=>"col-sm-4",
                    "label-size"=>"col-sm-12",
                    "input-size"=>"col-sm-12"
                ],
                "search"=>[
                    "item"=>"col-sm-6",
                    "label-size"=>"col-sm-12",
                    "input-size"=>"col-sm-12"
                ],
                "edit"=>[
                    "item"=>"col-sm-12",
                    "label-size"=>"col-sm-4",
                    "input-size"=>"col-sm-8"
                ],
            ]
        ];
    }

    public function getControlSize($index){
        return Arr::get($this->settings, "controlSize.".$index);
    }

    public function getLink($url){
        return Arr::get($this->settings, "links.".$url);
    }
}   