<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- The PHP-generated form will be embedded here -->
    <div class="container mt-5">
        















    <?php
// Database connection (replace with your own connection details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "adbu_daf_departments";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the form, sections, subsections, and questions
$form_id = 3; // Assuming you want to load form with ID 1

// Fetch form details
$form_sql = "SELECT * FROM forms WHERE id = $form_id";
$form_result = $conn->query($form_sql);

if ($form_result->num_rows > 0) {
    $form = $form_result->fetch_assoc();
    
    // Display the form title and description
    echo "<div class='container my-5'>";
    echo "<h2 class='text-center mb-3'>{$form['title']}</h2>";
    echo "<p class='text-center mb-4'>{$form['description']}</p>";

    // Fetch sections associated with the form
    $sections_sql = "SELECT * FROM sections WHERE form_id = $form_id ORDER BY position";
    $sections_result = $conn->query($sections_sql);

    if ($sections_result->num_rows > 0) {
        while ($section = $sections_result->fetch_assoc()) {
            echo "<div class='card mb-4'>";
            echo "<div class='card-header bg-primary text-white'>";
            echo "<h4>{$section['title']}</h4>";
            echo "</div>";
            echo "<div class='card-body'>";
            echo "<p>{$section['description']}</p>";

            // Fetch subsections under this section
            $subsections_sql = "SELECT * FROM subsections WHERE section_id = {$section['id']} ORDER BY position";
            $subsections_result = $conn->query($subsections_sql);

            if ($subsections_result->num_rows > 0) {
                while ($subsection = $subsections_result->fetch_assoc()) {
                    echo "<div class='mb-3'>";
                    echo "<h5 class='text-secondary'>{$subsection['title']}</h5>";
                    echo "<p>{$subsection['description']}</p>";

                    // Fetch questions under this subsection
                    $questions_sql = "SELECT * FROM questions WHERE subsection_id = {$subsection['id']} ORDER BY position";
                    $questions_result = $conn->query($questions_sql);

                    if ($questions_result->num_rows > 0) {
                        echo "<form>";
                        while ($question = $questions_result->fetch_assoc()) {
                            echo "<div class='form-group mb-3'>";
                            echo "<label for='question_{$question['id']}' class='form-label'>{$question['question_text']}</label>";

                            // Handle question types
                            switch ($question['question_type']) {
                                case 'text':
                                    echo "<input type='text' class='form-control' id='question_{$question['id']}' placeholder='Enter your response'>";
                                    break;
                                
                                case 'textfile':
                                    echo "<textarea class='form-control' id='question_{$question['id']}' rows='3' placeholder='Enter detailed response'></textarea>";
                                    break;

                                case 'file':
                                    echo "<input type='file' class='form-control' id='question_{$question['id']}' multiple>";
                                    break;

                                case 'tabular':
                                    // Fetch headers for tabular data
                                    $headers_sql = "SELECT * FROM headers WHERE question_id = {$question['id']} ORDER BY sort_order";
                                    $headers_result = $conn->query($headers_sql);

                                    // Arrays for storing headers
                                    $column_headers = [];
                                    $row_headers = [];

                                    if ($headers_result->num_rows > 0) {
                                        while ($header = $headers_result->fetch_assoc()) {
                                            if ($header['header_type'] == 'column') {
                                                $column_headers[] = $header['header_name'];
                                            } elseif ($header['header_type'] == 'row') {
                                                $row_headers[] = $header['header_name'];
                                            }
                                        }
                                    }

                                    // Display the tabular question as a table
                                    if (!empty($column_headers) || !empty($row_headers)) {
                                        echo "<div class='table-responsive'>";
                                        echo "<table class='table table-bordered'>";
                                        
                                        // Display column headers
                                        echo "<thead><tr><th></th>"; // Empty corner for row headers
                                        foreach ($column_headers as $col_header) {
                                            echo "<th>$col_header</th>";
                                        }
                                        echo "</tr></thead>";

                                        // Display rows with row headers
                                        echo "<tbody>";
                                        foreach ($row_headers as $row_header) {
                                            echo "<tr>";
                                            echo "<td><strong>$row_header</strong></td>"; // Row header
                                            
                                            // Create input fields for each cell
                                            foreach ($column_headers as $col_header) {
                                                echo "<td><input type='text' class='form-control'></td>";
                                            }
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";
                                        
                                        echo "</table>";
                                        echo "</div>"; // End table-responsive
                                    }
                                    break;
                            }

                            echo "</div>"; // form-group
                        }
                        echo "</form>";
                    }

                    echo "</div>"; // subsection
                }
            }

            echo "</div>"; // card-body
            echo "</div>"; // card
        }
    }

    echo "</div>"; // container
}

$conn->close();
?>


    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
