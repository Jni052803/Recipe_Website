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

    // Delete the recipe
    $stmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $recipe_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "<p>Recipe deleted successfully! <a href='recipes.php'>View Recipes</a></p>";
    } else {
        echo "<p>Error deleting recipe: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "No recipe selected for deletion!";
    exit();
}
?>
