<?php

    // Allow only POST methods
    header('Access-Control-Allow-Methods: GET POST PUT');
    header('Content-Type: application/json');

    // include the necessary files
    include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
    include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
    include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/user.php");

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
                //$update = json_decode(utf8_encode($_POST['data']));

                $data = json_decode(file_get_contents('php://input'), true);
                $update = $data['data'];

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

                    if(isset($update['posts'])) {

                        // Get the posts details
                        $posts = $update['posts'];
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
                
                if(isset($_GET['updateid'])) {

                    /**
                     * Maybe Need to secure this endpoint
                     *  - send details if
                     *      - it is made the current user
                     *      - or he is an admin
                     */
                    $updateid = $_GET['updateid'];
                    real_escape_string($updateid);

                    $FETCH_UPDATE_DETAILS_QUERY = "SELECT * FROM TC_update WHERE UPDATEID = $updateid";
                    $update_result = select_query($conn, $FETCH_UPDATE_DETAILS_QUERY);

                    if($update_result -> num_rows > 0) {

                        $update_row = $update_result -> fetch_assoc();
                        $update = array();

                        $update['title'] = $update_row['Title'];
                        $update['description'] = $update_row['Description'];
                        $update['dateadded'] = $update_row['Dateadded']; // might wanna change the data type

                        $update['posts'] = array();
                        // Fetch the images related to the update
                        $FETCH_IMAGES_QUERY = "SELECT * FROM TC_image WHERE UPDATEID = $updateid";
                        $images_result = select_query($conn, $FETCH_IMAGES_QUERY);

                        // Check if the update has any images associated with it
                        if($images_result -> num_rows > 0) {

                            while($image_row = $images_result -> fetch_assoc()) {
                                $image = array();
                                $image['imgURL'] = $image_row['ImageURL'];
                                $image['caption'] = $image_row['Caption'];

                                // Add the image to the updates
                                array_push($update['posts'], $image);
                            }
    
                        }
                    
                        echo json_encode(array("status" => "Success", "data" => $update));
                    } else {
                        echo json_encode(array("status" => "Error", "message" => "Update not found"));
                    }
                } else { 
                    
                    /**
                     * Check if posts are required only for a certain user
                     * 
                     * if not send updates made by all users - this feature is only for admin
                     */

                     if(isset($_GET['userid'])) {
                        
                     } else {
                         // verify the admin status
                     }

                }
            }
        }

    }

?>