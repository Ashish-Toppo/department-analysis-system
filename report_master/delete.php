<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the sigined in user is admin or not
check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 

?>