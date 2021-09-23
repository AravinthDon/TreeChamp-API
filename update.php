<?php

// Allow only POST methods
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET POST');
header('Content-Type: application/json');

// include the necessary files
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/user.php");
include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/update.php");


// Get the url
//$query = parse_url($_SERVER['REQUEST_URI'])['query'];

// Get the headers
$headers = apache_request_headers();
$uid = $headers['uid'];
$api_key = $headers['appid'];

// Check if the user is valid
if (!isset($uid) && !isset($api_key)) {
    echo json_encode(array("status" => "Error", "message" => "User Id and API key not provided."));
} else {
    if (!verify_user($conn, $uid, $api_key)) {
        // User not valid
        echo json_encode(array("status" => "Error", "message" => "User not valid"));
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get the data
            //$update = json_decode(utf8_encode($_POST['data']));

            $data = json_decode(file_get_contents('php://input'), true);
            $update = $data['data'];

            // Update the details
            if (!isset($update['title']) && !isset($update['description']) && !isset($update['treeid'])) {
                echo json_encode(array("status" => "Error", "message" => "Title and Description not set"));
            } else {

                $title = $update['title'];
                $description = $update['description'];
                $issue = $update['issue'];

                $title = mysqli_real_escape_string($conn, $title);
                $description = mysqli_real_escape_string($conn, $description);
                $userid = mysqli_real_escape_string($conn, $uid);
                $treeid = mysqli_real_escape_string($conn, $update['treeid']);
                $issue = mysqli_real_escape_string($conn, $issue);


                $INSERT_UPDATE_QUERY = "INSERT INTO TC_update(TREEID, USERID, Title, `Description`, `Issue`) VALUES($treeid, $userid, '$title', '$description', $issue)";
                $updateid = insert_query($conn, $INSERT_UPDATE_QUERY);

                if (isset($update['posts'])) {

                    // Get the posts details
                    $posts = $update['posts'];
                    foreach ($posts as &$post) {
                        if (isset($post['imgURL'])) {
                            real_escape_string($post['imgURL']);
                            $imgurl = $post['imgURL'];
 
                            if (isset($post['caption'])) {
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
                if (isset($updateid)) {
                    echo json_encode(array("status" => "Success", "updateID" => $updateid));
                } else {
                    echo json_encode(array("status" => "Error", "message" => "Cannot make an update"));
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            if (isset($_GET['updateid'])) {

                /**
                 * Maybe Need to secure this endpoint
                 *  - send details if
                 *      - it is made by the current user
                 *      - or he is an admin
                 */
                $updateid = $_GET['updateid'];
                real_escape_string($updateid);

                $update = get_update($conn, $updateid);

                if (!empty($update)) {
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

                if (isset($_GET['userid'])) {
                    $puserid = $_GET['userid'];
                    real_escape_string($puserid);
                    // Fetch update ids of updates made by the user
                    $USER_UPDATES_QUERY = "SELECT UPDATEID FROM TC_update WHERE USERID = $puserid";
                    $updates_result = select_query($conn, $USER_UPDATES_QUERY);

                    if($updates_result-> num_rows > 0) {

                        $updates = array();
                        while($update_row = $updates_result->fetch_assoc()) {
                            $update = get_update($conn, $update_row['UPDATEID']);
                            if(!empty($update)) {
                                array_push($updates, $update);
                            }
                        }

                        echo json_encode(array("status" => "Success", "data" => $updates));

                    } else {
                        echo json_encode(array("status" => "Error", "message" => "No updates found"));
                    }
                } else {
                    // // verify the admin status
                    // if(!verify_admin($conn, $uid)) {
                    //     echo json_encode(array("status" => "Error", "messsage" => "Only admins can view all trees"));
                    // }
                    
                    $UPDATES_QUERY = "SELECT UPDATEID FROM TC_update ORDER BY Dateadded DESC";
                    $updates_result = select_query($conn, $UPDATES_QUERY);

                    if($updates_result-> num_rows > 0) {

                        $updates = array();
                        while($update_row = $updates_result->fetch_assoc()) {
                            $update = get_update($conn, $update_row['UPDATEID']);
                            if(!empty($update)) {
                                array_push($updates, $update);
                            }
                        }

                        echo json_encode(array("status" => "Success", "data" => $updates));
                    } else {
                        echo json_encode(array("status" => "Error", "message" => "No updates found"));
                    }
                      
                }
            }
        }
    }
}
