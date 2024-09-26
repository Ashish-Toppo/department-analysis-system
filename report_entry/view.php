<?php

// session
session_start();

// load all necessary functions
include_once("../config/database.php");
include_once("../includes/functions.php");



// Fetch form details
$form_id = $_GET['id'];
$form_query = "SELECT * FROM forms WHERE id = $form_id";
$form_result = $conn->query($form_query);
$form = $form_result->fetch_assoc();

// Fetch sections
$sections_query = "SELECT * FROM sections WHERE form_id = $form_id";
$sections_result = $conn->query($sections_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $form['title']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2><?php echo $form['title']; ?></h2>
    <p><?php echo $form['description']; ?></p>

    <div class="list-group">
        <?php while ($section = $sections_result->fetch_assoc()): ?>
            <div class="list-group-item mt-3">
                <h4><?php echo $section['title']; ?></h4>
                <p><?php echo $section['description']; ?></p>

                <div id="section-<?php echo $section['id']; ?>" class="mb-4">
                    <?php
                    // Fetch subsections
                    $subsections_query = "SELECT * FROM subsections WHERE section_id = " . $section['id'];
                    $subsections_result = $conn->query($subsections_query);
                    ?>
                    <form action="save_response.php" method="POST" class="mb-3">
                        <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                        <?php while ($subsection = $subsections_result->fetch_assoc()): ?>
                            <div class="ml-3">
                                <h5><?php echo $subsection['title']; ?></h5>
                                <p><?php echo $subsection['description']; ?></p>

                                <?php
                                // Fetch questions
                                $questions_query = "SELECT * FROM questions WHERE subsection_id = " . $subsection['id'];
                                $questions_result = $conn->query($questions_query);
                                while ($question = $questions_result->fetch_assoc()):
                                ?>
                                    <div class="form-group ml-4">
                                        <label><?php echo $question['question_text']; ?></label>
                                        <?php if ($question['question_type'] == 'text'): ?>
                                            <input type="text" class="form-control" name="response[<?php echo $question['id']; ?>]">
                                        <?php elseif ($question['question_type'] == 'file'): ?>
                                            <input type="file" class="form-control" name="response[<?php echo $question['id']; ?>]">
                                        <?php elseif ($question['question_type'] == 'textfile'): ?>
                                            <input type="text" class="form-control" name="response[<?php echo $question['id']; ?>]" placeholder="Enter your text here">
                                            <input type="file" class="form-control mt-2" name="response_file[<?php echo $question['id']; ?>]">
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endwhile; ?>
                        <button type="submit" class="btn btn-primary">Submit Section</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>