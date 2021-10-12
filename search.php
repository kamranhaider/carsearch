<!DOCTYPE HTML>
<html>
<head>
    <title>Car Search</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap/css/main.css" />
    <noscript><link rel="stylesheet" href="bootstrap/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
<?php include("classes/cars.class.php");
    $cars = new cars();
    $bodyType = $cars->BodyTypes();
    $makes = $cars->Makes();
    $token = $cars->createToken();

?>
<!-- Wrapper -->
<div id="wrapper">

    <!-- Header -->
    <header id="header">
        <div class="inner">


        </div>
    </header>

    <div id="">
        <div class="inner">
            <div class="image main">
                <img src="images/banner.jpg" class="img-fluid" alt="" />
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <h2>Search Your Favorite Car</h2>
                        <div class="row">
                            <form id="searchform">
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="">Make</label>
                                        </div>
                                        <select class="custom-select" name="make" >
                                            <option value="any" selected>Choose...</option>
                                            <?php foreach($makes as $make){?>
                                                <option><?php echo $make->make?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">Transmission</label>
                                        </div>
                                        <select class="custom-select" name="transmission" >
                                            <option value="any" selected>Any...</option>
                                            <option value="A">Automatic</option>
                                            <option value="M">Manual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">Body Type</label>
                                        </div>
                                        <select class="custom-select" name="body_types" >
                                            <option value="any" selected>Any...</option>
                                            <?php foreach($bodyType as $body){?>
                                                <option><?php echo $body->body_type?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">Doors</label>
                                        </div>
                                        <select class="custom-select" name="doors" >
                                            <option value="any" selected>Any...</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">Cylinders</label>
                                        </div>
                                        <select class="custom-select" name="cylinders" >
                                            <option value="any" selected>Any...</option>
                                            <option>4</option>
                                            <option>5</option>
                                            <option>6</option>
                                            <option>7</option>
                                            <option>8</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
                                    <div class="input-group w-100 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">Your Zipcode</label>
                                        </div>
                                        <input type="text" class="form-control" id="zipcode" value="1960" name="zipcode">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <a type="button" id="btnSearch" class="btn w-100 text-white btn-primary btn-sm">Search</a>
                                </div>
                                <div class="col-md-12">
                                    <small id="nozip" class="text-danger"></small>
                                </div>
                            </form>
                        </div>

                        </div>



                    <div class="col-md-9">
                        <section class="">

                            <div class="container mx-auto mt-4">
                                <div id="searchresult" class="row">


                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>

<!-- Scripts -->
<script src="bootstrap/js/jquery/jquery.min.js"></script>
<script src="js/functions.js"></script>
<script>
    $("#btnSearch").on("click",function(){
        $("#nozip").html("");
        $("#searchresult").html("Searching......");
        if($("#zipcode").val()=='')
        {
            $("#nozip").html("To get precise result, please enter your Zipcode");
            $("#zipcode").focus();
            return;
        }
        data = $("#searchform").serialize();
        console.log(data);
        $.ajax({
            url:"ajax.php?action=searchcar&token=<?php echo $token?>",
            type:"POST",
            dataType:"json",
            data:data,
            success:function(response){
               $html = "";

               $.each(response, function(i, item){
                    images = item.imagefile.split("|");
                    image = checkifImage(images);
                    $html = $html + '<div class="col-md-4 mb-4">';
                    $html = $html + '<div class="card" style="width: 18rem;">';
                    $html = $html + '<img src="' + image + '" class="card-img-top" alt="...">';
                    $html = $html + '<div class="card-body">';
                    $html = $html + '<h5 class="card-title">' + item.make + '-' + item.year +'</h5>';
                    $html = $html + '<h6 class="card-subtitle mb-2 text-muted">' + item.body_type + ' - ' + item.model + '</h6>';
                    $html = $html + '<p class="card-text" style="font-size:14px; max-height:151px; overflow:hidden">' + item.description + '</p>';
                   $html = $html + '<small class="text-info"><strong>Price:</strong> ' + item.price + '<BR /><strong>Dealer:</strong> ' + item.dealer_name + '</small>';
                    $html = $html + '<a href="car.php?id=' + item.row_id +'" class="btn btn-sm btn-info"> View Detail</a>';
                   // $html = $html + '<a href="#" class="btn "><i class="fab fa-github"></i> Github</a>';
                    $html = $html + '</div></div></div>';
               });
               $html===""?$html="No result found....":$html=$html;
                $("#searchresult").html($html);
            }
        })
    })
</script>
</body>
</html>