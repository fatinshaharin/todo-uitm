<?php
require_once 'config/database.php';

// Get task ID from URL
$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($taskId <= 0) {
    header('Location: index.php?error=Invalid task ID');
    exit();
}

try {
    $conn = getDBConnection();

    // Prepare SQL statement
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $taskId);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: index.php?success=Task deleted successfully!');
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header('Location: index.php?error=Failed to delete task');
        exit();
    }
} catch (Exception $e) {
    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit();
}