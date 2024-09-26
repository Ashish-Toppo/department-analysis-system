<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the signed-in user is admin or not
// check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 

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
    <title>Department Assessment Form</title>
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
        /* Style for the signout button */
        .signout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
        }
        .signout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Signout button -->
        <a href="../signout.php" class="signout-btn">Sign Out</a>

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
                                    <td> '. $assessment['id'] .': '. $assessment['title'] .' </td>
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
    <a href="./create.php?csrf=<?php echo $csrfToken; ?>" class="floating-btn">+</a>

    <!-- JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
