<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the sigined in user is admin or not
check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GForm Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">GForm Admin Dashboard</h1>
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
                        <tr>
                            <td>Form 1: Contact Us</td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewFormModal" data-form-id="1">View</button>
                                <button class="btn btn-danger btn-sm" data-form-id="1">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Form 2: Survey</td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewFormModal" data-form-id="2">View</button>
                                <button class="btn btn-danger btn-sm" data-form-id="2">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Form 3: Registration</td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewFormModal" data-form-id="3">View</button>
                                <button class="btn btn-danger btn-sm" data-form-id="3">Delete</button>
                            </td>
                        </tr>
                        <!-- more forms will be listed here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for viewing form details -->
    <div class="modal fade" id="viewFormModal" tabindex="-1" role="dialog" aria-labelledby="viewFormModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFormModalLabel">Form Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- form details will be displayed here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        // JavaScript code to populate the form list and handle view and delete actions
        // will be added here
    </script>
</body>
</html>