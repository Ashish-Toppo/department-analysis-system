<?php

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the signed-in user is admin or not
// check_user_signin(); // if user is not signed in, destroy session data and redirect to signin page

// Database connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$section_id = $_GET['id'];

// Fetch section title and description
$section_query = "SELECT title, description FROM sections WHERE id = ?";
$section_stmt = $mysqli->prepare($section_query);
$section_stmt->bind_param("i", $section_id);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc();

// Fetch sub-sections and their questions
$subsection_query = "SELECT id, title, description FROM subsections WHERE section_id = ?";
$subsection_stmt = $mysqli->prepare($subsection_query);
$subsection_stmt->bind_param("i", $section_id);
$subsection_stmt->execute();
$subsection_result = $subsection_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($section['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1><?php echo htmlspecialchars($section['title']); ?></h1>
    <p><?php echo htmlspecialchars($section['description']); ?></p>

    <?php while ($subsection = $subsection_result->fetch_assoc()): ?>
        <div class="mb-4">
            <h5><?php echo htmlspecialchars($subsection['title']); ?></h5>
            <p><?php echo htmlspecialchars($subsection['description']); ?></p>

            <form action="submit_response.php" method="POST">
                <input type="hidden" name="subsection_id" value="<?php echo $subsection['id']; ?>">
                
                <?php
                // Fetch questions related to the sub-section
                $question_query = "SELECT id, question_text, question_type FROM questions WHERE subsection_id = ?";
                $question_stmt = $mysqli->prepare($question_query);
                $question_stmt->bind_param("i", $subsection['id']);
                $question_stmt->execute();
                $question_result = $question_stmt->get_result();

                while ($question = $question_result->fetch_assoc()): ?>
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($question['question_text']); ?></label>
                        <?php if ($question['question_type'] == 'text'): ?>
                            <input type="text" class="form-control" name="question_<?php echo $question['id']; ?>" required>
                        <?php elseif ($question['question_type'] == 'textarea'): ?>
                            <textarea class="form-control" name="question_<?php echo $question['id']; ?>" required></textarea>
                        <?php elseif ($question['question_type'] == 'multiple_choice'): ?>
                            <!-- Example for multiple choice, adjust as needed -->
                            <select class="form-control" name="question_<?php echo $question['id']; ?>" required>
                                <option value="">Select an option</option>
                                <option value="Option 1">Option 1</option>
                                <option value="Option 2">Option 2</option>
                                <option value="Option 3">Option 3</option>
                            </select>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
    <?php endwhile; ?>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$section_stmt->close();
$subsection_stmt->close();
// $question_stmt->close();
$mysqli->close();
?>