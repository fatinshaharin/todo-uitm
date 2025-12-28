<?php
include 'includes/header.php';
require_once 'config/database.php';

// Initialize variables
$errorMessage = '';
$task = null;

// Get task ID from URL or POST
$taskId = isset($_POST['task_id']) ? (int)$_POST['task_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($taskId <= 0) {
    header('Location: index.php?error=Invalid task ID');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    // Validate required fields
    if (empty($title) || empty($description) || empty($due_date) || empty($status) || empty($priority)) {
        $errorMessage = 'Please fill in all required fields.';
    } else {
        try {
            $conn = getDBConnection();

            // Prepare SQL statement
            $sql = "UPDATE tasks SET title = ?, description = ?, status = ?, priority = ?, due_date = ?, update_at = NOW() WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $title, $description, $status, $priority, $due_date, $taskId);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: index.php?success=Task updated successfully!');
                exit();
            } else {
                $errorMessage = 'Error updating task: ' . $stmt->error;
                $stmt->close();
                $conn->close();
            }
        } catch (Exception $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch task data
try {
    $conn = getDBConnection();
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        $stmt->close();
        $conn->close();
        header('Location: index.php?error=Task not found');
        exit();
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $errorMessage = 'Database error: ' . $e->getMessage();
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="card shadow bg-body-tertiary border-0">
            <div class="card-body">
                <div class="fw-bold">Instructions</div>
                <hr />
                Please fill in all required fields.
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card shadow bg-body-tertiary border-0">
            <div class="card-body">
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($errorMessage) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="update.php">
                    <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">

                    <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required />

                    <label for="description" class="form-label fw-bold">Task Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($task['description']) ?></textarea>

                    <label for="status" class="form-label fw-bold">Status<span class="text-danger">*</span></label>

                    <select class="form-select" id="status" name="status">
                        <option value="pending" <?= (($task['status'] ?? '') == 'pending') ? 'selected' : '' ?>>Pending</option>

                        <option value="in-progress" <?= (($task['status'] ?? '') == 'in-progress') ? 'selected' : '' ?>>In-progress</option>

                        <option value="completed" <?= (($task['status'] ?? '') == 'completed') ? 'selected' : '' ?>>Completed</option>
                    </select>

                    <label for="priority" class="form-label fw-bold">Priority<span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low" <?= (($task['priority'] ?? '') == 'low') ? 'selected' : '' ?>>Low</option>

                        <option value="medium" <?= (($task['priority'] ?? '') == 'medium') ? 'selected' : '' ?>>Medium</option>

                        <option value="high" <?= (($task['priority'] ?? '') == 'high') ? 'selected' : '' ?>>High</option>
                    </select>

                    <label for="due_date" class="form-label fw-bold">Due Date<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>" required />



                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Task
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>