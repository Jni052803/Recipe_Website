<?php
session_start();
require_once '../server/config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];
    
    // Fetch the recipe details
    $stmt = $conn->prepare("SELECT title, description, ingredients, steps FROM recipes WHERE recipe_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $recipe_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($title, $description, $ingredients, $steps);
        $stmt->fetch();
    } else {
        echo "<p class='error'>Recipe not found!</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p class='error'>No recipe selected for editing!</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update recipe if form is submitted
    $new_title = $_POST['title'];
    $new_description = $_POST['description'];
    $new_ingredients = $_POST['ingredients'];
    $new_steps = $_POST['steps'];

    // Check if all fields are filled
    if (!empty($new_title) && !empty($new_description) && !empty($new_ingredients) && !empty($new_steps)) {
        $stmt = $conn->prepare("UPDATE recipes SET title = ?, description = ?, ingredients = ?, steps = ? WHERE recipe_id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $new_title, $new_description, $new_ingredients, $new_steps, $recipe_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            echo "<p class='success'>Recipe updated successfully! <a href='recipes.php'>View Recipes</a></p>";
        } else {
            echo "<p class='error'>Error updating recipe: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='error'>All fields are required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header class="header">
        <h1>Edit Recipe</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php">Logout</a></p>
    </header>

    <main class="container">
        <!-- Edit Recipe Form -->
        <form action="edit_recipe.php?id=<?= $recipe_id; ?>" method="POST" class="form">
            <label for="title">Recipe Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($description); ?></textarea>

            <label for="ingredients">Ingredients (separated by commas):</label>
            <textarea id="ingredients" name="ingredients" rows="4" required><?= htmlspecialchars($ingredients); ?></textarea>

            <label for="steps">Steps:</label>
            <textarea id="steps" name="steps" rows="6" required><?= htmlspecialchars($steps); ?></textarea>

            <button type="submit" class="button">Update Recipe</button>
        </form>
    </main>
</body>
</html>
