<?php
// Start session
session_start();

// Include necessary files
include_once("../config/database.php");
include_once("../includes/functions.php");

// Ensure the user is signed in
// if (!isset($_SESSION['user_id'])) {
//     echo "User not signed in.";
//     exit();
// }

$user_id = $_SESSION['user_id'];

// Get the list of assessments
$assessmentsQuery = $conn->prepare("SELECT id, title, description FROM assessments");
$assessmentsQuery->execute();
$assessmentsResult = $assessmentsQuery->get_result();

// Check if the user has responded to any assessment
$responseQuery = $conn->prepare("SELECT DISTINCT assessment_id FROM responses WHERE filled_by = ?");
$responseQuery->bind_param("i", $user_id);
$responseQuery->execute();
$respondedAssessmentsResult = $responseQuery->get_result();

$respondedAssessments = [];
while ($row = $respondedAssessmentsResult->fetch_assoc()) {
    $respondedAssessments[] = $row['assessment_id'];
}

// Generate CSRF token
$csrfToken = generateCsrfToken();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assessment List</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .signout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sign Out Button -->
        <a href="../signout.php" class="btn btn-danger signout-btn">Sign Out</a>
        
        <h1 class="mt-4">Assessment List</h1>
        <ul class="list-group">
            <?php
            $list_items = [];
            while ($assessment = $assessmentsResult->fetch_assoc()) {
                $assessment_id = $assessment['id'];
                $title = htmlspecialchars($assessment['title']);
                $description = htmlspecialchars($assessment['description']);

                // Determine if the user has responded to this assessment
                $hasResponded = in_array($assessment_id, $respondedAssessments);

                // Create list item with adjusted classes
                $itemClass = $hasResponded ? 'list-group-item list-group-item-secondary' : 'list-group-item';
                $buttonDisabled = $hasResponded ? 'disabled' : '';

                // Store the item and button state in an array
                $list_items[] = [
                    'class' => $itemClass,
                    'button_disabled' => $buttonDisabled,
                    'id' => $assessment_id,
                    'title' => $title,
                    'description' => $description
                ];
            }

            // Separate responded and not responded items
            $not_responded_items = array_filter($list_items, function($item) {
                return $item['button_disabled'] === '';
            });

            $responded_items = array_filter($list_items, function($item) {
                return $item['button_disabled'] === 'disabled';
            });

            // Combine items with not responded first
            $sorted_items = array_merge($not_responded_items, $responded_items);

            foreach ($sorted_items as $item) {
                echo '<li class="' . $item['class'] . '">';
                echo '<h5>' . $item['title'] . '</h5>';
                echo '<p>' . $item['description'] . '</p>';
                echo '<a href="fill.php?id=' . $item['id'] . '&csrf=' . $csrfToken . '" class="btn btn-primary ' . $item['button_disabled'] . '">Fill</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close database connections
$assessmentsQuery->close();
$responseQuery->close();
$conn->close();
?>
