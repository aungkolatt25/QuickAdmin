@foreach($quickdata->getVisibleColumns("create") as $column)
    @component("quick::general.component.row_template")
        @slot("label")
            {{qt($column->displayName)}}
        @endslot
        @slot("input")
            @component(config("quick.template.form"))
                @slot("input")
                    {{$column->getUI("create", null)}}
                @endslot
            @endcomponent
        @endslot
    @endcomponent
@endforeach