<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("Error: Class ID not provided.");
}

$class_id = intval($_GET["id"]);

// Fetch class details
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    die("Error: Class not found.");
}

// Fetch uploaded files
$files_stmt = $conn->prepare("SELECT * FROM files WHERE class_id = ?");
$files_stmt->bind_param("i", $class_id);
$files_stmt->execute();
$files = $files_stmt->get_result();

// Fetch enrolled students
$students_stmt = $conn->prepare("
    SELECT users.name 
    FROM users 
    JOIN class_students ON users.id = class_students.student_id 
    WHERE class_students.class_id = ? 
    ORDER BY users.name ASC
");
$students_stmt->bind_param("i", $class_id);
$students_stmt->execute();
$students = $students_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class</title>
    <style>
        /* General Styling */
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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
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

        h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: black;
        }

        /* Class Details */
        .class-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        /* File List */
        .file-list {
            list-style: none;
            padding: 0;
        }

        .file-list li {
            padding: 12px 20px;
            background-color: #fff;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-list a {
            text-decoration: none;
            color: #1a237e;
            font-weight: bold;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .file-list a:hover {
            color: #3949ab;
        }

        /* Student List */
        .student-list {
            list-style: none;
            padding: 0;
        }

        .student-list li {
            padding: 12px;
            background-color: #fff;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background-color: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #3949ab;
        }

        .btn-danger {
            background-color: #ff6f61;
        }

        .btn-danger:hover {
            background-color: #e6574d;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2 style="color: white;">Teacher Panel</h2>
    <a href="dashboard.php">🏠 Home</a>
    <a href="create_class.php">✒️ Create Class</a>
    <a href="profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="class-details">
        <h1><?php echo htmlspecialchars($class['class_name']); ?></h1>

        <!-- Uploaded Assignments -->
        <h2>📂 Uploaded Assignments</h2>
        <ul class="file-list">
            <?php while ($file = $files->fetch_assoc()): ?>
                <li>
                    <span><?php echo htmlspecialchars($file['filename']); ?></span>
                    <span>
                        <a href="download.php?file=<?php echo urlencode($file['filename']); ?>">⬇️ Download</a>
                        <a href="delete.php?id=<?php echo $file['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this file?')">❌ Delete</a>
                    </span>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Enrolled Students -->
        <h2>📋 Enrolled Students</h2>
        <ul class="student-list">
            <?php while ($student = $students->fetch_assoc()): ?>
                <li>👤 <?php echo htmlspecialchars($student['name']); ?></li>
            <?php endwhile; ?>
        </ul>

        <a href="upload.php?class_id=<?php echo $class_id; ?>" class="btn">📤 Upload a File</a>
        <a href="dashboard.php" class="btn btn-danger">⬅️ Back to Dashboard</a>
    </div>
</div>

</body>
</html>

<?php 
$files_stmt->close(); 
$students_stmt->close(); 
?>
