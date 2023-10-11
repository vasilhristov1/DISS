<?php
    session_start();
    header('Content-Type: application/json');
    $data = [];
    if (isset($_GET)) {
        if (isset($_SESSION['username']) && isset($_SESSION['email']) && isset($_SESSION['type'])) {
            $data = ["success" => true, "username" => $_SESSION['username'], "email" => $_SESSION['email'], "type" => $_SESSION['type']];
        } else {
            $data = ["success" => true];
        }
    }

    echo json_encode($data);
?>