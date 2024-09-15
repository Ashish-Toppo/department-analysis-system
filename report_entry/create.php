<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GForm User Response</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Department Assessment</h1>
        <div class="row">
            <div class="col-md-12">
                <form id="response-form">
                    <h2 id="form-title">Fill the Information</h2>
                    <div id="form-fields">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control">
                            <div class="input-group mb-3">
                                <input type="file" id="name-images" name="name-images[]" multiple class="form-control-file">
                                <label for="name-images">Upload images (optional)</label>
                                <div id="name-images-preview"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                            <div class="input-group mb-3">
                                <input type="file" id="email-images" name="email-images[]" multiple class="form-control-file">
                                <label for="email-images">Upload images (optional)</label>
                                <div id="email-images-preview"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                            <div class="input-group mb-3">
                                <input type="file" id="phone-images" name="phone-images[]" multiple class="form-control-file">
                                <label for="phone-images">Upload images (optional)</label>
                                <div id="phone-images-preview"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control"></textarea>
                            <div class="input-group mb-3">
                                <input type="file" id="message-images" name="message-images[]" multiple class="form-control-file">
                                <label for="message-images">Upload images (optional)</label>
                                <div id="message-images-preview"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="agree">Agree to terms</label>
                            <input type="checkbox" id="agree" name="agree" class="form-check-input">
                            <div class="input-group mb-3">
                                <input type="file" id="agree-images" name="agree-images[]" multiple class="form-control-file">
                                <label for="agree-images">Upload images (optional)</label>
                                <div id="agree-images-preview"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript code -->
    <script>
        // Get all file input elements
var fileInputs = document.querySelectorAll('input[type="file"]');

// Add event listener to each file input element
fileInputs.forEach(function(fileInput) {
    var previewDiv = fileInput.nextElementSibling;
    var files = [];

    fileInput.addEventListener('change', function() {
        var newFiles = Array.prototype.slice.call(fileInput.files);
        files = files.concat(newFiles);

        var fileList = '';
        files.forEach(function(file) {
            fileList += '<p>' + file.name + ' <a href="#" class="remove-file" data-file="' + file.name + '">Remove</a></p>';
        });

        previewDiv.innerHTML = fileList;
    });
});

// Add event listener to remove file links
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-file')) {
        event.preventDefault();
        var fileName = event.target.getAttribute('data-file');
        var fileInput = event.target.closest('div').previousElementSibling;
        var previewDiv = fileInput.nextElementSibling;

        // Remove the file from the files array
        files = files.filter(function(file) {
            return file.name !== fileName;
        });

        // Update the preview list
        var fileList = '';
        files.forEach(function(file) {
            fileList += '<p>' + file.name + ' <a href="#" class="remove-file" data-file="' + file.name + '">Remove</a></p>';
        });

        previewDiv.innerHTML = fileList;
    }
});
    </script>
</body>
</html>