<?php

    // Common database functionalities needed

    /**
     * Code taken from : https://www.php.net/manual/en/function.trim.php
     * Reference it
     * */

    function trim_value(&$value) {
        $value = trim($value);
    }

    function real_escape_string(&$value) {
        // include the database configuration file
        include("../config/database.php");
        $value = mysqli_real_escape_string($conn, $value);
    }
    
    function insert_query($conn, $insertquery) {
        
        if($conn->query($insertquery) === TRUE) {
            return $conn->insert_id;     
        } else {
            echo "<blockquote>";
            echo "<div> $insertquery </div>";
            echo "<div> Insertion error: ". $conn->error. "</div>";
            echo "</blockquote>";
            return NULL;
        }
        
    }


    function select_query($conn, $selectquery) {

        $result = $conn->query($selectquery);
        if(!$result) {
            echo "Select Error: ".$conn->error;
        } else {
            return $result;
        }
    }
    
    // Get the count of a value in a table
    function get_count($conn, $table, $value) {

    }

    /**
     * checks and returns the id of the value if exists
     */
    function if_exist($conn, $table, $column, $value) {
        
        // probably cleaned by the calling function
        //$value = $conn->real_escape_string($value);

        $query = "SELECT * FROM {$table} WHERE {$column} = '{$value}'";

        $result = select_query($conn, $query);

        if($result->num_rows == 0) {
            echo "<p> $table </p>";
            return NULL;
        } else {
            return $result;
        }
    }

    // Add a single value 
    function add_value($conn, $table, $column, $value, $id) {
        
        // clean the data
        $tablename = mysqli_real_escape_string($conn, $table);
        $columnname = mysqli_real_escape_string($conn,$column);
        
        $value = mysqli_real_escape_string($conn, $value);

        // For debugging purposes
        //echo $tablename. " ". $columnname. " ". $value; 

        $if_exists = if_exist($conn, $table, $column, $value); 
        if ($if_exists == NULL) {
            $query = "INSERT INTO {$tablename}({$columnname}) VALUES('{$value}')";
            return insert_query($conn, $query);
        } else {
            if($if_exists->num_rows == 1) {
                $row = $if_exists->fetch_assoc();
                return $row[$id];
            }
        }


    }
?>