<?php


// all necessary functions

// function to check if the signed in user is admin
function check_if_user_admin () {
    if (!isset($_SESSION['username'])) {
        $_SESSION = []; // empty the session if it has any data
        header('location: ../signin.php'); // redirect to signin page

        die; // stop furthur execution of the page
    }

    else if ($_SESSION['username'] !== 'admin') {
        $_SESSION = []; // empty the session if it has any data
        header('location: ../signin.php'); // redirect to signin page

        die; // stop furthur execution of the page
    }

    else {
        // the signed in user is admin

        // do nothing for now
    }
}