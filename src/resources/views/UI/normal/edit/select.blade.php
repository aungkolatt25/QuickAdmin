@component("quick::general.component.row_template", compact("column"))
    @slot("label")
        {{qt($column->displayName)}}
    @endslot
    @slot("input")
        @component(config("quick.template.form"))
            @slot("input")
            <select name="{{$column->getRequestName()}}" class="{{$column->getClass()}}">
                @foreach($column->getRelation()->getModel()->get() as $model)
                <option value="{{ $model->{$column->rkey} }}">
                    {{ $column->getValue($model,true)}}
                </option>
                @endforeach
            </select>
            @endslot
        @endcomponent
    @endslot
@endcomponent