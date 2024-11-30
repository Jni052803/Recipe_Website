<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Website</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header>
        <h1>Welcome to the Recipe Website</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Hello, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="logout.php" class="button">Logout</a>
        <?php else: ?>
            <p><a href="login.php" class="button">Login</a> or <a href="signup.php" class="button">Sign Up</a> to get started.</p>
        <?php endif; ?>
    </header>

    <main>
        <h2>Discover and Share Recipes</h2>
        <p>This website allows you to browse, share, and edit recipes. Create an account to get started!</p>

        <?php if (isset($_SESSION['username'])): ?>
            <div class="button-group">
                <a href="recipes.php" class="button">View Your Recipes</a>
                <a href="add_recipe.php" class="button">Add a Recipe</a>
                <a href="others_recipes.php" class="button">View Recipes from Others</a> 
            </div>
        <?php else: ?>
            <p>You must log in to view or add recipes.</p>
        <?php endif; ?>
    </main>
</body>
</html>
