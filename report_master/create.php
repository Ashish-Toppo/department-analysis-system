<?php 

// start session
session_start();

// include all necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// check if the sigined in user is admin or not
// check_if_user_admin(); // destroy all session and redirect to signin page if the user is not admin 


// if create new assessment is clicked then
if (isset($_POST['createAssessment'])) {

// data from frontend
$data = $_POST;

// Initialize questions array
$questions = [];
  

$i = 1;
while (isset($data["question_$i"])) {
    $question = [
        'question' => $data["question_$i"],
        'type' => $data["question_type_$i"],
        'required' => isset($data["required_$i"]) ? true : false,
        'allow_upload' => $data["allow_upload_$i"] === 'yes',
        'max_files' => $data["max_files_$i"] ?? null,
        'max_file_size' => $data["max_file_size_$i"] ?? null,
        'upload_sample' => $data["upload_sample_$i"] === 'yes',
        'sample_files' => $data["sample_files_$i"] ?? [],
    ];

    // Handle options for multiple-choice questions
    if ($question['type'] === 'multiple') {
        $question['options'] = [];
        $j = 1;
        while (isset($data["option_{$i}_$j"])) {
            $question['options'][] = $data["option_{$i}_$j"];
            $j++;
        }
    }

    // Add the processed question to the questions array
    $questions[] = $question;
    $i++;
}

// function to handle file upload
function handleFileUpload($file, $destinationDir) {
  $targetFile = $destinationDir . basename($file["name"]);
  if (move_uploaded_file($file["tmp_name"], $targetFile)) {
      return $targetFile;
  } else {
      throw new Exception("File upload failed for " . $file["name"]);
  }
}

// Ensure the upload directory exists
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// data comes from $_POST
$title = $conn->real_escape_string($_POST['title']);
$description = $conn->real_escape_string($_POST['description']);

// Start a transaction to ensure all queries succeed
$conn->begin_transaction();

try {
  // Insert the assessment into the `assessments` table
  $uuid = generateUniqueId();
  $insertAssessment = $conn->prepare("INSERT INTO assessments (uuid, title, description) VALUES (?, ?, ?)");
  $insertAssessment->bind_param("sss", $uuid, $title, $description);
  $insertAssessment->execute();
  $assessment_id = $conn->insert_id; // Get the auto-incremented id

  // Iterate over the questions array and insert into `questions` table
  $i = 1;
  while (isset($_POST["question_$i"])) {
      // Generate unique UUID for each question
      $question_uuid = generateUniqueId();

      // Sanitize the question fields
      $question_text = $conn->real_escape_string($_POST["question_$i"]);
      $question_type = $conn->real_escape_string($_POST["question_type_$i"]);
      $is_required = isset($_POST["required_$i"]) ? 1 : 0;
      $allow_upload = $_POST["allow_upload_$i"] === 'yes' ? 1 : 0;
      $max_files = isset($_POST["max_files_$i"]) ? (int)$_POST["max_files_$i"] : NULL;
      $max_file_size = isset($_POST["max_file_size_$i"]) ? (int)$_POST["max_file_size_$i"] : NULL;
      $upload_sample = $_POST["upload_sample_$i"] === 'yes' ? 1 : 0;

      // Insert question into the `questions` table
      $insertQuestion = $conn->prepare("
          INSERT INTO questions (
              uuid, assessment_id, question_text, question_type, is_required, 
              allow_upload, max_files, max_file_size, upload_sample
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
      $insertQuestion->bind_param(
          "sissiiiii",
          $question_uuid, $assessment_id, $question_text, $question_type, $is_required, 
          $allow_upload, $max_files, $max_file_size, $upload_sample
      );
      $insertQuestion->execute();
      $question_id = $conn->insert_id; // Get the auto-incremented id

      // Insert options if it's a multiple-choice question
      if ($question_type === 'multiple') {
          $j = 1;
          while (isset($_POST["option_{$i}_$j"])) {
              // Generate unique UUID for each option
              $option_uuid = generateUniqueId();
              $option_text = $conn->real_escape_string($_POST["option_{$i}_$j"]);

              // Insert the option into the `options` table
              $insertOption = $conn->prepare("INSERT INTO options (uuid, question_id, option_text) VALUES (?, ?, ?)");
              $insertOption->bind_param("sss", $option_uuid, $question_id, $option_text);
              $insertOption->execute();
              $j++;
          }
      }

      // Insert uploads if file uploads are allowed
      if ($allow_upload && isset($_FILES["sample_files_$i"])) {
          foreach ($_FILES["sample_files_$i"]["tmp_name"] as $index => $tmp_name) {
              // Handle file upload
              $file = [
                  "name" => $_FILES["sample_files_$i"]["name"][$index],
                  "tmp_name" => $tmp_name,
                  "error" => $_FILES["sample_files_$i"]["error"][$index],
                  "size" => $_FILES["sample_files_$i"]["size"][$index]
              ];
              $file_path = handleFileUpload($file, $uploadDir);

              // Generate unique UUID for each upload
              $upload_uuid = generateUniqueId();

              // Insert the file path into the `uploads` table
              $insertUpload = $conn->prepare("INSERT INTO uploads (uuid, question_id, file_path) VALUES (?, ?, ?)");
              $insertUpload->bind_param("sss", $upload_uuid, $question_id, $file_path);
              $insertUpload->execute();
          }
      }

      $i++;
  }

  // Commit the transaction if everything is successful
  $conn->commit();

  // Redirect to list.php after successful execution
  header("Location: ./list.php");
  exit();  // Ensure no further script execution after redirection

} catch (Exception $e) {
  // Roll back the transaction if something goes wrong
  $conn->rollback();
  echo "Error: " . $e->getMessage();
}

// Close the statements if they exist
if (isset($insertAssessment)) $insertAssessment->close();
if (isset($insertQuestion)) $insertQuestion->close();
if (isset($insertOption)) $insertOption->close();
if (isset($insertUpload)) $insertUpload->close();

$conn->close();



}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Form with Fixed and Dynamic Fields</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .section {
            position: relative;
        }
        .delete-section-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .file-upload-preview img {
            max-width: 100px;
            margin-right: 10px;
        }
        .file-upload-preview {
            display: flex;
            flex-wrap: wrap;
        }
        .file-upload-preview .file-preview {
            position: relative;
            margin-right: 10px;
        }
        .file-upload-preview .file-preview img {
            max-width: 100px;
        }
        .remove-file-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Dynamic Form with Fixed and Dynamic Fields</h2>

        <!-- Form container -->
        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Fixed fields -->
            <div class="form-group">
                <label>Title of Assessment</label>
                <input type="text" class="form-control" name="title" placeholder="Enter title of assessment" required>
            </div>
            <div class="form-group">
                <label>Description of Assessment</label>
                <textarea class="form-control" name="description" placeholder="Enter description of assessment" rows="4" required></textarea>
            </div>

            <!-- Sections will be added dynamically here -->
             <div id="dynamicForm">

             </div>

             <input type="submit" class="btn btn-success" name="createAssessment">
        </form>

        <!-- Button to add new section -->
        <div class="text-center my-3">
            <button id="addSectionBtn" class="btn btn-primary">Add New Section</button>
        </div>
    </div>

    <script>
        let sectionCount = 0;

        // Function to add a new section
        document.getElementById('addSectionBtn').addEventListener('click', function() {
            sectionCount++;
            const section = document.createElement('div');
            section.classList.add('section', 'mb-4', 'p-3', 'border', 'border-secondary');
            section.setAttribute('data-section-id', sectionCount);

            section.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm delete-section-btn">Delete Section</button>
                <div class="form-group">
                    <label>Question</label>
                    <input type="text" class="form-control" name="question_${sectionCount}" placeholder="Enter your question" required>
                </div>

                <div class="form-group">
                    <label>Question Type</label>
                    <select class="form-control question-type" name="question_type_${sectionCount}" required>
                        <option value="answer">Answer Type</option>
                        <option value="multiple">Multiple Choice</option>
                    </select>
                </div>

                <!-- Multiple choice options, hidden by default -->
                <div class="multiple-choice-options" style="display:none;">
                    <button type="button" class="btn btn-info mb-2 add-option-btn">Add Option</button>
                    <div class="options-container"></div>
                </div>

                <div class="form-group">
                    <label>Allow file upload?</label>
                    <select class="form-control allow-file-upload" name="allow_upload_${sectionCount}" required>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>

                <!-- File upload options, hidden by default -->
                <div class="file-upload-options" style="display:none;">
                    <div class="form-group">
                        <label>Max Files</label>
                        <input type="number" class="form-control" name="max_files_${sectionCount}" placeholder="Enter max number of files">
                    </div>
                    <div class="form-group">
                        <label>Max File Size (MB)</label>
                        <input type="number" class="form-control" name="max_file_size_${sectionCount}" placeholder="Enter max file size in MB">
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Sample?</label>
                    <select class="form-control upload-sample" name="upload_sample_${sectionCount}" required>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>

                <!-- Sample file upload options, hidden by default -->
                <div class="sample-upload-options" style="display:none;">
                    <div class="form-group">
                        <label>Upload Sample Files</label>
                        <input type="file" class="form-control-file" name="sample_files_${sectionCount}[]" multiple>
                        <div class="file-upload-preview"></div>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="required_${sectionCount}">
                    <label class="form-check-label">Required</label>
                </div>

                <hr>
            `;

            document.getElementById('dynamicForm').appendChild(section);

            // JavaScript array to keep track of uploaded files
            let fileArray = [];

            // Event listener for question type change
            section.querySelector('.question-type').addEventListener('change', function() {
                const selectedType = this.value;
                const multipleChoiceOptions = section.querySelector('.multiple-choice-options');
                if (selectedType === 'multiple') {
                    multipleChoiceOptions.style.display = 'block';
                } else {
                    multipleChoiceOptions.style.display = 'none';
                }
            });

            // Event listener for adding options in multiple choice
            section.querySelector('.add-option-btn').addEventListener('click', function() {
                const optionContainer = section.querySelector('.options-container');
                const optionCount = optionContainer.children.length + 1;
                const optionField = document.createElement('div');
                optionField.classList.add('form-group');
                optionField.innerHTML = `<label>Option ${optionCount}</label><input type="text" class="form-control" name="option_${sectionCount}_${optionCount}" placeholder="Enter option ${optionCount}" required>`;
                optionContainer.appendChild(optionField);
            });

            // Event listener for file upload dropdown
            section.querySelector('.allow-file-upload').addEventListener('change', function() {
                const allowFileUpload = this.value;
                const fileUploadOptions = section.querySelector('.file-upload-options');
                if (allowFileUpload === 'yes') {
                    fileUploadOptions.style.display = 'block';
                } else {
                    fileUploadOptions.style.display = 'none';
                }
            });

            // Event listener for upload sample dropdown
            section.querySelector('.upload-sample').addEventListener('change', function() {
                const uploadSample = this.value;
                const sampleUploadOptions = section.querySelector('.sample-upload-options');
                if (uploadSample === 'yes') {
                    sampleUploadOptions.style.display = 'block';
                } else {
                    sampleUploadOptions.style.display = 'none';
                }
            });

            // Event listener for file input change to show selected files
            section.querySelector('input[type="file"]').addEventListener('change', function(event) {
                const fileUploadPreview = section.querySelector('.file-upload-preview');
                const files = Array.from(event.target.files);

                // Keep track of previously selected files
                fileArray = fileArray.concat(files);

                fileUploadPreview.innerHTML = '';
                fileArray.forEach(file => {
                    const filePreview = document.createElement('div');
                    filePreview.classList.add('file-preview');
                    
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = file.name;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.classList.add('remove-file-btn');
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', function() {
                        // Remove file from fileArray
                        fileArray = fileArray.filter(f => f !== file);
                        filePreview.remove();
                        // Remove file from input
                        const dataTransfer = new DataTransfer();
                        fileArray.forEach(f => dataTransfer.items.add(f));
                        section.querySelector('input[type="file"]').files = dataTransfer.files;
                    });
                    
                    filePreview.appendChild(img);
                    filePreview.appendChild(removeBtn);
                    fileUploadPreview.appendChild(filePreview);
                });
            });

            // Event listener for delete button
            section.querySelector('.delete-section-btn').addEventListener('click', function() {
                section.remove();
            });
        });
    </script>
</body>
</html>
