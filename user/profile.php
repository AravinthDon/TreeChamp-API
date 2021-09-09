<?php

/**
 * For fetching and editing the profile details
 */

header('Access-Control-Allow-Methods: GET POST PUT');
header('Content-Type: application/json');

// include the necessary files
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/user.php");

// Get the headers
$headers = apache_request_headers();
$uid = $headers['uid'];
$api_key = $headers['appid'];

if (!isset($uid) && !isset($api_key)) {
    echo json_encode(array("status" => "Error", "message" => "User ID and API key not provided."));
} else {
    if (!verify_user($conn, $uid, $api_key)) {
        // User not valid
        echo json_encode(array("status" => "Error", "message" => "User not valid"));
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $FETCH_USERDETAILS_QUERY = "SELECT * FROM TC_user WHERE USERID = $uid";
            $user_result = select_query($conn, $FETCH_USERDETAILS_QUERY);

            if ($user_result->num_rows > 0) {
                $user = array();

                $user_row = $user_result->fetch_assoc(); // only one row

                $user['username'] = $user_row['Username'];
                // Add any additional details in the future

                echo json_encode(array("status" => "Success", "data" => $user));
            } else {
                echo json_encode(array("status" => "Error", "message" => "Details not found!"));
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

            // Get the url
            $rpath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = explode('/', $rpath);
            $endofurl = $path[count($path) - 1];

            // get the data
            $data = json_decode(file_get_contents('php://input'), true);

            switch ($endofurl) {
                case "changepassword":
                    $password = $data['password'];
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    real_escape_string($hashed_password);

                    $UPDATE_PASSWORD_QUERY = "UPDATE TC_user SET Password='$hashed_password' WHERE USERID=$uid";

                    if ($conn->query($UPDATE_PASSWORD_QUERY) == TRUE) {
                        echo json_encode(array("status" => "Success", "message" => "Password Changed successfully"));
                    } else {
                        echo json_encode(array("status" => "Error", "message" => "Failed to update password!"));
                    }
                    break;
                case "changeusername":
                    $username = $data['username'];
                    // check if the username is already available
                    $USER_CHECK_QUERY = "SELECT *  FROM `TC_user` WHERE `Username` LIKE '{$username}'";
                    $result = select_query($conn, $USER_CHECK_QUERY);

                    if ($result->num_rows > 0) {
                        //array_push();
                        echo json_encode(array("status" => "Error", "message" => "Username already taken"));
                    } else {
                        $UPDATE_USERNAME_QUERY = "UPDATE TC_user SET Username='$username' WHERE USERID=$uid";

                        if ($conn->query($UPDATE_USERNAME_QUERY) == TRUE) {
                            echo json_encode(array("status" => "Success", "message" => "Username Changed successfully"));
                        } else {
                            echo json_encode(array("status" => "Error", "message" => "Failed to update username!"));
                        }
                    }
                    break;
            }
        }
    }
}
