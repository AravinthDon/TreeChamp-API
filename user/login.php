<?php
    /**
     * Password hashing reference: https://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
     */
    // set the headers
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    // check if the method is post
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // check if the username and the password is given
        if(!isset($_POST['password']) && !isset($_POST['username']) && !isset($_POST['type'])) {
            echo json_encode(array("status" => "Error", "message" => "Username, Password,and UserType not provided"));
        } elseif( !isset($_POST['password']) | !isset($_POST['username']) | !isset($_POST['type']) ) {
            // Respond with appropriate error messages
            if(!isset($_POST['username'])) {
                echo json_encode(array("status" => "Error", "message" => "Username not provided"));
            } elseif(!isset($_POST['password'])) {
                echo json_encode(array("status" => "Error", "message" => "Passowrd not provided"));
            } elseif(!isset($_POST['type'])) {
                echo json_encode(array("status" => "Error", "message" => "UserType not provided"));
            }   

        } else {
            // include files
            include("../config/database.php");
            include("../utilities/db.php");
            include("../utilities/user.php");

            // create user array
            $user = array();
            $user['username'] = $_POST['username'];
            $user['password'] = $_POST['password'];
            $user['type'] = $_POST['type'];

            // trim whitespace on the $user details
            array_walk($user, 'trim_value');

            // sanitise the $user details
            array_walk($user, 'real_escape_string');

            // check for the usertype id 
            // $usertypeid_Query = "SELECT USERTYPEID FROM TC_userType WHERE UserType = \"{$user['type']}\"";
            // $res_usertypeid = select_query($conn, $usertypeid_Query);

            $usertypeid = get_usertypeid($conn, $user['type']);

            // if($res_usertypeid -> num_rows > 0) {
            //     $usertypeid = $res_usertypeid -> fetch_assoc()['USERTYPEID'];
            // }
            $username = $user['username'];
            // check if the username is already available
            $USER_CHECK_QUERY = "SELECT *  FROM `TC_user` WHERE `Username` LIKE '{$username}' AND `USERTYPEID` = {$usertypeid}";
            $result = select_query($conn, $USER_CHECK_QUERY);
            

            if($result->num_rows > 0 ) {
                // the user is available check for the passowrd
                $row = $result->fetch_assoc();

                // get the details
                $user_id = $row['USERID'];
                $username = $row['Username'];
                $hash = $row['Password'];
                $api_key = $row['api_key'];
                $type = get_usertype($conn, $row['USERTYPEID']);

                // since the username is unique we only need to check the password
                if(password_verify($user['password'], $hash)){
                    $response = array();
                    $response['user_id'] = $user_id;
                    $response['api_key'] = $api_key;
                    $response['type'] = get_usertype($conn, $row['USERTYPEID']);

                    echo json_encode(array("status" => "Success", "data" => $response));
                } else {
                    echo json_encode(array("status" => "Error", "message" => "Wrong Password"));
                }
            } else {
                echo json_encode(array("status" => "Error", "message" => "Username not found"));
            }

        }
    }
?>