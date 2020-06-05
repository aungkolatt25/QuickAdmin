<?php
namespace Quick\Quick;
use Quick\Quick\Traits\Attribute;
use Quick\Quick\Traits\ArrayImplement;
use Quick\Quick\QuickData;

class Relation implements \ArrayAccess{
    use Attribute;
    use ArrayImplement;

    public $attributes;
    
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($attribute){
        return $this->getAttribute($attribute);
    }

    public function getModel(){
        return QuickData::get($this->related)->getModel();
    }

    public function save($model, $mix){
        switch($this->type){
            case "hasOne":
            case "hasMany":
                $model->relation($this->id)->save($mix);
                break;
            case "belongsTo":
                $model->relation($this->id)->associate($mix);
                break;
            case "belongsToMany":
                $model->relation($this->id)->sync($mix);
                break;
        }
    }
}