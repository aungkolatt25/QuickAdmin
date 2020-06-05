@foreach($quickdata->getVisibleColumns("edit") as $column)
    @component("quick::general.component.row_template")
        @slot("label")
            {{qt($column->displayName)}}
        @endslot
        @slot("input")
            @component(config("quick.template.form"))
                @slot("input")
                    {{$column->getUI("edit", $data)}}
                @endslot
            @endcomponent
        @endslot
    @endcomponent
@endforeach