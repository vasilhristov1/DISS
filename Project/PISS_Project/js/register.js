(function() {

    var submitInput = document.getElementById("register");
    submitInput.addEventListener('click', submitForm);
})();

var errorMessage = "";
var doesUserExist = false;
var isValidationSuccessful = true;

// validates registration and sends post request to register.php
function submitForm(event) {

    event.preventDefault();

    var userExistsError = document.querySelector(".userExistsError");
    userExistsError.innerHTML = "";
    var success = document.querySelector("#success");
    success.innerHTML = "";
    isValidationSuccessful = true;
    doesUserExist = false;

    var username = document.querySelector("#username");
    let usernameError = document.querySelector(".usernameError");
    if (!validateInput(username)) {
        usernameError.innerHTML = errorMessage;
        errorMessage = "";
        isValidationSuccessful = false;
        console.log(errorMessage);
    } else {
        usernameError.innerHTML = "";
    }

    var email = document.querySelector("#email");
    let emailError = document.querySelector(".emailError");
    if (!validateInput(email)) {
        emailError.innerHTML = errorMessage;
        errorMessage = "";
        isValidationSuccessful = false;
    } else {
        emailError.innerHTML = "";
    }

    var password = document.querySelector("#password");
    let passwordError = document.querySelector(".passwordError");
    if (!validateInput(password)) {
        passwordError.innerHTML = errorMessage;
        errorMessage = "";
        isValidationSuccessful = false;
    } else {
        passwordError.innerHTML = "";
    }

    var confirmPassword = document.querySelector("#confirm-password");
    let confirmPasswordError = document.querySelector(".confirmPasswordError")
    if (password.value !== confirmPassword.value) {
        confirmPasswordError.innerHTML = "Password and confirm password must match";
        isValidationSuccessful = false;
    } else {
        confirmPasswordError.innerHTML = "";
    }

    const url = "../php/register.php";

    if (isValidationSuccessful) {
        const userData = { email: email.value, username: username.value, password: password.value, confirmPassword: confirmPassword.value };
        sendRequest(url, { method: "POST", data: `data=${JSON.stringify(userData)}` }, load, console.log );
    }
}

function load(response) {
    console.log(response)
    if (response.success) {
        window.location = 'login.html';
        userExistsError.innerHTML = "";
        success.innerHTML = "You are successfully registered";
    } else {
        var errors = document.querySelector('.userExistsError');
        errors.innerHTML = response.data;
    }
}

function validateInput(input) {

    var value = input.value;
    if (input.required && value == "") {
        errorMessage = `${input.placeholder} is a required field`;
        return false;
    }

    switch (input.id) {
        case "username":
            if (value.length !== 0 && value.length < 3) {
                errorMessage = 'Username should contain 3 or more symbols';
                return false;
            } else if (value === "undefined") {
                errorMessage = "Username cannot be undefined";
                return false;
            }
            break;
        case "email":
            if (value.length !== 0 && !/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)$/.test(value)) {
                errorMessage = `e-mail is incorrect`;
                return false;
            }
            break;
        case "password":
            if (value.length !== 0) {
                if (value.length < 8) {
                    errorMessage = `Password should be with 8 or more symbols`;
                    return false;
                } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(value)) {
                    errorMessage = `Password should contain upper and lower case letters and numbers`;
                    return false;
                }
            }
            break;
        default:
            break;
    }
    return true;
}