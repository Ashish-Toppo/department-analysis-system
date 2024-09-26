<?php

// load all necessary files
include_once('./_load_all.php');

// check if the user is signed in 
// if the user is not signed in, then redirect to signin page
if (!isset($_SESSION['username'])) header("location: ./signin.php");

else  header('location: ./router.php');