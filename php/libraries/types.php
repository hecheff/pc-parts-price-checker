<?php
    // Add functions related to product types here

    /**
     * Add/edit product type entry
     * 
     * @param $name Type name
     * @param $id   Type ID (edit existing if not null)
     */
    function WriteDB_Types($name, $id = null) {
        $time_now = TIMESTAMP_NOW;
        $name = addslashes(strip_tags($name));

        if ($id == null) {
            $query = "INSERT INTO types (name, created_at, updated_at) VALUES('$name', '$time_now', '$time_now');";
        } else {
            $query = "UPDATE types SET name='$name', updated_at='$time_now' WHERE id='$id';";
        }
        
        if (!$GLOBALS['conn']->query($query)) {
            echo "Type entry error.<br>";
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }

    // Delete product type from database
    function DeleteFromDB_Types($id) {
        $query = "DELETE FROM types WHERE id='$id';";
        if(!$GLOBALS['conn']->query($query)) {
            echo("Error description: " . $GLOBALS['conn'] -> error);
        }
    }