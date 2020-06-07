# QuickAdmin
    Quick Building Admin Data Managment.
    # Define Common Form Design
    # Define Common List Design
    # Four Stage
        List, Create, Edit, Search

# Builing Builder
    You can bind controller and model. You must be inherited from QuickBuilder and QuickModel.
    One Builder represents ListPage,CreatePage,EditPage.
    There is declare which column are inclued.

# Default QuickBuilder Logic for create
# Default QuickBuilder Logic for edit

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