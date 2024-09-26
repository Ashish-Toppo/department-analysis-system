
<?php
session_start();

// load all necessary functions
include_once("../config/database.php");
include_once("../includes/functions.php");

$mysqli = new mysqli($host, $username, $password, $dbname);


// Insert form title and description
$form_title = $mysqli->real_escape_string($_POST['form_title']);
$form_description = $mysqli->real_escape_string($_POST['form_description']);

$insert_form_query = "INSERT INTO forms (title, description) VALUES (?, ?)";
$stmt = $mysqli->prepare($insert_form_query);
$stmt->bind_param("ss", $form_title, $form_description);
$stmt->execute();
$form_id = $stmt->insert_id; // Get the ID of the newly created form

$stmt->close();

// Loop through sections, subsections, and questions
if (isset($_POST['sections'])) {
    foreach ($_POST['sections'] as $sectionIndex => $section) {
        $section_title = $mysqli->real_escape_string($section['title']);
        $section_desc = $mysqli->real_escape_string($section['description']);

        // Insert section
        $insert_section_query = "INSERT INTO sections (form_id, title, description) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($insert_section_query);
        $stmt->bind_param("iss", $form_id, $section_title, $section_desc);
        $stmt->execute();
        $section_id = $stmt->insert_id; // Get the ID of the newly created section

        // Loop through subsections
        if (isset($section['subsections'])) {
            foreach ($section['subsections'] as $subsectionIndex => $subsection) {
                $subsection_title = $mysqli->real_escape_string($subsection['title']);
                $subsection_desc = $mysqli->real_escape_string($subsection['description']);

                // Insert subsection
                $insert_subsection_query = "INSERT INTO subsections (section_id, title, description) VALUES (?, ?, ?)";
                $stmt = $mysqli->prepare($insert_subsection_query);
                $stmt->bind_param("iss", $section_id, $subsection_title, $subsection_desc);
                $stmt->execute();
                $subsection_id = $stmt->insert_id; // Get the ID of the newly created subsection

                // Loop through questions
                if (isset($subsection['questions'])) {
                    foreach ($subsection['questions'] as $questionIndex => $question) {
                        $question_text = $mysqli->real_escape_string($question['text']);
                        $question_type = $mysqli->real_escape_string($question['type']);

                        // Insert question
                        $insert_question_query = "INSERT INTO questions (subsection_id, question_text, question_type) VALUES (?, ?, ?)";
                        $stmt = $mysqli->prepare($insert_question_query);
                        $stmt->bind_param("iss", $subsection_id, $question_text, $question_type);
                        $stmt->execute();
                    }
                }
            }
        }
    }
}

$stmt->close();
$mysqli->close();

// Redirect to a success page
header("Location: form_creation_success.php");
exit();
?>