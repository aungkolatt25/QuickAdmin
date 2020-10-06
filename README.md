# QuickAdmin
    Quick Building Admin Data Managment.
    # Define Common Form Design
    # Define Common List Design
    # Four Stage
        List, Create, Edit, Search

# Building Builder
    You can bind controller and model. You must be inherited from QuickBuilder and QuickModel.
    One Builder represents ListPage,CreatePage,EditPage.
    There is declare which column are included.
    Builder File must be exist under app\Builder.
    After Creation of Build, you can get simple 4stage(CRUD);
    see documentation [here](./Builder.md)
# QuickBuilder Controller
    getFileName() - Loading Related File.Default load from route segment 2
    getListData(Request $request) - Return Builder for listing data.

# Default QuickBuilder Logic for create
    store(Request $request) - action for store data
    createRule() - Return rule for create from file.
    beforeSave($model) - to action on model before save
    saveLogic($model) - to save model logic
    prepareRelateionStore($relationObj, $currentModel, $relatedData) - Preparing for some relation data
        beforeSaveRelated($relatedData, $currentModelId)
        saveLogicRelated($relationObj, $currentModel, $relatedData)
        afterSaveRelated($relatedData, $currentModelId)
    afterSave($model)
# Default QuickBuilder Logic for edit
    editRule() - Return rule array from edit from file

# Column
    name attribute for
    setValue accept callback function to override original value
    visible attribute to define which column are need to show in what stage.
        there is four stage list,create,edit,search

# Column Function
    getValueAccessable
        This method is use to change user data to database assignable data
    getValueUserable
        This method is use to change database data to userdata
    bindSearchLogic
        This method is use to bind search logic.If you build new column type, you should bind your search logic.

# Column Type
    integer,date,text,select

# Column Relation
    Defining Name at relation attribute
    
# Relation
# Relation Type
    hasOne, hasMany, BelongsTo, BelongsToMany
    Depends of Type, there is require attriubtes are change.