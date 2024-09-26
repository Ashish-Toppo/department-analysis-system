<?php

session_start();

// load all necessary files
include_once("../config/config.php");
include_once("../config/Database.php");
include_once("../includes/functions.php");

// generate csrf token
generateCsrfToken();

// get the form id 
$form_id = $_GET['id'];

// get details of the form
$database = new Database();

$form_details = $database->setTable('forms')->fetch(['title', 'description'])->where('id = ?', [$form_id])->run()[0];
$database->resetQuery();

// get deatils of sections in the form
$sections = $database->setTable('sections')->fetch()->where('form_id = ?', [$form_id])->run();
$database->resetQuery();

// print("<pre>"); print_r($sections); die;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Example - Question List</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <style>
    .section-title {
      background-color: #007bff;
      color: white;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 30px;
    }
    .subsection {
      margin-bottom: 40px;
    }
    .section-body {
      margin-bottom: 50px;
    }
    .subsection h5 {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="container my-5">
  <!-- Form Title and Description -->
  <div class="text-center mb-5">
    <h2 class="form-title"> <?= $form_details['title'] ?> </h2>
    <p class="form-description">
      <?= $form_details['description'] ?>
    </p>
  </div>

  <!-- Sections added dynamically -->
   <?php foreach($sections as $section): ?>
        <div class="form-section mb-4">
            <div class="section-title">
                <h4> <?= $section['title'] ?> </h4>
                <p class="form-description">
                    <?= $section['description'] ?>
                </p>
            </div>
            <div class="section-body pl-4">

            <?php
                // fetch subsection details
                $section_id = $section['id'];

                $subsections = $database->setTable('subsections')->fetch()->where('section_id = ?', [$section_id])->orderBy('position')->run();
                $database->resetQuery();
            ?>
            
            <!-- Subsections added dynamically -->
            <?php foreach($subsections as $subsection): ?>
                <div class="subsection">
                    <a href="form_2.php?id=<?= $subsection['id'] ?>&csrf=<?= $_SESSION['csrf_token'] ?>"><h5> <?= $subsection['title'] ?> </h5></a>
                    <p class="section-description">
                        <?= $subsection['description'] ?>
                    </p>
                </div>
            <?php endforeach; ?>
            
            </div>
        </div>
    <?php endforeach; ?>

  

  
</div>

<!-- Optional JavaScript -->
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>
