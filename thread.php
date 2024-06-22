<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: indexforum.php");
    exit();
}

$thread_id = intval($_GET['id']);

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT t.title, t.content, t.username, t.created_at, f.name AS forum_name FROM threads t JOIN forums f ON t.forum_id = f.id WHERE t.id = ?");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
$thread = $result->fetch_assoc();
$stmt->close();

if (!$thread) {
    echo "Thread not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO replies (thread_id, username, content) VALUES (?, ?, ?)");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("iss", $thread_id, $username, $content);
    if ($stmt->execute()) {
        header("Location: thread.php?id=$thread_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$replies_stmt = $conn->prepare("SELECT username, content, created_at FROM replies WHERE thread_id = ? ORDER BY created_at ASC");
$replies_stmt->bind_param("i", $thread_id);
$replies_stmt->execute();
$replies_result = $replies_stmt->get_result();
$replies_stmt->close();

function format_date($date)
{
    return date('F j, Y, g:i a', strtotime($date));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thread: <?= htmlspecialchars($thread['title']) ?></title>
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Thread: <?= htmlspecialchars($thread['title']) ?></h1>
        <p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>
        <p>Posted by: <?= htmlspecialchars($thread['username']) ?> on <?= format_date($thread['created_at']) ?></p>
        <hr>
        <h2>Replies</h2>
        <?php while ($reply = $replies_result->fetch_assoc()): ?>
            <div class="reply">
                <p><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                <p>Posted by: <?= htmlspecialchars($reply['username']) ?> on <?= format_date($reply['created_at']) ?></p>
                <hr>
            </div>
        <?php endwhile; ?>

        <?php if (isset($_SESSION['username'])): ?>
            <h2>Post a Reply</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Reply</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Log in</a> to post a reply.</p>
        <?php endif; ?>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>

</html>