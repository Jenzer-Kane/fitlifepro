<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$forum_id = intval($_GET['id']);

// Retrieve forum information
$sql = "SELECT * FROM forums WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $forum_id);
$stmt->execute();
$forum = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$forum) {
    die('Forum not found.');
}

// Retrieve threads related to the forum
$sql = "SELECT * FROM threads WHERE forum_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $forum_id);
$stmt->execute();
$threads = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($forum['name']) ?> - Threads</title>
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1><?= htmlspecialchars($forum['name']) ?></h1>
        <p><?= htmlspecialchars($forum['description']) ?></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Thread Title</th>
                    <th>Author</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($threads->num_rows > 0): ?>
                    <?php while ($thread = $threads->fetch_assoc()): ?>
                        <tr>
                            <td><a href="thread.php?id=<?= $thread['id'] ?>"><?= htmlspecialchars($thread['title']) ?></a></td>
                            <td><?= htmlspecialchars($thread['username']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No threads available.</td>
                    <?php endif; ?>
            </tbody>
        </table>
        <a href="new_thread.php?forum_id=<?= $forum_id ?>" class="btn btn-primary">Create New Thread</a>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>

</html>