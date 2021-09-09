<?php
// Utitlies for update function

function get_update($conn, $updateid) {

    // Create an empty array
    $update = array();

    $FETCH_UPDATE_DETAILS_QUERY = "SELECT * FROM TC_update WHERE UPDATEID = $updateid";
    $update_result = select_query($conn, $FETCH_UPDATE_DETAILS_QUERY);


    if ($update_result->num_rows > 0) {

        $update_row = $update_result->fetch_assoc();

        $update['title'] = $update_row['Title'];
        $update['description'] = $update_row['Description'];
        $update['dateadded'] = $update_row['Dateadded']; // might wanna change the data type

        $update['posts'] = array();
        // Fetch the images related to the update
        $FETCH_IMAGES_QUERY = "SELECT * FROM TC_image WHERE UPDATEID = $updateid";
        $images_result = select_query($conn, $FETCH_IMAGES_QUERY);

        // Check if the update has any images associated with it
        if ($images_result->num_rows > 0) {

            while ($image_row = $images_result->fetch_assoc()) {
                $image = array();
                $image['imgURL'] = $image_row['ImageURL'];
                $image['caption'] = $image_row['Caption'];

                // Add the image to the updates
                array_push($update['posts'], $image);
            }
        }
    }

    return $update;
}
