<?php
    // Add functions related to product brands here

    /**
     * Add/edit brand entry
     * 
     * @param $name Brand name
     * @param $id   Brand ID (edit existing if not null)
     */
    function WriteDB_Brands($name, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));

        if ($id == null) {
            $query = "INSERT INTO brands (name, created_at, updated_at) VALUES('$name', '$time_now', '$time_now');";
        } else {
            $query = "UPDATE brands SET name='$name', updated_at='$time_now' WHERE id='$id';";
        }
        
        if (!$GLOBALS['conn']->query($query)) {
            echo "Brand entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }

    // Delete brand from database
    function DeleteFromDB_Brands($id) {
        $query = "DELETE FROM brands WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }