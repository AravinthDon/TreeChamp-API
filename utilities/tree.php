<?php

    // include db file
    //include("db.php");

    function add_typeOfTree($conn, $typeOfTree) {
        return add_value($conn, "TC_treeType", "TreeType", $typeOfTree, "TREETYPEID");
    }

    function add_speciesType($conn, $speciesType) {
        
        return add_value($conn, "TC_speciesType", "SpeciesType", $speciesType, "SPECIESTYPEID");
    }

    function add_species($conn, $species) {
        return add_value($conn, "TC_species", "Species", $species, "SPECIESID");
    }

    function add_age($conn, $age) {
        return add_value($conn, "TC_age", "Age", $age, "AGEID");
    }

    function add_vigour($conn, $vigour) {
        return add_value($conn, "TC_vigour", "Vigour", $vigour, "VIGOURID");
    }

    function add_condition($conn, $condition) {
        return add_value($conn, "TC_condition", "ConditionType", $condition, "CONDITIONID");
    }

    function add_treeSurround($conn, $treeSurround) {
        return add_value($conn, "TC_treeSurround", "TreeSurround", $treeSurround, "TREESURROUNDID");
    }


    function add_tree($conn, $tree) {
        
        //$treetag = 'NULL';
        //$age_id = 'NULL';
        //$species_id = 'NULL';
        //$speciestype_id = 'NULL';

        if(!empty($tree['TYPEOFTREE'])){
            $typeoftree_id = add_typeOfTree($conn, $tree['TYPEOFTREE']);
        }

        if(!empty($tree['SPECIESTYPE'])) {
            if($tree['SPECIESTYPE'] == "Not Known") {
                $speciestype_id = 'NULL';
            } else {
                $speciestype_id = add_speciesType($conn, $tree['SPECIESTYPE']);
            }
        }

        if(!empty($tree['SPECIES'])) {
            if($tree['SPECIES'] == "Not Known") {
                $species_id = 'NULL';
            } else {
                $species_id = add_species($conn, $tree['SPECIES']);
            }    
        }

        if(!empty($tree['AGE'])) {
            $age_id = add_age($conn, $tree['AGE']);
        } else {
            $age_id = 'NULL';
        }

        if(!empty($tree['TREESURROUND'])) {
            $treesurround_id = add_treeSurround($conn, $tree['TREESURROUND']);
        }

        if(!empty($tree['VIGOUR'])) {
            $vigour_id = add_vigour($conn, $tree['VIGOUR']);
        }

        if(!empty($tree['CONDITION'])) {
            $condition_id = add_condition($conn, $tree['CONDITION']);
        }

        if(empty($tree['TREETAG'])) {
            $treetag = 'NULL';  
        } else {
            $treetag = $tree['TREETAG'];
        }
        
        $description = $tree['DESCRIPTION'];
        $diameter = $tree['DIAMETER'];
        $spreadradius = $tree['SPREADRADIUS'];
        $longitude = $tree['LONGITUDE'];
        $latitude = $tree['LATITUDE'];
        $treeheight = $tree['TREEHEIGHT'];

        $tree_insert_query = "INSERT INTO `TC_tree` 
        (`TreeTag`, `TREETYPEID`, `SPECIESTYPEID`, `SPECIESID`, `AGEID`, `TREESURROUNDID`, `VIGOURID`, `CONDITIONID`, `Description`,
        `Diameter`, `SpreadRadius`, `Longitude`, `Latitude`, `Height`) 
        VALUES ({$treetag},{$typeoftree_id}, {$speciestype_id}, {$species_id}, {$age_id}, {$treesurround_id}, {$vigour_id}, {$condition_id}, '{$description}',
         {$diameter}, {$spreadradius}, {$longitude}, {$latitude}, $treeheight) ";

        insert_query($conn, $tree_insert_query);
        
    }

    
    // get the details of the tree based on the treeid
    function fetch_tree($conn, $treeid) {

        $treeid = mysqli_escape_string($conn, $treeid);

        // fetch tree query
        $FETCH_TREE_QUERY =  "SELECT * FROM TC_tree WHERE TREEID = {$treeid}";
        $result = select_query($conn, $FETCH_TREE_QUERY);

        if($result -> num_rows > 0 ) {

            // get the results as array
            $row = $result -> fetch_assoc();

            // return the row
            return $row;

        }
    }


?>