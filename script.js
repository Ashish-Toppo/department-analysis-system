// Function to add a new chapter
function addNewChapter() {
  const formSections = document.getElementById("formSections");

  // Create a new section element
  const newSection = document.createElement("div");
  newSection.classList.add("form-section", "mb-4", "p-3", "border", "rounded");

  // Create the HTML structure for the new section
  newSection.innerHTML = `
    <hr> <br> <br>
    <div class="text-end">
      <button
        type="button"
        class="btn btn-danger btn-sm"
        onclick="removeFormSection(this)"
      >
        <i class="bi bi-trash"></i> Delete
      </button>
    </div>

    <!-- Question Field -->
    <div class="mb-3">
      <label for="question" class="form-label">Question</label>
      <input type="text" class="form-control" placeholder="Type your question here" />
    </div>

    <!-- Answer Type Selection -->
    <div class="mb-3">
      <label for="answerType" class="form-label">Answer Type</label>
      <select class="form-select answerType" onchange="changeInputType(this)">
        <option value="multipleChoice" selected>Multiple choice</option>
        <option value="answerType">Answer Type</option>
      </select>
    </div>

    <!-- Option List (conditionally visible) -->
    <div class="mb-3 option-container d-none">
      <label class="form-label">Options</label>
      <div class="optionList">
        <div class="input-group mb-2">
          <input type="text" class="form-control" placeholder="Option 1" />
          <button
            class="btn btn-outline-secondary"
            type="button"
            onclick="removeOption(this)"
          >
            Remove
          </button>
        </div>
      </div>
      <button
        type="button"
        class="btn btn-outline-primary"
        onclick="addOption(this)"
      >
        + Add Option
      </button>
    </div>

    <!-- File Upload Section (conditionally visible) -->
    <div class="mb-3 file-upload-section d-none">
      <label class="form-label">File Upload</label>
      <div class="file-upload-container p-3 border rounded">
        <input type="file" class="form-control mb-3" id="fileUpload" multiple />
        <small class="form-text text-muted">Max 10MB per file, up to 10 files.</small>
        <div class="mb-3">
          <label for="maxFiles" class="form-label">Max Number of Files</label>
          <input type="number" class="form-control" id="maxFiles" placeholder="Enter max number of files" min="1" max="10" />
        </div>
        <div class="mb-3">
          <label for="maxFileSize" class="form-label">Max File Size (in MB)</label>
          <input type="number" class="form-control" id="maxFileSize" placeholder="Enter max file size in MB" min="1" max="10" />
        </div>
      </div>
    </div>

    <!-- Required toggle -->
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" />
      <label class="form-check-label">Required</label>
    </div>
  `;

  // Append the new section to the form
  formSections.appendChild(newSection);
}

// Function to change the input type (e.g., show/hide options and file upload fields)
function changeInputType(selectElement) {
  const formSection = selectElement.closest(".form-section");
  const optionContainer = formSection.querySelector(".option-container");
  const fileUploadSection = formSection.querySelector(".file-upload-section");

  // Show/hide the options and file upload sections based on the selected answer type
  if (selectElement.value === "answerType") {
    optionContainer.classList.add("d-none");
    // fileUploadSection.classList.remove("d-none");
    // Remove the options button
    const optionsButton = formSection.querySelector(".btn-outline-primary");
    optionsButton.style.display = "none";
  } else if (selectElement.value === "multipleChoice") {
    optionContainer.classList.remove("d-none");
    // fileUploadSection.classList.add("d-none");
    // Show the options button
    const optionsButton = formSection.querySelector(".btn-outline-primary");
    optionsButton.style.display = "block";
  }
}

// function to hide and show section depending upon whether user are allowed to upload files
function changeFileUpload(selectElement) {
  const fileUploadContainer = selectElement.closest(".file-upload-container");
  const maxFile = fileUploadContainer.querySelector('.max-file');
  const maxSize = fileUploadContainer.querySelector('.max-size');


  // show hide the sections base on the selected option
  if(selectElement.value == 'yes') {
    maxFile.classList.remove("d-none");
    maxSize.classList.remove("d-none");
  } else if (selectElement.value == 'no') {
    maxFile.classList.add("d-none");
    maxSize.classList.add("d-none");
  }
}

// Function to add a new option input with file upload functionality
function addOption(buttonElement) {
  const optionContainer = buttonElement.closest(".option-container");
  const optionList = optionContainer.querySelector(".optionList");

  const optionCount = optionList.children.length + 1; // Get the current number of options
  const newOption = document.createElement("div");
  newOption.className = "input-group mb-2";

  newOption.innerHTML = `
    <input type="text" class="form-control" placeholder="Option ${optionCount}" />
    <!-- File Upload Button with Icon -->
    <button class="btn btn-outline-secondary" type="button" onclick="triggerFileUpload(this)">
      <i class="bi bi-file-earmark-arrow-up"></i> Upload
    </button>
    <!-- Hidden file input -->
    <input type="file" class="file-input d-none" accept="image/*" onchange="handleFileUpload(this)" />
    <!-- Remove Option Button -->
    <button class="btn btn-outline-secondary" type="button" onclick="removeOption(this)">
      Remove
    </button>
  `;

  // Append the option to the list
  optionList.appendChild(newOption);

  // Create a container for file previews (below the option)
  const filePreviewContainer = document.createElement("div");
  filePreviewContainer.className = "file-preview-container mt-2";
  optionList.appendChild(filePreviewContainer); // Add it right after the option
}

// Function to remove an option input
function removeOption(buttonElement) {
  const optionGroup = buttonElement.closest(".input-group");
  const filePreviewContainer = optionGroup.nextElementSibling;
  optionGroup.remove(); // Remove the entire option group from the DOM
  if (filePreviewContainer) {
    filePreviewContainer.remove(); // Remove the file preview container if it exists
  }
}

// Function to remove a form section
function removeFormSection(buttonElement) {
  const formSection = buttonElement.closest(".form-section");
  formSection.remove();
}

// Function to trigger file upload when the button is clicked
function triggerFileUpload(button) {
  const fileInput = button.closest(".input-group").querySelector(".file-input");
  fileInput.click(); // Simulate click on the hidden file input
}

// Handle file upload and display the preview
function handleFileUpload(input) {
  const file = input.files[0]; // Get the first file
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const previewContainer = input
        .closest(".form-section")
        .querySelector(".file-preview-container");

      // Create an img element for the preview
      const imgPreview = document.createElement("img");
      imgPreview.src = e.target.result;
      imgPreview.style.width = "100px"; // Set a fixed width or adjust accordingly
      imgPreview.className = "me-2"; // Add margin-right for spacing

      // Create a button to remove the image
      const removeButton = document.createElement("button");
      removeButton.textContent = "Remove";
      removeButton.className = "btn btn-danger btn-sm mb-2";
      removeButton.onclick = function () {
        imgPreview.remove(); // Remove image preview
        removeButton.remove(); // Remove the button
      };

      // Append the image and remove button to the container
      previewContainer.appendChild(imgPreview);
      previewContainer.appendChild(removeButton);
    };
    reader.readAsDataURL(file); // Read the file and convert it to a Data URL
  }
}
