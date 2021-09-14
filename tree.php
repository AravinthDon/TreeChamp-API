<?php

    // set headers
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET POST');
    header('Content-Type: application/json');

    // add necessary files
    include("config/database.php");
    include("utilities/db.php");
    include("utilities/tree.php");

    if($_SERVER['REQUEST_METHOD'] == "GET") {
        if(isset($_GET['treeid'])) {
            
            $tree = fetch_tree($conn, $_GET['treeid']);
            echo json_encode(array("status" => "Success", "data" => $tree));
        } else {
            echo json_encode(array("status" => "Error", "message" => "Tree details not found" ));
        }
    }
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        
        // create the array
        $tree = array();

        $tree['TREEID'] = $_POST['treeid'];
        $tree['TreeTag'] = $_POST['treetag'];
        $tree['TreeType'] = $_POST['treetype'];
        $tree['SpeciesType'] = $_POST['speciestype'];
        $tree['Species'] = $_POST['species'];
        $tree['Age'] = $_POST['age'];
        $tree['TreeSurround'] = $_POST['treesurround'];
        $tree['Vigour'] = $_POST['vigour'];
        $tree['Condition'] = $_POST['condition'];
        $tree['Longitude'] = $_POST['longitude'];
        $tree['Latitude'] = $_POST['latitude'];
        $tree['Height'] = $_POST['height'];
        $tree['Description'] = $_POST['description'];
        $tree['SpreadRadius'] = $_POST['spreadradius'];
        $tree['Diameter'] = $_POST['diameter'];
        
        $result = updateTree($conn, $tree);
        if($result['status'] == "Success") {
            echo json_encode(array("status" => "Success", "data" => $result['tree']));
            
        } else {
            echo json_encode(array("status" => "Error", "message" => $result['error']));
        }
    }
?>