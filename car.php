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
$carDetail = $cars->CarDetails($_GET['id']);

?>
<!-- Wrapper -->
<div id="wrapper">

    <!-- Header -->
    <header id="header">
        <div class="inner"></div>
    </header>

    <div id="">
        <div class="inner">
            <div class="image main">
                <img src="images/banner.jpg" class="img-fluid" alt="" />
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">

                        <a class="btn btn-info" href="search.php">Search More</a>
                        <h3 class="mb-0 mt-5">Dealer</h3>
                        <?php echo $carDetail->dealer_name?>
                        <h3 class="mb-0 mt-5">Dealer Address</h3>
                        <?php echo $carDetail->dealer_address?>
                    </div>



                    <div class="col-md-9">
                        <section class="">

                            <div class="container mx-auto mt-4">
                                    <h2><?php echo $carDetail->make ." - " .$carDetail->year?></h2>
                                    <div class="row text-info">
                                        <div class="col-md-4">
                                            <strong>Price : </strong>$<?php echo number_format($carDetail->price,0) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Make : </strong><?php echo $carDetail->make ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Body Type : </strong><?php echo $carDetail->body_type?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Transmission : </strong><?php if($carDetail->transmission==='A'){echo 'Automatic';}else{echo 'Manual';} ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Doors : </strong><?php echo $carDetail->doors?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Engine : </strong><?php echo $carDetail->engine?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>&nbsp;&nbsp;&nbsp;Exterior : </strong><?php echo $carDetail->exterior?>
                                        </div>
                                    </div>
                                    <p class="text-default"><?php echo $carDetail->description?> </p>

                                        <h3>Amenities:</h3>
                                        <div class="row">
                                        <?php $amm = explode("|", $carDetail->amenities);
                                            foreach($amm as $am){?>

                                                <span class="mb-1 mr-2 p-2 badge badge-info text-white"><?php echo $am?></span>

                                        <?php }?>
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
                    $html = $html + '<a href="car.php?id="' + item.row_id +' class="btn btn-sm btn-info"> View Detail</a>';
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