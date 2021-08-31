<?php

    /*
     *   Calculate distance between two points on earth
     *   Haversine formula : 
     *    https://www.movable-type.co.uk/scripts/latlong.html
     *   Spherical law of cosines:
     *    slightly slower than haversine
     *     Highly precise
     *   Equirectangular approximation:
     *    less accurate
     *    based on pythagoras theorem
     *   https://www.php.net/manual/en/mysqli.quickstart.stored-procedures.php
     *    
     */
    // set headers
    header('Access-Control-Allow-Methods: GET');
    header('Content-Type: application/json');

    // check if the method is get
    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        // parse the json data
        //$_GET = json_decode(file_get_contents('php://input'), true);
        
        //echo $_GET;
        //print_r($_GET);

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
        
            // Fetch all the tress 
            $LOCATION_SEARCH_QUERY = "SELECT * FROM TC_tree";
            $result = select_query($conn, $LOCATION_SEARCH_QUERY);

            if($result->num_rows > 0) {
                
                $counter = 0;
                $trees = array();

                // fetch the results
                while($row = $result->fetch_assoc()) {

                    // The spherical law of cosine algorithm
                    $pi1 = $latitude * pi()/180;
                    $pi2 = $row['Latitude'] * pi()/180;
                    $delta = ($row['Longitude'] - $longitude) * pi()/180;
                    $radius = 6371000;

                    // Calculate the distance
                    $distance = acos(sin($pi1) * sin($pi2) + cos($pi1) * cos($pi2) * cos($delta) ) * $radius;

                    // check if surround Distance is provided
                    // default : 100 metres
                    $surroundDistance = isset($_GET['surround']) ? $_GET['surround'] : 100;

                    if ($distance <= $surroundDistance) {

                        // calculate the bearing for the tree
                        $y = sin($row['Longitude'] - $longitude) * cos($row['Latitude']);
                        $x = cos($latitude) * sin($row['Latitude']) - sin($latitude) * cos($row['Latitude'])
                        * cos($row['Longitude'] - $longitude);

                        // calculate the initial bearing
                        $thetha = atan2($y, $x);
                        $bearing = ($thetha * 180 / pi() + 360) % 360;

                        // get the details
                        $trees[$counter] = fetch_tree($conn, $row['TREEID']);
                        // add the distance
                        $trees[$counter]['Distance'] = $distance;
                        $trees[$counter]['Bearing'] = $bearing;
                        $counter += 1;
                    }
                    
                }

                echo json_encode($trees);
                //echo json_encode($_GET);
            }

        }
    }
?>