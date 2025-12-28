<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
    $taskId = (int)$_POST['task_id'];

    try {
        $conn = getDBConnection();

        $sql = "UPDATE tasks SET status = 'completed', update_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $taskId);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: ../index.php?success=Task marked as completed');
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header('Location: ../index.php?error=Failed to update task');
            exit();
        }
    } catch (Exception $e) {
        header('Location: ../index.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}