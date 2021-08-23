<?php

    // Verify if the user is authenticated
    function verify_user($conn, $user_id, $api_key) {


        $VERIFY_USER_QUERY = "SELECT USERID FROM TC_User WHERE USERID = ${user_id} AND api_key = ${api_key}";

        $verified_user_id = select_query($conn, $VERIFY_USER_QUERY);

        if($verified_user_id -> num_rows == 0) {
            return false;
        } else if($verified_user_id -> num_rows == 1) {
            return true;
        } else 
            return false;
    }

?>
