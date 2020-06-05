<select name="{{$column->getName()}}" class="{{$column->getClass()}}">
    <option value="">All</option>
    @foreach($column->getRelation()->getModel()->get() as $model)
    <option value="{{ $model->{$column->rkey} }}">
        {{ $column->getValue($model,true) }}
    </option>
    @endforeach
</select>