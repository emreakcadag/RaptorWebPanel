<?php
session_start();
$panel_username = 'root';
$panel_password = 'toor';
    
function login(){
    global $panel_username;
    global $panel_password;
    
    if (isset($_SESSION['username']) and isset($_SESSION['password'])){
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
        if ($username == $panel_username and $password == $panel_password) 
            return true;
    }
    
    return false;

}

function logout(){
    $_SESSION = [];
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    session_unset();
    session_destroy();
    header('Location: login.php');

}
