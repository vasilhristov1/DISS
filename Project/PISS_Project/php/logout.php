<?php
    session_start();

    if ($_SESSION) {
        session_unset();
        session_destroy();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
?>