<?php
/*
 * Class Name : cars
 * Language : PHP
 * Author : Kamran Haider
 * Usage : A class to get information about the cars listings.
 * Description: This class contains the methods to retrieve cars listings info like list of Car Makes, Body Types, and the list of cars that are related to requested search.
 *
 */
class cars{
    private $cn;
    private $dbhost = "104.238.228.175";
    private $dbuser = "testproject";
    private $dbpwd = "TestProject$$123";
    private $db = "cars";

    /*
     * Method Name : _construct
     * Description : This method executes when it get included in some page
     */
    function __construct() {
        $this->cn = mysqli_connect($this->dbhost,$this->dbuser,$this->dbpwd,$this->db);
    }

    /*
     * Method Name : CarDetails
     * Parameter : $id
     * Returns : Data object of car's detail
     * Description : Method used to get details about the requested cars. it gets id of a car query from the database and returns the details
     * Usage : Method is being used on the page car.php
     */
    public function CarDetails($id){
        $sql = "select * from view_cars where row_id='$id'";
        $data = $this->executeQuery($sql);
        return $data[0];
    }

    /*
     * Method Name : BodyTypes
     * Parameter : None
     * Returns : Returns expected body types master data
     * Description : This method returns the data object for the list of body types to display on the search form drop down
     * Usage : Method is being used on the page search.php
     */
    public function BodyTypes(){
        $sql = "select * from body_types";
        $data = $this->executeQuery($sql);
        return $data;
    }

    /*
     * Method Name : Makes
     * Parameter : None
     * Returns : Returns expected Makes (Brand Name) master data
     * Description : This method returns the data object for the list of car makes(brands) to display on the search form drop down
     * Usage : Method is being used on the page search.php
     */
    public function Makes(){
        $sql = "select * from car_makes";
        $data = $this->executeQuery($sql);
        return $data;
    }

    /*
     * Method Name : ValidateRequest
     * Parameter : $token
     * Returns : "valid" or "invalid"
     * Description : It executes from the ajax script to validate the request from the website, it receive the token in parameters and validate from the database that is already generated on the page, if token is matched from the one that is in database it returns valid otherwise invalid
     * Usage : Method is being used on the page ajax.php
     */
    public function validateRequest($token){
        if($token==='invalid') return 'invalid';
        //if($token===$_SESSION['token']) return 'valid';else return 'invalid';
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "select * from user_sessions where ip_address='$ip'";
        $qry = mysqli_query($this->cn, $sql);
        $t = mysqli_fetch_array($qry);
        if($token===$t['token']) return 'valid';else return 'invalid';
        //echo $t['token'];

        //return json_decode(json_encode($data));
    }

    /*
     * Method Name : CreateToken
     * Parameter : None
     * Returns : A random string containing 15 characters
     * Description : It executes from the web page to generate a token string to send in all ajax requests to validate.
     * Usage : Method is being used on the page search.php
     */
    public function createToken(){
        $str = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0');
        $limit = 15;
        $token = "";
        for($i=0;$i<=$limit;$i++){
            $rnd = rand(0, 35);
            $token .= $str[$rnd];
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "insert into user_sessions  (ip_address, token) values ('$ip', '$token') on duplicate key update token='$token'";
        mysqli_query($this->cn, $sql);
        return $token;
    }
    /*
     * Method Name : getNearbyZips
     * Parameter : $zipcode
     * Returns : List of zipcodes from 50miles radius
     * Description : Method to get list of zip codes that resides in 50miles radius of the given zip code, it returns the list of nearby zipcode to precise the search for the cars.
     * Usage : This is a private method and can be used within the same class
     */
    private function getNearbyZips($zipcode){
        $radius = 50;
        if($zipcode==''){
            $str_arr_ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']));
            $latitude = $str_arr_ipdat->geoplugin_latitude;
            $longitude = $str_arr_ipdat->geoplugin_longitude;
        }else{
            $sql = "select * from tbl_zipcodes where zipcode='$zipcode'";
            $qry = mysqli_query($this->cn, $sql);
            $data = mysqli_fetch_array($qry);
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];
        }
        $sql = 'SELECT zipcode FROM tbl_zipcodes WHERE (POW((69.1*(longitude-"' . $longitude. '")*cos(' . $longitude .'/57.3)),"2")+POW((69.1*(latitude-"' . $latitude. '")),"2"))<(' . $radius . '*' . $radius . ')';
        $qry = mysqli_query($this->cn, $sql);
        $zips = "";
        while($row = mysqli_fetch_assoc($qry)) $zips .= "'".$row['zipcode']."',";
        return trim($zips, ",");
    }
    /*
     * Method Name : SearchCars
     * Parameter : $param (array of the parameters given from the search form)
     * Returns : List of zipcodes from 50miles radius
     * Description : This is the main method that searches the cars from the database according to the filters, it only includes the filter in the query that is set in the form.
     * Usage : method is being method by on the page search.php
     */
    public function SearchCars($param){
        $zips = $this->getNearbyZips($param->zipcode);
        $sql = "select * from view_cars  where zip in ($zips)";
        if($param->make!='any') $sql .= " and make='".$param->make."'";
        if($param->transmission!='any') $sql .= " and transmission='".$param->transmission."'";
        if($param->body_types!='any') $sql .= " and body_type='".$param->body_types."'";
        if($param->cylinders!='any') $sql .= " and cylinders='".$param->cylinders."'";
        if($param->doors!='any') $sql .= " and doors='".$param->doors."'";
        $data = $this->executeQuery($sql);
        return $data;
    }

    /*
     * Method Name : executeQuery($sql)
     * Parameter : $sql
     * Returns : Database query result
     * Description : Method to execute the database queries within the class
     * Usage : This is a private method and can be used only within the class object
     */
    private function executeQuery($sql){
        $qry = mysqli_query($this->cn, $sql);
        $data = array();
        while($row = mysqli_fetch_assoc($qry)) $data[] = $row;
        return json_decode(json_encode($data));
    }
}