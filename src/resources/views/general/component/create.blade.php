@foreach($quickdata->getVisibleColumns("create") as $column)
    {{$column->getUI("create", compact("data", "stage"))}}
@endforeach