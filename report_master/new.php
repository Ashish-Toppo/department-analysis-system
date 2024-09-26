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
        <form id="dynamicForm">
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
