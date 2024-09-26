<?php

session_start();

// load all necessary files
include_once("../config/config.php");
include_once("../config/Database.php");
include_once("../includes/functions.php");

// verify csrf
$csrf = isset($_GET['csrf']) ? $_GET['csrf'] : '';
if(!verifyCsrfToken($csrf)){
    echo "csrf validation error";
    die;
}

// get the form id 
$subsection_id = $_GET['id'];

// get details of the form
$database = new Database();

$subsection_details = $database->setTable('subsections')->fetch(['title', 'description'])->where('id = ?', [$subsection_id])->run()[0];
$database->resetQuery();



// print("<pre>"); print_r($sections); die;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .file-preview {
            margin-top: 10px;
        }
        .file-preview img {
            max-width: 100px;
            max-height: 100px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <form class="container mt-5">
        <h2> <?= $subsection_details['title'] ?> </h2>
        <div class="details">
            
            <p class="pl-3">
                <?= $subsection_details['description']  ?>
            </p>
        </div>

        <?php
            // fetch all the questions of the subsection
            $questions = $database->setTable('questions')->fetch()->where('subsection_id = ?', [$subsection_id])->run();
            $database->resetQuery();
        ?>
        

        <?php
            // dynamically add question tags
            foreach ($questions as $question): 
        ?>
                <!-- Text Input Field -->
                <?php if ($question['question_type'] == 'text'): ?>
                    <div class="mb-3">
                        <label for="textInput" class="form-label"> <?= $question['question_text'] ?> </label>
                        <input type="text" class="form-control" id="textInput" placeholder="Enter text">
                    </div>
                <?php elseif ($question['question_type'] == 'file'): ?>
                    <div class="mb-3">
                        <label for="multiFileUpload" class="form-label"> <?= $question['question_text'] ?> </label>
                        <input type="file" class="form-control" id="multiFileUpload" multiple>
                        <div class="file-preview mt-2" id="multiFilePreview"></div>
                    </div>
                <?php elseif ($question['question_type'] == 'file'): ?>
                    <div class="mb-3">
                        <label for="textFileUpload" class="form-label"> <?= $question['question_text'] ?> </label>
                        <input type="text" class="form-control mb-2" id="textFileInput" placeholder="Enter text">
                        <input type="file" class="form-control" id="fileInput">
                    </div>
                <?php elseif ($question['question_type'] == 'tabular'): ?>
                        <?php 
                            // get all table headers
                            $columns = $database->setTable('headers')->fetch()->where('question_id = ?', [$question['id']])->where('header_type = ?', ['column'])->orderBy('sort_order')->run();
                            $rows = $database->setTable('headers')->fetch()->where('question_id = ?', [$question['id']])->where('header_type = ?', ['row'])->orderBy('sort_order')->run(); 
                        ?>

                        <div class="mb-3">
                            <label for="tabularEntry" class="form-label"> <?= $question['question_text'] ?> </label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Row/Col</th>

                                        <?php foreach ($columns as $column): ?>
                                            <th> <?= $column['header_name'] ?> </th>
                                        <?php endforeach; ?>
                                        
                                        <th> Uploads (if required) </th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <th> <?= $row['header_name'] ?> </th>

                                        <?php for ($i = 0; $i < count($columns); $i++): ?>
                                            <td><textarea class="form-control" placeholder="Enter text"></textarea></td>
                                        <?php endfor; ?>

                                        <td><input type="file" class="form-control"></td>
                                    </tr>
                                <?php endforeach; ?>
                <?php endif; ?>
        
        <?php endforeach; ?>
        

        <!-- Tabular Data Entry with Text and File Uploads -->
        
        
                </tbody>
            </table>
        </div>

        <input type="submit" value="submit">
        
    </form>     
                    
                
                    
                        
                    

    <script>
        $(document).ready(function() {
        let fileList = []; // Array to store selected files

        // Handle multiple file preview and remove option
        $('#multiFileUpload').on('change', function() {
            const files = $(this)[0].files;
            const previewContainer = $('#multiFilePreview');
            
            // Loop through the newly selected files
            if (files.length > 0) {
                $.each(files, function(index, file) {
                    // Check if file already exists in the fileList array
                    if (!fileList.some(f => f.name === file.name && f.size === file.size)) {
                        fileList.push(file); // Add file to the array
                        
                        // Create file name display element
                        const fileName = $('<span>').text(file.name).css({'display': 'inline-block', 'margin': '0'});
                        const removeBtn = $('<button>').text('x').addClass('btn btn-danger btn-sm ms-2').click(function() {
                            fileName.remove();
                            removeBtn.remove();
                            // Remove file from fileList
                            fileList = fileList.filter(f => f.name !== file.name || f.size !== file.size);
                        });
                        
                        previewContainer.append(fileName).append(removeBtn); // Append file name and remove button
                    }
                });
            }
        });
    });


    </script>
</body>
</html>
