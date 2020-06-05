<select name="{{$column->getName()}}">
    <option value="">All</option>
    @foreach($column->getRelation()->getModel()->get() as $model)
    <option value="{{ $model->{$column->rkey} }}">
        {{ $column->getValue($model,true) }}
    </option>
    @endforeach
</select>