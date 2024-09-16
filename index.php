<?php

// start session
session_start();

// include all the necessary files
include_once ("./config/database.php");
include_once ("./includes/functions.php");

// check if the user is signed in 
// if the user is not signed in, then redirect to signin page
if (!isset($_SESSION['username'])) header("location: ./signin.php");