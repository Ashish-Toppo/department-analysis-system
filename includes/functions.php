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

// function to check if the user (any user) is signin in
function check_user_signin () {
    if (!isset($_SESSION['username'])) {
        $_SESSION = []; // empty the session if it has any data
        header('location: ../signin.php'); // redirect to signin page

        die; // stop furthur execution of the page
    }

    else {
        // the signed in user is admin

        // do nothing for now
    }
}

// function to generate csrf token
function generateCsrfToken() {
    // Check if a token already exists, if not generate a new one
    if (empty($_SESSION['csrf_token'])) {
        // Generate a random token
        $token = bin2hex(random_bytes(32)); // 32 bytes = 64 hex characters
        // Store the token in the session
        $_SESSION['csrf_token'] = $token;
    }
    return $_SESSION['csrf_token'];
}

// Function to verify the CSRF token from a form submission
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

