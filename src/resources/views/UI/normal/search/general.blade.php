@component("quick::general.component.row_template", ["column"=>$column,"stage"=>"search"])
    @slot("label")
        {{qt($column->displayName)}}
    @endslot
    @slot("input")
        @component(config("quick.template.form"))
            @slot("input")
            <input type="text" name="{{$column->getRequestName()}}" value="{{ request()->get($column->getRequestName()) }}" class="{{$column->getClass()}}">
            @endslot
        @endcomponent
    @endslot
@endcomponent