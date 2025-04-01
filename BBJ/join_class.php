<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = $_POST["class_id"];
    $student_id = $_SESSION["user_id"];

    // Check if already joined
    $check = $conn->query("SELECT * FROM class_students WHERE class_id = $class_id AND student_id = $student_id");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO class_students (class_id, student_id) VALUES ($class_id, $student_id)");
        echo "<script>alert('Joined class successfully!'); window.location.href='student_dashboard.php';</script>";
    } else {
        echo "<script>alert('You are already in this class.');</script>";
    }
}

$classes = $conn->query("SELECT * FROM classes");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class</title>
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

        .join-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: auto;
            text-align: center;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #1a237e;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #3949ab;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <a href="student_dashboard.php">🏠 Home</a>
    <a href="join_class.php">📚 Join Class</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
    <h1>Join a Class</h1><br>
    <div class="join-form">
        <form method="post">
            <label for="class_id">Select Class:</label>
            <select name="class_id" required>
                <?php while ($row = $classes->fetch_assoc()) { ?>
                    <option value="<?= $row["id"] ?>"><?= htmlspecialchars($row["class_name"]) ?></option>
                <?php } ?>
            </select>
            <button type="submit">Join Class</button>
        </form>
    </div>
</div>

</body>
</html>
