<?php

    // Verify if the user is authenticated
    function verify_user($conn, $user_id, $api_key) {


        $VERIFY_USER_QUERY = "SELECT USERID FROM TC_user WHERE USERID = ${user_id} AND api_key = '${api_key}'";

        $verified_user_id = select_query($conn, $VERIFY_USER_QUERY);

        if($verified_user_id -> num_rows == 1) {
            return true;
        } 
        
        return false;
    }

    function get_usertypeid($conn, $type) {
        $usertypeid_Query = "SELECT USERTYPEID FROM TC_userType WHERE UserType = \"{$type}\"";
        $res_usertypeid = select_query($conn, $usertypeid_Query);

        if($res_usertypeid -> num_rows > 0) {
            return $res_usertypeid -> fetch_assoc()['USERTYPEID'];
        }
    }

    function get_usertype($conn, $usertypeid) {
        $usertype_query = "SELECT UserType FROM TC_userType WHERE USERTYPEID = $usertypeid";
        $usertype = select_query($conn, $usertype_query);

        if($usertype->num_rows > 0) {
            return $usertype->fetch_assoc()['UserType'];
        }
    }

    function verify_admin($conn, $user_id) {

        // get admins user type id
        $admin_usertypeid = get_usertypeid($conn, "Admin");

        $VERIFY_ADMIN_QUERY = "SELECT USERID FROM TC_user WHERE USERID = $user_id AND USERTYPEID = $admin_usertypeid";
        $verified_admin_id = select_query($conn, $VERIFY_ADMIN_QUERY);

        if($verified_admin_id -> num_rows = 1) {
            return true;
        }

        return false;
    }
?>
