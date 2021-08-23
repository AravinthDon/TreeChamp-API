<?php

    // Include the database connection file

    include("../config/database.php");
    include("../utilities/db.php");
    
    // Upload the file with the front-end app to the server
    // use file_get_contents($endpoint) to then fetch the file

    // php api file upload https://www.onlyxcodes.com/2021/03/php-rest-api-file-upload.html
    $endpoint = "odTrees.csv";

    $file = file($endpoint);

    //$lines = explode(PHP_EOL, $file);

    $rows = array_map('str_getcsv', $file);
    $header = array_shift($rows);
    $csv = array();

    //$array = $fields = array();
    /**
     * Allow the admin user to populate the database with csv files
     * 
     * - Read data from the file
     * - For each entry: 
     *  - Check for the data quality
     *  - Call the add() function 
     * 
     * */ 

    // Create the array

    foreach($rows as $row) {
        if (count($header) == count($row)) {
            $csv[] = array_combine($header, $row);
        }
    }
    
    echo "Uploading data...";
   // Iterate through the data to upload
    foreach($csv as $row) {
            include_once('tree.php');
            add_tree($conn, $row);
        }
 

    echo "Success";
 
?>
