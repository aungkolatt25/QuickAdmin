@component("quick::general.component.row_template", compact("column", "stage"))
    @slot("label")
        {{qt($column->displayName)}}
    @endslot
    @slot("input")
        @component(config("quick.template.form"))
            @slot("input")
                <input type="{{$column->getInputType()}}" name="{{$column->getRequestNameForMany()}}" class="{{$column->getClass()}}" value="{{ old($column->getRequestName().".$i",$column->getValue($data)) }}">
            @endslot
        @endcomponent
    @endslot
@endcomponent