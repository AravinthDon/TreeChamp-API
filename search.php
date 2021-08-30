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
        $get_data = json_decode(file_get_contents('php://input'), true);

        //print_r($get_data);

        if(!isset($get_data['latitude']) && !isset($get_data['longitude'])) {
            echo json_encode(array("status" => "Error", "message" => "Latitude and Longitude not set"));
        } elseif(!isset($get_data['latitude']) | !isset($get_data['longitude'])) {
            
            if(!isset($get_data['latitude'])) {
                echo json_encode(array("status" => "Error", "message" => "Latitude not set"));
            } elseif(!isset($get_data['longitude'])) {
                echo json_encode(array("status" => "Error", "message" => "Longitude not set"));
            }
        } else {

            // include files
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/config/database.php");
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/db.php");
            include("$_SERVER[DOCUMENT_ROOT]/treechamp/api/utilities/tree.php");

            // get the details
            $coord['latitude'] = $get_data['latitude'];
            $coord['longitude'] = $get_data['longitude'];

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


                    if ($distance <= 100) {

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
                
            }

        }
    }
?>