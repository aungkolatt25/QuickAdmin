@component("quick::general.component.row_template", compact("column"))
    @slot("label")
        {{qt($column->displayName)}}
    @endslot
    @slot("input")
        @component(config("quick.template.form"))
            @slot("input")
                <input type="{{$column->getInputType()}}" name="{{$column->getName()}}" class="{{$column->getClass()}}" value="{{ $column->getValue($data) }}">
            @endslot
        @endcomponent
    @endslot
@endcomponent