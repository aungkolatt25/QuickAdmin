<form action="{{qurl($quickdata->file.'/search')}}" method="get">
    @foreach($quickdata->getVisibleColumns("search") as $column)
        {{ $column->getUI("search",$column, null) }}
    @endforeach
    <input type="submit" value="Submit">
</form>