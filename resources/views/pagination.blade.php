<!DOCTYPE html>
<html>
<head>
	 <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Custom Pagination</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
		<script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
		<script src="{{asset('js/bootstrap.min.js')}}"></script>

</head>
<body>
	<div class="jumbotron text-center">
  <h1>My First Bootstrap Page</h1>
  <p>Resize this responsive page to see the effect!</p> 
</div>

<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <h3>Column 1</h3>
      <p>Lorem ipsum dolor..</p>
      <p>Ut enim ad..</p>
    </div>
    <div class="col-sm-4">
      <h3>Column 2</h3>
      <p>Lorem ipsum dolor..</p>
      <p>Ut enim ad..</p>
    </div>
    <div class="col-sm-4">
      <h3>Column 3</h3> 
      <p>Lorem ipsum dolor..</p>
      <p>Ut enim ad..</p>
    </div>
  </div>
  <div class="clear-fix"></div>
  <div class="row">
  	<div class="col-sm-4 col-md-offset-4">Pagination Links go here..</div>
  </div>
  <div class="row">
  	<div id="pagination-links" class="col-sm-4 col-md-offset-4"></div>
  </div>
</div>

<!-- pagination script goes down here... -->
<script type="text/javascript">

$(document).ready(function(){

	//script for numbered pagination links////////////
	$("#pagination-links a:nth-child(2)").addClass("active");

	$("#prev").on("click",function(){




	})

	/*$("#next").on("click",function(){

		
		
	})*/

	$(".badge").on("click",function(){

		$(this).siblings().removeClass("active");
		$(this).addClass("active");

	})

	//End of script for numbered pagination links////////////


})
	
	function nextBtn(thisObj){

		console.log(thisObj);
		thisObj.siblings(".active").next().click();
		thisObj.siblings(".active").removeClass("active");
		thisObj.next().addClass("active");
		
		
	}

	function requestData(page=1,limit=10,filter=null){

		console.log("is it working?");
		data="page="+page+"&limit="+limit+"&filter="+filter;//+{ _token: "{{csrf_token()}}"};

		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
			type:"POST",
			url:"{{url('/pagination')}}",
			data:data,
		}).done(function(data){

		var data = JSON.parse(data);
		
		$("#pagination-links").html(data["pagination_links"]);
	});

	}

	requestData();

	
</script>
</body>
</html>