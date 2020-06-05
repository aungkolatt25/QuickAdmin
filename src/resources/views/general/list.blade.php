@extends(config("quick.template.layout"))
@section("style")
<style type="text/css">
	.item-col{
		text-align: left;
		margin-left: 0 !important;
		margin-right: auto;
		-ms-flex-preferred-size: 0;
		flex-basis: 0;
		-webkit-box-flex: 9;
		-ms-flex-positive: 9;
		flex-grow: 9 !important;
	}
</style>
@endsection

@section("script")
<script type="text/javascript">


	title = "{{$quickdata->getTitle()}}";
	header = ["{{<?php //echo implode('}}","{{', $colheader); ?>}}"];
	key = ["<?php //echo implode('","', array_keys($colheader));?>"];
	length = 100/key.length;
	widths = [];
	file = title+".pdf";
	key.forEach(function(e){
		widths.push(length+"%");
	});
		
		function print(e){
			var docDefinition = {
				content: [
				{text:title,alignment:"center",fontweight:"bold",fontSize: 17, bold: true},
				{
					style: 'table',
		      		table: {
		        	// headers are automatically repeated if the table spans over multiple pages
		        	// you can declare how many rows should be treated as headers
			        	headerRows: 1,
			        	widths: widths,
			        	body: toArray(e)
		      		}
		    	}],
		    	styles: {
		    		table:{
						margin: [5, 5, 5, 5]
		    		}
				}	
			};
			pdfMake.createPdf(docDefinition).download(file);
		}

		function toArray(data){
			var a = [header];
			data.forEach(function(e){
				var v=[];
				key.forEach(function(s){
					if(e[s+"_more"] != undefined)
						v.push(e[s+"_more"]);
					else
						v.push(e[s] != undefined?e[s]:"");
				});
				a.push(v);v=[];
			});
			console.log(a);
			return a;
		}

		$(".print").click(function() {
			url = $(this).data("print");
			alert(url);
			$.ajax({
				url:url,
				type:'json',
				success: function(e){
					print(JSON.parse(e));
				}
			});
		});
</script>
@endsection

@section(config("quick.template.content"))
<div class="title-search-block">

	<div class="title-block">
		<div class="row">

			<div class="col-md-6">
				<h3 class="title">
                    {{qt($quickdata->getTitle())}}
					<a href="{{$quickdata->getCreatedLink()}}" class="btn btn-primary btn-sm rounded-s">
						Add New
					</a>
					<div class="action dropdown">
						<button class="btn  btn-sm rounded-s btn-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							More actions...
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenu1">
							<a class="dropdown-item" href="#"><i class="fa fa-pencil-square-o icon"></i>Mark as a draft</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#confirm-modal"><i class="fa fa-close icon"></i>Delete</a>
						</div>
					</div>
				</h3>
				<p class="title-description">
					List of sample items - e.g. books, movies, events, etc...
				</p>
			</div>

		</div>
	</div>
	@include("quick::general/component/search")
</div>
{{QuickComponent($quickdata, "before_list")}}
{{QuickComponent($quickdata, "list",compact("datas"))}}
{{QuickComponent($quickdata, "after_list")}}
@endsection