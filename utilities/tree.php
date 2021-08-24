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

    function get_tree_type($conn, $treetypeid) {

        $treetypeid = mysqli_escape_string($conn, $treetypeid);

        $FIND_TREETYPE = "SELECT * FROM TC_treeType WHERE TREETYPEID = $treetypeid";
        $result = select_query($conn, $FIND_TREETYPE);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['TreeType'];
        } else {
            return NULL;
        }
    }

    function get_species_type($conn, $speciestypeid) {

        $speciestypeid = mysqli_escape_string($conn, $speciestypeid);

        $FIND_SPECIESTYPE = "SELECT * FROM TC_speciesType WHERE SPECIESTYPEID = $speciestypeid";
        $result = select_query($conn, $FIND_SPECIESTYPE);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['SpeciesType'];
        } else {
            return NULL;
        }
    }

    function get_species($conn, $speciesid) {

        $speciesid = mysqli_escape_string($conn, $speciesid);

        $FIND_SPECIES = "SELECT * FROM TC_species WHERE SPECIESID = $speciesid";
        $result = select_query($conn, $FIND_SPECIES);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['Species'];
        } else {
            return NULL;
        }
    }

    function get_age($conn, $ageid) {

            $ageid = mysqli_escape_string($conn, $ageid);

            $FIND_AGE = "SELECT * FROM TC_age WHERE AGEID = $ageid";
            $result = select_query($conn, $FIND_AGE);

            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['Age'];
            } 
        return NULL;
        
    }

    function get_treesurround($conn, $treesurroundid) {

        $treesurroundid = mysqli_escape_string($conn, $treesurroundid);

        $FIND_SURROUND = "SELECT * FROM TC_treeSurround WHERE TREESURROUNDID = $treesurroundid";
        $result = select_query($conn, $FIND_SURROUND);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['TreeSurround'];
        } else {
            return NULL;
        }
    }

    function get_vigour($conn, $vigourid) {

        $vigourid = mysqli_escape_string($conn, $vigourid);

        $FIND_VIGOUR = "SELECT * FROM TC_vigour WHERE VIGOURID = $vigourid";
        $result = select_query($conn, $FIND_VIGOUR);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['Vigour'];
        } else {
            return NULL;
        }
    }

    function get_condition($conn, $conditionid) {

        $conditionid = mysqli_escape_string($conn, $conditionid);

        $FIND_CONDITION = "SELECT * FROM TC_condition WHERE CONDITIONID = $conditionid";
        $result = select_query($conn, $FIND_CONDITION);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['ConditionType'];
        } else {
            return NULL;
        }
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

            //echo "Treeid: <p> {$row['TREEID']} </p>";

            $tree['ID'] = $row['TREEID'];
            $tree['TreeTag'] = $row['TreeTag'];
            $tree['TreeType'] = get_tree_type($conn, $row['TREETYPEID']);
            $tree['SpeciesTye'] = get_species_type($conn, $row['SPECIESTYPEID']);
            $tree['Species'] = get_species($conn, $row['SPECIESID']);
            $tree['Age'] = ($row['AGEID'] == 'NULL') ? 'NULL' : get_age($conn, $row['AGEID']);
            $tree['TreeSurround'] = get_treesurround($conn, $row['TREESURROUNDID']);
            $tree['Vigour'] = get_vigour($conn, $row['VIGOURID']);
            $tree['Condition'] = get_condition($conn, $row['CONDITIONID']);
            $tree['Description'] = $row['Description'];
            $tree['Diameter'] = $row['Diameter'];
            $tree['SpreadRadius'] = $row['SpreadRadius'];
            $tree['Longitude'] = $row['Longitude'];
            $tree['Latitude'] = $row['Latitude'];
            $tree['Height'] = $row['Height'];
            
            // return the row
            return $tree;

        }

        return [];
    }

    function get_id($conn, $table, $column, $value, $id) {

        $table = mysqli_real_escape_string($conn, $table);
        $value = mysqli_real_escape_string($conn, $value);

        $FETCH_ID_QUERY = "SELECT '{$id}' FROM {$table} where {$column} = '{$value}'";
        $result_id = select_query($conn, $FETCH_ID_QUERY);

        if($result_id -> num_rows > 0) {
            return $result_id -> fetch_assoc()[$id];
        }

    }
    function updateTree($conn, $tree) {

        $treetypeid = get_id($conn, 'TC_treeType', 'TreeType', $tree['TreeType'], 'TREETYPEID');
        //echo $treetypeid;
        $speciestypeid = get_id($conn, 'TC_speciesType', 'SpeciesType', $tree['SpeciesType'], 'SPECIESTYPeID');
        $speciesid = get_id($conn, 'TC_species', 'Species', mysqli_real_escape_string($conn, $tree['Species']), 'SPECIESID');
        $ageid = get_id($conn, 'TC_age', 'Age', $tree['Age'], 'AGEID');
        $treeSurroundid = get_id($conn, 'TC_treeSurround', 'TreeSurround', $tree['TreeSurround'], 'TREESURROUNDID');
        $vigourid = get_id($conn, 'TC_vigour', 'Vigour', $tree['Vigour'], 'VIGOURID');
        $conditionid = get_id($conn, 'TC_condition', 'ConditionType', $tree['Condition'], 'CONDITIONID');

        $TREE_UPDATE_QUERY = "UPDATE `TC_tree` SET `TreeTag` = {$tree['TreeTag']}, `TREETYPEID` = {$treetypeid}, `SPECIESTYPEID` = ${speciestypeid},
        `SPECIESID` = ${speciesid},`AGEID` = ${ageid}, `TREESURROUNDID` = ${treeSurroundid}, `VIGOURID` = ${vigourid}, `CONDITIONID` = ${conditionid},
        `Description` = '{$tree['Description']}', `Diameter` = {$tree['Diameter']}, `SpreadRadius` = {$tree['SpreadRadius']},
        `Longitude` = {$tree['Longitude']}, `Latitude` = {$tree['Latitude']},
        `Height` = {$tree['Height']} WHERE `TC_tree`.`TREEID` = {$tree['TREEID']}";

        $result = $conn->query($TREE_UPDATE_QUERY);
        $res = array();
        if(!$result) {
            
            $res['status'] = "Error";
            $res['error'] = $conn->error;
        } else {
            $res['status'] = "Success";
            $res['tree'] = $tree;
        }

        return $res;
            
    }
?>