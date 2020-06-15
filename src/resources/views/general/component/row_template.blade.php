<div class="form-group row {{$column->size($stage)}}">
    <label class="{{$column->labelSize($stage)}} form-control-label text-xs-right">
        {{$label}}
    </label>
    <div class="{{$column->inputSize($stage)}}">
        {{$input}}
        <div class="customername-error error-msg">
            
        </div>
    </div>
</div>