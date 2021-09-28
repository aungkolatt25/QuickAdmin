<?php
namespace Quick\Quick;
use Quick\Quick\Traits\Attribute;
use Quick\Quick\Traits\ArrayImplement;
use Quick\Quick\QuickData;

/**
 * Relation
 * To represent the relation on column
 */
class Relation implements \ArrayAccess{
    use Attribute;
    use ArrayImplement;

    public $attributes;
    
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }
    
    /**
     * accessing attribute value as object's attribute
     */
    public function __get($attribute){
        return $this->getAttribute($attribute);
    }

    /**
     * Get Related Model
     * @return Model
     */
    public function getModel(){
        return QuickData::get($this->related)->getModel();
    }

    /**
     * Saving Logic depends on Type
     * @param Model $model
     * @param mix $mix
     * @return mix
     */
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
                $model->relation($this->id)->sync(
                    collect($mix)->map(function($value){
                        return $value->getKey();
                    })->values()
                );
                break;
        }
    }
}