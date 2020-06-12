@php
    $i=0;
    $columns = $quickdata->getVisibleColumns("create");
    $total = count(old($columns->first()->getRequestName(), [""]));
@endphp
<div>
@for($i = 0; $i < $total; $i++)
    <div >
    @if($i == $total-1)
        <span class="add">++++</span>
    @else
        <span class="sub">----</span>
    @endif
    @foreach($columns as $column)
        {{$column->getUI("create-many", compact("data", "i"))}}
    @endforeach
    </div>
@endfor
</div>
@push("script")
<script>
    $("body").on("click", ".add", function(){
        $dom = $(this).parent().html();
        $(this).parent().parent().append($dom);
    });
</script>
@endpush