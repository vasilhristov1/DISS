<?php
    require_once 'user.php';

    session_start();

    header('Content-Type: application/json');

    $errors = [];
    $response = [];

    if (isset($_POST)) {
        $data = json_decode($_POST["data"], true);
        
        if(!$data['username']) {
            $errors[] = 'Please eneter username';
        }

        if(!$data['password']) {
            $errors[] = 'Please enter password';
        }

        if($data['username'] && $data['password']) {
            $user = new User($data['username'], $data['password']);
            $isValid = $user->isValid();

            if($isValid['success']) {
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['type'] = $user->getType();
            } else {
                $errors[] = $isValid['error'];
            }
        }
    } else {
        $errors[] = 'Invalid request';
    }

    if($errors) {
        $response = ['success' => false, 'data' => $errors];
    } else {
        $response = ['success' => true];
    }

    echo json_encode($response);
?>