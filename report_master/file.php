<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editable Dynamic Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .container-fluid {
            width: 100%;
        }

        .form-container {
            width: 90%;
            max-width: 1000px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            box-sizing: border-box;
        }

        .form-section {
            position: relative;
            padding: 20px;
            margin: 60px 0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .question {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            position: relative;
        }

        .section-buttons, .question-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
        }

        .form-section:hover .section-buttons, .question:hover .question-buttons {
            display: block;
        }

        .section-buttons button, .question-buttons button {
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            line-height: 1;
            margin: 0 2px;
        }

        .add-question, .add-sub-question {
            background-color: #007bff;
            color: white;
        }

        .delete-section, .delete-question {
            background-color: #dc3545;
            color: white;
        }

        .sub-question-separator {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .evaluation-footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .editable-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #ccc;
            background: transparent;
        }

        .editable-input:focus {
            border-bottom: 1px solid #000;
            outline: none;
        }

        .sub-question {
            position: relative;
            margin-top: 20px;
        }

        .sub-question-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
        }

        .sub-question:hover .sub-question-buttons {
            display: block;
        }

        .sub-question-buttons button {
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            line-height: 1;
            margin: 0 2px;
            background-color: #dc3545;
            color: white;
        }

        .remove-sub-question {
            background-color: #dc3545;
            color: white;
        }

    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="handle.php" method="POST" enctype="multipart/form-data" class="form-container">
                <input type="text" id="form-title" class="form-control mb-3 editable-input" value="Generic Form Title" placeholder="Enter Form Title">
                <textarea id="form-description" class="form-control mb-4 editable-input" rows="2" placeholder="Enter Form Description">This is the description of the form.</textarea>
                
                <div id="sections-container"></div>

                <button id="add-section" type="button" class="btn btn-primary mt-4">Add Section</button>
                <input type="submit" id="submit-form" class="btn btn-success btn-block mt-4" />
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('add-section').addEventListener('click', function () {
        addSection();
    });

    let sectionCount = 0;
    let questionCounts = {};
    let subQuestionCounts = {};

    function addSection() {
        sectionCount++;
        questionCounts[sectionCount] = 0;
        const sectionLabel = "Section " + sectionCount;

        const sectionHTML = `
            <div class="form-section" id="section-${sectionCount}">
                <input type="text" class="form-control editable-input mb-2" id="section-title-${sectionCount}" value="${sectionLabel}" placeholder="Enter Section Title">
                <textarea class="form-control editable-input mb-2" id="section-desc-${sectionCount}" placeholder="Enter Section Description"></textarea>

                <div id="questions-container-${sectionCount}"></div>

                <div class="section-buttons">
                    <button class="btn btn-secondary btn-sm add-question" type="button" onclick="addQuestion(${sectionCount})">+</button>
                    <button class="btn btn-danger btn-sm delete-section" type="button" onclick="removeSection(${sectionCount})">×</button>
                </div>
            </div>
        `;

        $('#sections-container').append(sectionHTML);
    }

    function addQuestion(sectionId) {
        questionCounts[sectionId]++;
        let questionCount = questionCounts[sectionId];
        subQuestionCounts[`${sectionId}-${questionCount}`] = 0;

        const questionLabel = sectionId + "-" + questionCount;

        const questionHTML = `
            <div class="question" id="question-${sectionId}-${questionCount}">
                <input type="text" class="form-control editable-input mb-2" id="question-title-${sectionId}-${questionCount}" value="Question ${questionLabel}" placeholder="Enter Question Title">
                <textarea class="form-control mb-2" id="question-description-${sectionId}-${questionCount}" placeholder="Enter a description for the question"></textarea>

                <div id="sub-questions-container-${sectionId}-${questionCount}"></div>

                <div class="evaluation-section mt-4">
                    <label class="form-label">Evaluation Type:</label>
                    <select class="form-control mb-2" id="question-evaluation-type-${sectionId}-${questionCount}">
                        <option value="absolute">Absolute</option>
                        <option value="rubric">Rubric</option>
                    </select>

                    <label class="form-label">Max Points:</label>
                    <input type="number" class="form-control mb-2" placeholder="Enter maximum points" id="question-max-points-${sectionId}-${questionCount}">
                </div>

                <div class="question-buttons">
                    <button class="add-sub-question" type="button" onclick="addSubQuestion(${sectionId}, ${questionCount})">+</button>
                    <button class="delete-question" type="button" onclick="removeQuestion(${sectionId}, ${questionCount})">×</button>
                </div>
            </div>
        `;

        $('#questions-container-' + sectionId).append(questionHTML);
    }
    


    function addSubQuestion(sectionId, questionId) {
        subQuestionCounts[`${sectionId}-${questionId}`]++;
        let subQuestionCount = subQuestionCounts[`${sectionId}-${questionId}`];

        const subQuestionLabel = sectionId + "-" + questionId + "-" + subQuestionCount;

        const subQuestionHTML = `
            <div class="sub-question" id="sub-question-${sectionId}-${questionId}-${subQuestionCount}" style="margin-left: 15px">
                <label for="sub-question-type-${sectionId}-${questionId}-${subQuestionCount}">Sub Question ${subQuestionLabel}</label>
                <select class="form-control sub-question-type" id="sub-question-type-${sectionId}-${questionId}-${subQuestionCount}">
                    <option value="file">File Upload</option>
                    <option value="textfile">Text with File Upload</option>
                    <option value="tabular">Tabular</option>
                </select>

                <div class="mt-2">
                    <input type="text" id="sub-question-input-${sectionId}-${questionId}-${subQuestionCount}" class="form-control" placeholder="Enter your sub-question">
                </div>

                <div class="sub-question-buttons">
                    <button class="remove-sub-question" type="button" onclick="removeSubQuestion(${sectionId}, ${questionId}, ${subQuestionCount})">×</button>
                </div>
            </div>
        `;

        $('#sub-questions-container-' + sectionId + "-" + questionId).append(subQuestionHTML);
    }

    function removeSection(sectionId) {
        $('#section-' + sectionId).remove();
    }

    function removeQuestion(sectionId, questionId) {
        $('#question-' + sectionId + '-' + questionId).remove();
    }

    function removeSubQuestion(sectionId, questionId, subQuestionId) {
        $('#sub-question-' + sectionId + '-' + questionId + '-' + subQuestionId).remove();
    }
</script>
</body>
</html>
