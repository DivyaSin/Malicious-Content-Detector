/**
 * Created by Divya Singh on 12/3/17.
 */

function validateUsername()
{   field = document.getElementById("username").value;
    if (field == "") {
        document.getElementById("userNameError").innerHTML = "No username is entered.";
        return false;
    }
    else if (field.length < 5) {
        document.getElementById("userNameError").innerHTML = "Username must be at least 5 characters.\n"
        return false;
    }
    else if (/[^a-zA-Z0-9_-]/.test(field)) {
        document.getElementById("userNameError").innerHTML = "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"
        return false;

    }
    document.getElementById("userNameError").innerHTML = ""
    return true
}

function validatePassword()
{
    $pass = document.getElementById("pwd").value;
    if ($pass == ""){
        document.getElementById("passwordError").innerHTML =  "No password is entered.";
        return false;
    }
    else if ($pass.length < 6) {
        document.getElementById("passwordError").innerHTML =  "Password must be at least 6 characters.";
        return false;
    }
    else if (!/[a-z]/.test($pass) || ! /[A-Z]/.test($pass) ||!/[0-9]/.test($pass)) {
        document.getElementById("passwordError").innerHTML =  "Password require one each of a-z, A-Z and 0-9.";
        return false;
    }
    document.getElementById("passwordError").innerHTML =  "";
    return true
}

function validate() {
    if(!validateUsername() || !validatePassword()){
        alert("Not Validated");
        return false;
    }
    document.getElementById("Validated").innerHTML = ""
    return true;
}

function validateEmail()
{
    $emailID = document.getElementById("emailid").value;
    if ($emailID == ""){
        document.getElementById("emailError").innerHTML = "No email is entered.\n";
        return false;
    }
    else if (!(($emailID.indexOf(".") > 0) && ($emailID.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test($emailID)) {
        document.getElementById("emailError").innerHTML = "Email address is invalid.\n";
        return false;
    }
    document.getElementById("emailError").innerHTML = "";
    return true;
}

function validateNewUser(){
    if(!validateUsername() || !validatePassword() || !validateEmail()){
        alert("Not Validated");
        return false;
    }
    document.getElementById("Validated").innerHTML = ""
    return true;
}