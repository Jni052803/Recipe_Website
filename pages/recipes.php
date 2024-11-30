<?php
session_start();
require_once '../server/config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

// Pagination setup (only 1 recipe per page)
$recipes_per_page = 1; 

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $recipes_per_page; 

$stmt = $conn->prepare("SELECT recipe_id, title, description, ingredients, steps FROM recipes WHERE user_id = ? LIMIT ?, ?");
$stmt->bind_param("iii", $user_id, $offset, $recipes_per_page); 
$stmt->execute();
$stmt->store_result();

// Get the total number of recipes for the user to calculate total pages
$total_recipes_stmt = $conn->prepare("SELECT COUNT(*) FROM recipes WHERE user_id = ?");
$total_recipes_stmt->bind_param("i", $user_id);
$total_recipes_stmt->execute();
$total_recipes_stmt->bind_result($total_recipes);
$total_recipes_stmt->fetch();
$total_recipes_stmt->close();

// Calculate the total number of pages needed
$total_pages = ceil($total_recipes / $recipes_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Recipe</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header>
        <h1>Your Recipe</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php" class="logout-button">Logout</a></p>
    </header>

    <main>
        <div class="recipes-list">
            <?php
            // Check if there are any recipes for the user
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($recipe_id, $title, $description, $ingredients, $steps);
                while ($stmt->fetch()) {
                    echo "<div class='recipe'>";
                    echo "<h2>" . htmlspecialchars($title) . "</h2>";
                    echo "<p><strong>Description:</strong> " . htmlspecialchars($description) . "</p>";
                    echo "<p><strong>Ingredients:</strong> " . htmlspecialchars($ingredients) . "</p>";
                    echo "<p><strong>Steps:</strong> " . nl2br(htmlspecialchars($steps)) . "</p>";
                    
                    // Add a wrapper div around the buttons to space them out
                    echo "<div class='recipe-actions'>";
                    echo "<a href='edit_recipe.php?id=" . $recipe_id . "' class='button'>Edit</a>";
                    echo "<a href='delete_recipe.php?id=" . $recipe_id . "' class='button' onclick='return confirm(\"Are you sure you want to delete this recipe?\")'>Delete</a>";
                    echo "</div>";
                    
                    echo "</div>";
                }
            } else {
                echo "<p>You don't have any recipes yet. <a href='add_recipe.php'>Add a new recipe</a></p>";
            }

            // Pagination links
            echo "<div class='pagination'>";
            if ($page > 1) {
                echo "<a href='recipes.php?page=" . ($page - 1) . "' class='button'>Previous</a>";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<span class='current-page'>$i</span>"; // Current page is not a link
                } else {
                    echo "<a href='recipes.php?page=$i' class='button'>$i</a>";
                }
            }

            if ($page < $total_pages) {
                echo "<a href='recipes.php?page=" . ($page + 1) . "' class='button'>Next</a>";
            }
            echo "</div>";
            ?>
        </div>

        <div class="button-group">
            <a href="add_recipe.php" class="button">Add a New Recipe</a>
            <a href="index.php" class="button">Back to Home</a>
        </div>
    </main>
</body>
</html>
