<?php
    // set the header to json type
    // header('Content-Type: application/json');

    //host details
    $host = "aravichandiran01.lampt.eeecs.qub.ac.uk";
    $user = "aravichandiran01";
    $password = "6csZxSbhNd9jSQjv";
    $database = "aravichandiran01";

    // create a connection
    $conn = new mysqli($host, $user, $password, $database);

    // check for connection errors
    if($conn->connect_error) {
        echo "Connection error: ".$conn->connect_error;
    } 
    
?>