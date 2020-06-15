@extends("layout.master")
@section("content")
@php
$stage= "edit";
@endphp
<form action="{{qurl($quickdata->file.'/edit/'.$data->getKey())}}" method="post">
{{csrf_field()}}
    <div class="title-block">
        <h3 class="title">
            {{qt($quickdata->getTitle())}} <span class="sparkline bar" data-type="bar"></span>
                </h3>
    </div>
    <div class="card card-block">
        {{QuickComponent($quickdata, "edit", compact("data","stage"))}}
        <div class="form-group row">
            <div class="col-sm-10 col-sm-offset-2">
                <input type="submit" value="Submit" lass="btn btn-primary">
            </div>
        </div>
    </div>
</form>
@include("quick::general.component.errors")
@endsection
@push("script")

@endpush