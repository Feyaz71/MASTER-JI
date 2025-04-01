<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

include "config.php";

// Fetch user name using email
if (!isset($_SESSION["user_name"])) {
    $query = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION["user_name"] = $row["name"];
    }
}

$user_name = $_SESSION["user_name"];

// Fetch classes based on role
if ($role == "teacher") {
    $query = "SELECT * FROM classes WHERE teacher_id = ?";
} else {
    $query = "SELECT classes.* FROM classes 
              JOIN class_students ON classes.id = class_students.class_id 
              WHERE class_students.student_id = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$classes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #1a237e;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #3949ab;
        }

        /* Main Content */
        .content {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        /* Centered Button */
        .toggle-button {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #1a237e;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: fit-content;
            margin: 20px auto;
            transition: background 0.3s;
        }

        .toggle-button:hover {
            background-color: #3949ab;
        }

        /* Class Cards */
        .class-list {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .class-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .class-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .class-card a {
            display: inline-block;
            padding: 8px 12px;
            background-color: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .class-card a:hover {
            background-color: #3949ab;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Teacher Panel</h2>
    <a href="dashboard.php">🏠 Home</a>
    <?php if ($role == "teacher"): ?>
        <a href="create_class.php">✒️ Create Class</a>
    <?php else: ?>
        <a href="join_class.php">📚 Join Class</a>
    <?php endif; ?>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h1>Welcome <?php echo htmlspecialchars($user_name); ?> 👨‍🏫</h1><br>
    
    <!-- Toggle Button -->
    <div class="toggle-button" onclick="toggleClasses()">Your Classes</div>

    <!-- Class List -->
    <div class="class-list" id="classList">
        <?php if ($classes && $classes->num_rows > 0): ?>
            <?php while ($row = $classes->fetch_assoc()): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($row["class_name"]); ?></h3>
                    <a href="view_class.php?id=<?php echo $row["id"]; ?>">View Class</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No classes found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleClasses() {
        var classList = document.getElementById("classList");
        classList.style.display = (classList.style.display === "none" || classList.style.display === "") ? "grid" : "none";
    }
</script>

</body>
</html>
