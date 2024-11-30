<?php
session_start();
require_once '../server/config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch all recipe IDs to generate numbered buttons
$all_recipes_stmt = $conn->prepare("SELECT recipe_id FROM recipes WHERE user_id != ? ORDER BY recipe_id ASC");
$all_recipes_stmt->bind_param("i", $user_id);
$all_recipes_stmt->execute();
$all_recipes_stmt->bind_result($all_recipe_id);

$recipe_ids = [];
while ($all_recipes_stmt->fetch()) {
    $recipe_ids[] = $all_recipe_id;
}
$all_recipes_stmt->close();

// Determine the currently requested recipe
$current_index = isset($_GET['recipe_index']) ? (int)$_GET['recipe_index'] : 0;

// Ensure the current index is valid
if ($current_index < 0 || $current_index >= count($recipe_ids)) {
    $current_index = 0; // Default to the first recipe
}

$recipe_id = $recipe_ids[$current_index];

// Fetch the specific recipe details
$stmt = $conn->prepare("SELECT r.recipe_id, r.title, r.description, r.ingredients, r.steps, u.username 
                        FROM recipes r 
                        INNER JOIN users u ON r.user_id = u.user_id 
                        WHERE r.recipe_id = ? AND r.user_id != ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$stmt->store_result();

// If no recipe is found
if ($stmt->num_rows === 0) {
    echo "No recipes available at the moment.";
    exit();
}

$stmt->bind_result($recipe_id, $title, $description, $ingredients, $steps, $username);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title); ?> - Recipe</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($title); ?></h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>! <a href="logout.php" class="button">Logout</a></p>
        <a href="index.php" class="button">Back to Home</a>
    </header>

    <main class="container">
        <div class="recipe">
            <p><strong>By:</strong> <?= htmlspecialchars($username); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($description); ?></p>
            <p><strong>Ingredients:</strong> <?= nl2br(htmlspecialchars($ingredients)); ?></p>
            <p><strong>Steps:</strong> <?= nl2br(htmlspecialchars($steps)); ?></p>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php foreach ($recipe_ids as $index => $id): ?>
                <a href="?recipe_index=<?= $index; ?>" 
                   class="button <?= $index === $current_index ? 'current-page' : ''; ?>">
                    <?= $index + 1; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
