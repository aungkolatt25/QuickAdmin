@php
    $i=-1;
    $columns = $quickdata->getVisibleColumns("create");
@endphp
<div>
<script id="template">
    <div class="group">
        <span class="badge badge-primary add pull-right">Add</span>
        <div class="row">
            @foreach($columns as $column)
                {{$column->getUI("create-many", compact("data", "i", "stage"))}}
            @endforeach
        </div>
    </div>
</script>
@php
    $i=0;
    $total = count(old($columns->first()->getRequestAcessName(), [""]));
@endphp
@for($i = 0; $i < $total; $i++)
    <div class="group">
        @if($i == $total-1)
            <span class="badge badge-primary pull-right add">Add</span>
        @else
            <span class="badge badge-danger pull-right sub">Sub</span>
        @endif
        <div class="row">
            @foreach($columns as $column)
                {{$column->getUI("create-many", compact("data", "i", "stage"))}}
            @endforeach
        </div>
    </div>
@endfor
</div>
@push("script")
<script>
    $("body").on("click", ".add", function(){
        $(".group span.add").removeClass("add badge-primary").addClass("sub badge-danger").html("Sub");
        $dom = $($("#template").html());
        $(this).parent().parent().append($dom);
        $('.select2').select2();
    });
</script>
@endpush