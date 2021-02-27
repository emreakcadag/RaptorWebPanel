<?php
if (function_exists('login')){
    if(login()!=true) logout();
} else {
    header('Location: ../login.php');
}
