<?php

// session
session_start();

// if the user is admin, then direct to master directory
// if the user is departments, then direct to entry directory
if ($_SESSION['username'] == 'admin') header("location: ./report_master/list.php");