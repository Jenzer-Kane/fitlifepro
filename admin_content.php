<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'fitlifepro_register');

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Handle form submission for adding or editing an exercise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_exercise'])) {
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $intensity = $conn->real_escape_string($_POST['intensity']);
    $exercise_type = $conn->real_escape_string($_POST['exercise_type']);
    $category = $conn->real_escape_string($_POST['category']);
    $duration = $conn->real_escape_string($_POST['duration']);

    if ($id) {
        $sql = "UPDATE exercises SET name='$name', description='$description', intensity='$intensity', exercise_type='$exercise_type', category='$category', duration='$duration' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO exercises (name, description, intensity, exercise_type, category, duration) VALUES ('$name', '$description', '$intensity', '$exercise_type', '$category', '$duration')";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = $id ? "Exercise updated successfully!" : "Exercise added successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
    }
}

// Handle deletion of an exercise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_exercise'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $sql = "DELETE FROM exercises WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Exercise deleted successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
    }
}

// Fetch exercises from the database
$exercises = [];
$sql = "SELECT * FROM exercises";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Content | FITLIFE PRO ADMIN</title>
    <!-- /SEO Ultimate -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta charset="utf-8">
    <link rel="apple-touch-icon" sizes="57x57" href="./assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="./assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="./assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="./assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="./assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="./assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="./assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="./assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="./assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Latest compiled and minified CSS -->
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/js/bootstrap.min.js">
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- StyleSheet link CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="assets/css/mediaqueries.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet" type="text/css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css">
    <style>
        .navbar {
            background-color: rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 2;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .nav-item {
            margin-right: 15px;
        }

        .navbar-nav .nav-link {
            color: #fff;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
        }

        .team_member_box_content2 img {
            border-radius: 50%;
            overflow: hidden;
            /* Ensure the image stays within the circular boundary */
            width: 300px;
            /* Set the desired width */
            height: 300px;
            /* Set the desired height */
            object-fit: cover;
            /* Maintain the aspect ratio and cover the container */
        }

        .table-responsive {
            max-height: 800px;
            /* Set the max height as needed */
            overflow-y: auto;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="banner-section-outer">
        <header>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Logo outside the navbar -->
                    <a class="navbar-brand mb-0" href="./admin_dashboard.php">
                        <figure class="mb-0">
                            <img src="./assets/images/fitlife_pro_logo2.png" alt="" class="img-fluid">
                        </figure>
                    </a>
                    <!-- Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            <span class="navbar-toggler-icon"></span>
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link active" href="./admin_dashboard.php">Members</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_subscription_approval.php">Transactions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_forum.php">Forums</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="./admin_threads.php">Threads</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="./admin_content.php">Content</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact_btn" href="./admin_messages.php">Inquiries</a>
                                </li>
                                <li class="nav-item">
                                    <?php
                                    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
                                        // If admin is logged in, display "Admin" instead of username
                                        echo '<li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin</a></li>';

                                    } elseif (isset($_SESSION['username'])) {
                                        // If user is logged in, show name and logout button
                                        echo '<li class="nav-item"><a class="nav-link" href="#">' . '<a href="profile.php">' . $_SESSION['username'] . '</a>' . '</a></li>';
                                    } else {
                                        // If user is not logged in, show login and register buttons
                                        echo '<li class="nav-item"><a class="nav-link login_btn" href="./login.html">Login</a></li>';
                                        echo '<li class="nav-item"><a class="nav-link login_btn" href="./register.html">Register</a></li>';
                                    }
                                    ?>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link login_btn" href="logout.php">Logout</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
    </div> <!-- Closing banner-section-outer -->

    <div class="container">
        <h2>Add or Edit Exercise</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="" method="POST">
            <input type="hidden" name="id" id="id">
            <div class="form-group">
                <label for="name">Exercise Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="intensity">Intensity</label>
                <select name="intensity" id="intensity" class="form-control" required>
                    <option value="Low">Low</option>
                    <option value="Moderate">Moderate</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="exercise_type">Exercise Type</label>
                <select name="exercise_type" id="exercise_type" class="form-control" required>
                    <option value="Freeweights">Freeweights</option>
                    <option value="Bodyweight">Bodyweight</option>
                    <option value="Weightlifting">Weightlifting</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="Strength">Strength</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Core">Core</option>
                    <option value="Plyometrics">Plyometrics</option>
                </select>
            </div>
            <div class="form-group">
                <label for="duration">Duration</label>
                <input type="text" name="duration" id="duration" class="form-control" required>
            </div>
            <button type="submit" name="save_exercise" class="btn btn-primary">Save Exercise</button>
        </form>

        <h2 class="mt-4">Existing Exercises</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Intensity</th>
                        <th>Exercise Type</th>
                        <th>Category</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercises as $exercise): ?>
                            <tr>
                                <td><?= htmlspecialchars($exercise['name']) ?></td>
                                <td><?= htmlspecialchars($exercise['description']) ?></td>
                                <td><?= htmlspecialchars($exercise['intensity']) ?></td>
                                <td><?= htmlspecialchars($exercise['exercise_type']) ?></td>
                                <td><?= htmlspecialchars($exercise['category']) ?></td>
                                <td><?= htmlspecialchars($exercise['duration']) ?></td>
                                <td>
                                    <button class="btn btn-info"
                                        onclick="editExercise(<?= htmlspecialchars(json_encode($exercise)) ?>)">Edit</button>
                                    <a href="admin_content.php?delete_exercise=<?= $exercise['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this exercise?');">Delete</a>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editExercise(exercise) {
            document.getElementById('id').value = exercise.id;
            document.getElementById('name').value = exercise.name;
            document.getElementById('description').value = exercise.description;
            document.getElementById('intensity').value = exercise.intensity;
            document.getElementById('exercise_type').value = exercise.exercise_type;
            document.getElementById('category').value = exercise.category;
            document.getElementById('duration').value = exercise.duration;
        }
    </script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/video-popup.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/owl.carousel.js"></script>
    <script src="assets/js/carousel.js"></script>
    <script src="assets/js/video-section.js"></script>
    <script src="assets/js/counter.js"></script>
    <script src="assets/js/animation.js"></script>
</body>

</html>