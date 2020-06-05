<select name="{{$column->getName()}}" class="{{$column-getClass()}}">
    @foreach($column->getRelation()->getModel()->get() as $model)
    <option value="{{ $model->{$column->rkey} }}">
        {{ $model->{$column->rname} }}
    </option>
    @endforeach
</select>