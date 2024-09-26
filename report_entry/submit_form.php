<?php
// Start session
    session_start();

    
    // include all necessary files
    include_once("../config/database.php");
    include_once("../includes/functions.php");

    // get the csrf token 
    // $csrf_token = $_GET['csrf'];


    // verify csrf token
    // if (!verifyCsrfToken($csrf_token)) {
    //     echo "Access Denied :)";
    //     die; // stop furthur execution
    // }

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get assessment ID
        $assessment_id = $_POST['assessment_id'];

        // Loop through each question in the POST data
        foreach ($_POST as $key => $value) {
            // Check if the key matches the format for a question (e.g., question_1)
            if (strpos($key, 'question_') === 0) {
                // Extract question ID from the key (e.g., question_1 becomes 1)
                $question_id = str_replace('question_', '', $key);

                // Sanitize the response
                $response = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

                // Insert the response into the "responses" table
                $stmt = $conn->prepare("INSERT INTO responses (assessment_id, question_id, response) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $assessment_id, $question_id, $response);

                if (!$stmt->execute()) {
                    echo "Error saving response: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        // Handle file uploads
        foreach ($_FILES as $key => $file) {
            if (strpos($key, 'sample_files_') === 0 && !empty($file['name'][0])) {
                // Extract question ID from the file input name (e.g., sample_files_1 becomes 1)
                $question_id = str_replace('sample_files_', '', $key);

                // Loop through all uploaded files for this question
                for ($i = 0; $i < count($file['name']); $i++) {
                    // Get file details
                    $file_name = basename($file['name'][$i]);
                    $file_tmp = $file['tmp_name'][$i];
                    $upload_dir = '../uploads/';
                    
                    // Generate a unique name to prevent overwriting files
                    $unique_name = uniqid() . '_' . $file_name;
                    $file_path = $upload_dir . $unique_name;

                    // Check if file is valid
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        // Insert file path into the "uploads" table
                        $stmt = $conn->prepare("INSERT INTO uploads (question_id, file_path) VALUES (?, ?)");
                        $stmt->bind_param("is", $question_id, $file_path);

                        if (!$stmt->execute()) {
                            echo "Error saving file: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        echo "Error uploading file: " . $file['error'][$i];
                    }
                }
            }
        }

        // Redirect to the form submission success page (or a confirmation page)
        header("Location: ./list.php?assessment_id=" . $assessment_id);
        exit();
    }