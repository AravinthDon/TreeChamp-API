<?php

    // Allow only POST methods
    header('Access-Control-Allow-Methods: GET POST PUT');
    header('Content-Type: application/json');

    // include the necessary files
    include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
    include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");

    // Get the url
    //$query = parse_url($_SERVER['REQUEST_URI'])['query'];

    // Get the headers
    $headers = apache_request_headers();
    $uid = $headers['uid'];
    $api_key = $headers['appid'];

    // Check if the user is valid
    if(!isset($uid) && !isset($api_key)){
        echo json_encode(array("status" => "Error", "message" => "User Id and API key not provided."));
    } else {
        if(!verify_user($conn, $uid, $api_key)) {
            // User not valid
            echo json_encode(array("status" => "Error", "message" => "User not valid"));
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                // Get the data
                $update = json_decode($utf8_encode($_POST['data']));

                // Update the details
                if(!isset($update['title']) && !isset($update['description']) && !isset($update['treeid'])) {
                    echo json_encode(array("status" => "Error", "message" => "Title and Description not set"));
                } else {

                    $title = $update['title'];
                    $description = $update['description'];

                    $title = mysqli_real_escape_string($conn, $title);
                    $description = mysqli_real_escape_string($conn, $description);
                    $userid = mysqli_real_escape_string($conn, $uid);
                    $treeid = mysqli_real_escape_string($conn, $update['treeid']);

                    $INSERT_UPDATE_QUERY = "INSERT INTO TC_update(TREEID, USERID, Title, `Description`) VALUES($treeid, $userid, '$title', '$description')";
                    $updateid = insert_query($conn, $INSERT_UPDATE_QUERY);

                    foreach($posts as &$post) {
                        if(isset($post['imgURL'])) {
                            real_escape_string($post['imgURL']);
                            $imgurl = $post['imgURL'];
                            
                            if(isset($post['caption'])) {
                                $caption = $post['caption'];
                            } else {
                                $caption = "Updated Image";
                            }

                            $IMAGE_INSERT_QUERY = "INSERT INTO TC_image(UPDATEID, ImageURL, Caption) VALUES($updateid, '$imgurl', '$caption')";
                            $imgupdateid = insert_query($conn, $IMAGE_INSERT_QUERY);
                        }
                    }
                    // Send the response
                    if(isset($updateid)) {
                        echo json_encode(array("status" => "Success", "updateID" => $updateid));
                    } else {
                        echo json_encode(array("status" => "Error", "message" => "Cannot make an update"));
                    }
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'GET') {

            }
        }

    }

?>