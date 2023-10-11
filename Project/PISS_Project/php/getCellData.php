<?php
    require_once "dbTable.php";

    $errors = [];
    $response = [];
    if (isset($_GET)) {
        $db = new TableDatabase();

        $query = $db->getSavedTableInfo();
        if ($query["success"]) {
            $data = $query["data"]->fetchAll(PDO::FETCH_ASSOC);
            if (!$data) {
                $response = ["success" => true, "data" => false];
            } else {
                $response = ["success" => true, "data" => $data];
            }
        } else {
            $response = ["success" => false, "data" => $query["error"]->fetch(PDO::FETCH_ASSOC)];
        }
    }
    
    echo json_encode($response);
?>