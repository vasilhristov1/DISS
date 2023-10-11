<?php
    require_once "user.php";

    $errors = [];
    $response = [];
    $data = null;

    if ($_POST) {
        $data = json_decode($_POST["data"], true);

        if (!$data["email"]) {
            $errors[] = "Email is required";
        }
        if (!$data["username"]) {
            $errors[] = "Username is required";
        }

        if (!$data['password']) {
            $errors[] = "Password is required.";
          }
    
        if (!$data['confirmPassword']) {
            $errors[] = "Password confirmation is required.";
        }
    
        if ($data['username'] && $data['password'] && $data['confirmPassword']) {
            if ($data['password'] != $data['confirmPassword']) {
                $errors[] = "Password confirmation does not match password.";
            } else {
                
                $user = new User($data['username'], $data['password']);
                $exists = $user->userExists();
    
                if ($exists) {
                    $errors[] = "Username is already taken.";
                } else {
                    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
                    $user->createUser($passwordHash, $data['email']);
                }
            }
        }
        
        
    } else {
        $erros[] = "Invalid request.";
    }

    if ($errors) {
        $response = ["success" => false, "data" => $errors];
    } else {
        $response = ["success" => true, "data" => $data];
    }
    echo json_encode($response);
    
?>