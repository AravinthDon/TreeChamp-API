<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
// Added user defined headers: uid, appid
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, uid, appid");
header('Content-Type: application/json');


$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, uid, appid");
    header("HTTP/1.1 200 OK");
    die();
}


// include the necessary files
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/user.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/update.php");

$headers = apache_request_headers();
$uid = $headers['uid'];
$api_key = $headers['appid'];

if(!isset($uid) && !isset($api_key)){
    echo json_encode(array("status" => "Error", "message" => "User Id and API key not set"));
} else {
    if(!verify_admin($conn, $uid)){
        echo json_encode(array("status" => "Error", "message" => "Not an admin user"));
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(!isset($_POST['updateid']) && !isset($_POST['set'])){
                echo json_encode(array("status" => "Error", "message" => "Tree details incorrect"));
            } else {
                $updateid = $_POST['updateid'];
                $set = $_POST['set'];

                $updateid = mysqli_real_escape_string($conn, $updateid);
                $set = mysqli_real_escape_string($conn, $set);

                $UPDATE_ISSUE_QUERY = "UPDATE TC_update SET Solved = $set WHERE UPDATEID = $updateid";

                if($conn->query($UPDATE_ISSUE_QUERY) === TRUE) {
                    echo json_encode(array("status" => "Success", "message" => "Successfully updated"));
                } else {
                    echo json_encode(array("status" => "Error", "message" => $conn->error));
                }
            }
        }
    }
}

?>
