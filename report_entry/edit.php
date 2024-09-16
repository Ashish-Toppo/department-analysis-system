<?php

// session
session_start();

// load all necessary functions
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if user is signed in
check_user_signin(); // if user is not signed in, then destroy session data and redirect to signin page

?>