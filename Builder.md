# Builder
    Builder represent the model and pages.
    
## Builder Format
    return [
        "table"=>"persons",
        "sigular"=>"Person",
        "plupal"=>"People",
        /**option for extend action or override the default actions**/
        "controller"=>"\App\Http\Controllers\Person",
        /**model settings**/
        "options"=>[
            "primaryKey"=>"PersonID",
            "timestamps"=>false
        ],
        "columns"=> [
            [
                "name"=>"PersonID",
                "displayName"=>"Id",
                "type"=>"integer",
                "visible"=>[],
                "options"=>""
            ],
            [
                /**name must be same with database column name**/
                "name"=>"name",
                /**Show as**/
                "displayName"=>"Name",
                /**different type have diffent attribute**/
                "type"=>"text",
                /**visible control for stage**/
                "visible"=>["list","create", "edit", "search"],
            ],
            [
                "name"=>"location_id",
                "rname"=>"Location",
                "rkey"=>"LocationID",
                "displayName"=>"Location",
                "type"=>"select",
                "relation"=>"relLocation",
                "visible"=>["list","create", "edit", "search"],
            ],
        ],
        /**relation with current builder or one/many of current builder attributes**/
        "relations"=>[
            [
                "id"=>"relLocation",/**relation unique name**/
                "type"=>"belongsTo",/**relation type**/
                "related"=>"location",/**relation to file**/
                "localKey"=> "LocationID",
                "foreignKey"=> "LocationID"
            ],
        ]
    ];
