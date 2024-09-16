<?php

// Start the session
session_start();

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['username'])) {
    header("Location: ./router.php");
    exit;
}

// include the database file
include_once("./config/database.php");

// Initialize the error message
$error = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please fill out both fields.";
    } else {
        // Prepare a statement to check the username in the database
        $stmt = $conn->prepare("SELECT id, username, pswd FROM departments WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        // Check if the username exists in the database
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $usernameFromDb, $hashedPasswordFromDb);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashedPasswordFromDb)) {
                // Password is correct, set the session and redirect to dashboard
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $usernameFromDb;
                header("Location: router.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that username.";
        }

        // Close the statement
        $stmt->close();
    }

}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Department Assessment System</h2>
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Sign In</h3>
            <!-- Error message section  -->
            <?php if (strlen($error) > 0 ): ?>
                <div class="alert alert-danger" id="error-message" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
             
           

            <form action="" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" placeholder="Enter your username" name="username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Enter your password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Sign In</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
