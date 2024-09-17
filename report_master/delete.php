<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the sigined in user is admin or not
check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 


/**
 * -- get the assessment id and csrf token
 * -- check csrf token
 * -- delete the assessment
 * -- delete all other data related to the assessment
 * -- redirect back to report_master/list.php
 * 
 */

// if assessment id not defined then show error
if (!isset($_GET['id'])) {
    echo "Access Denied :)";
    die; 
}


// get the csrf token and the assessment id
$assessmentId = $_GET['id'];
$csrf_token = $_GET['csrf'];


// verify csrf token
if (!verifyCsrfToken($csrf_token)) {
    echo "Access Denied :)";
    die; // stop furthur execution
}

// delete all data related to the assessment

// delete the assessment
$deleted = deleteRecord($assessmentId, 'assessments', $conn);
if ($deleted !== true ) {
    // error
    echo "$deleted";
    die;
}
else {
    // deleted successfully
    header("location: ./list.php");
    die; // stop furthur execution if exists
}

