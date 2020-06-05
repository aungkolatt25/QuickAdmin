<div class="items-search">
        <div class="input-group">
            <input type="text" class="form-control boxed rounded-s" placeholder="Search for..." value="" name="search" data-toggle='modal' data-target='#search-modal'>
            <span class="input-group-btn">
                <button class="btn btn-secondary rounded-s" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>

    <div class="modal fade" id="search-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{qurl($quickdata->file.'/search')}}" method="get">
                        <div class="row">
                            @foreach($quickdata->getVisibleColumns("search") as $column)
                            <div class="col-md-6">
                                    <label class="text-xs-right">
                                        {{qt($column->displayName)}}
                                    </label>
                                    <div class="form-group">
                                        {{$column->getUI("search", null)}}
                                    </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>