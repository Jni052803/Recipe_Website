<?php
session_start();
require_once '../server/config.php'; // Include database connection

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission (server-side validation would still be important as well)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];

    if (!empty($title) && !empty($description) && !empty($ingredients) && !empty($steps)) {
        $user_id = $_SESSION['user_id']; // Get user_id from session

        // Prepare SQL statement to insert recipe into database
        $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, description, ingredients, steps) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $description, $ingredients, $steps);

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo "<p>Recipe added successfully! <a href='recipes.php'>View Recipes</a></p>";
        } else {
            echo "<p>Error adding recipe: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>All fields are required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to external CSS file -->
    <script>
        function validateForm() {
            // Get form fields
            var title = document.getElementById('title').value;
            var description = document.getElementById('description').value;
            var ingredients = document.getElementById('ingredients').value;
            var steps = document.getElementById('steps').value;

            // Check if any fields are empty
            if (title.trim() == "" || description.trim() == "" || ingredients.trim() == "" || steps.trim() == "") {
                alert("All fields are required!");
                return false; // Prevent form submission if validation fails
            }

            return true; // Allow form submission if validation passes
        }
    </script>
</head>
<body>
    <header>
        <h1>Add a New Recipe</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php">Logout</a></p>
    </header>

    <main>
        <!-- Recipe Form -->
        <form action="add_recipe.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="title">Recipe Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingredients (separated by commas):</label>
                <textarea id="ingredients" name="ingredients" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="steps">Steps:</label>
                <textarea id="steps" name="steps" rows="6" required></textarea>
            </div>

            <button type="submit" class="button">Add Recipe</button>
        </form>

        <!-- Navigation buttons -->
        <div class="button-group">
            <a href="index.php" class="button">Back to Home</a>
        </div>
    </main>
</body>
</html>
