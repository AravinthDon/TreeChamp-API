<?php

    // set headers
    header('Access-Control-Allow-Methods: GET');
    header('Content-Type: application/json');

    // check if the method is get
    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        if(!isset($_GET['latitude']) && !isset($_GET['longitude'])) {
            echo json_encode(array("status" => "Error", "message" => "Latitude and Longitude not set"));
        } elseif(!isset($_GET['latitude']) | !isset($_GET['longitude'])) {
            
            if(!isset($_GET['latitude'])) {
                echo json_encode(array("status" => "Error", "message" => "Latitude not set"));
            } elseif(!isset($_GET['longitude'])) {
                echo json_encode(array("status" => "Error", "message" => "Longitude not set"));
            }
        } else {

            // include files
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/tree.php");

            // get the details
            $coord['latitude'] = $_GET['latitude'];
            $coord['longitude'] = $_GET['longitude'];

            // sanitise the details
            array_walk($coord, 'trim_value');
            array_walk($coord, 'real_escape_string');

            $latitude = $coord['latitude'];
            $longitude = $coord['longitude'];
            // query for fetching details
            $LOCATION_SEARCH_QUERY = "SELECT TREEID FROM TC_tree WHERE latitude LIKE '$latitude%' AND longitude LIKE '$longitude%'";
            $result = select_query($conn, $LOCATION_SEARCH_QUERY);

            if($result->num_rows > 0) {
                
                $counter = 0;
                $trees = array();

                // fetch the results
                while($row = $result->fetch_assoc()) {
                    // get the details
                    $trees[$counter] = fetch_tree($conn, $row['TREEID']);
                    $counter += 1;
                }

                echo json_encode($trees);
                
            }

        }
    }
?>