@foreach($quickdata->getVisibleColumns("edit") as $column)
    {{$column->getUI("edit", compact("data", "stage"))}}
@endforeach