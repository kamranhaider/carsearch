<?php
include("classes/cars.class.php");
$cars = new cars();
isset($_GET['action'])===true?$action=$_GET['action']:$action='invalid';
isset($_GET['token'])===true?$token=$_GET['token']:$token='invalid';
$validate = $cars->validateRequest($token);
if($validate ==='invalid') die("Unauthorized request!!");

switch ($action)
{
    case "searchcar":
            $param = (object)$_POST;
            $result = $cars->SearchCars($param);
            header("Content-Type:application/json");
            echo json_encode($result);
        break;

}
