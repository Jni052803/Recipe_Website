<?php
include '../server/config.php';

$recipe_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/styles.css">
    <title><?php echo htmlspecialchars($recipe['title']); ?></title>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="recipes.php">Recipes</a>
    </nav>
    <div class="container">
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($recipe['description']); ?></p>
        <p><strong>Ingredients:</strong> <?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
        <p><strong>Steps:</strong> <?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></p>
    </div>
</body>
</html>
