<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the sigined in user is admin or not
check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 

// fetch assessment details
$assessments = fetch_query("SELECT * FROM `assessments` WHERE 1 ");

// generate csrf token
$csrfToken = generateCsrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GForm Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Style for the floating button */
        .floating-btn {
            position: fixed;
            bottom: 60px;
            right: 60px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }
        .floating-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Assessments</h1>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Form Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="form-list">

                    <!-- print the column for each assessment -->
                     <?php

                        if (count($assessments) > 0) {
                            foreach($assessments as $assessment) {
                                echo '<tr>
                                    <td> '. $assessment['id'] .': '. $assessment['name'] .' </td>
                                    <td>
                                        <a class="btn btn-primary" href="./edit.php?id='. $assessment['id'] .'&csrf='. $csrfToken .'">Edit</a> 
                                        <a class="btn btn-danger" href="./delete.php?id='. $assessment['id'] .'&csrf='. $csrfToken .'">Delete</a>
                                    </td>
                                </tr>';
                            }
                        } else {
                            echo "
                                <tr>
                                    <td colspan='2' class='text-center'> No Records were found! </td>
                                </tr>
                            ";
                        }
                     
                        
                     ?>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Floating button to create a new assessment -->
    <a href="./create.php" class="floating-btn">+</a>

    <!-- Modal for viewing form details -->
    <!-- <div class="modal fade" id="viewFormModal" tabindex="-1" role="dialog" aria-labelledby="viewFormModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFormModalLabel">Form Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"> -->
                    <!-- form details will be displayed here -->
                <!-- </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        // JavaScript code to populate the form list and handle view and delete actions
        // will be added here
    </script>
</body>
</html>
