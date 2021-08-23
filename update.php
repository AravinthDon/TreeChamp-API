<?php

    // Allow only POST methods
    header('Access-Control-Allow-Methods: POST');
    header('Content-Type: application/json');

    // check if the method is POST
    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        if(!isset($_POST['userid']) && !isset($_POST['api_key'])) {
            include_once('utilities/user.php');
            //if(verify_user($conn, $user_id, $api_key)) {
                if(!isset($_POST['title']) && !isset($_POST['description'])) {
                    $title = $_POST['title'];
                    $description = $_POST['description'];

                    
                }
            //}
        } 
    }
