<?php
include 'includes/header.php';
require_once 'config/database.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $priority = $_POST['priority'] ?? 'medium';
    $due_date = $_POST['due_date'] ?? '';

    // Validate required fields
    if (empty($title) || empty($description) || empty($due_date)) {
        $errorMessage = 'Please fill in all required fields.';
    } else {
        try {
            $conn = getDBConnection();

            // Prepare SQL statement
            $sql = "INSERT INTO tasks (title, description, status, priority, due_date, created_at, update_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $title, $description, $status, $priority, $due_date);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: index.php?success=Task created successfully!');
                exit();
            } else {
                $errorMessage = 'Error creating task: ' . $stmt->error;
                $stmt->close();
                $conn->close();
            }
        } catch (Exception $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<div class="row mt-5">
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

                <form method="POST" action="create.php">
                    <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required />

                    <label for="description" class="form-label fw-bold">Task Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required>  <?= htmlspecialchars($description ?? '') ?></textarea>

                    <label for="status" class="form-label fw-bold">Status<span class="text-danger">*</span></label>

                    <select class="form-select" id="status" name="status">
                        <option value="pending" <?= (($status ?? '') == 'pending') ? 'selected' : '' ?>>Pending</option>

                        <option value="in-progress" <?= (($status ?? '') == 'in-progress') ? 'selected' : '' ?>>In-progress</option>

                        <option value="completed" <?= (($status ?? '') == 'completed') ? 'selected' : '' ?>>Completed</option>
                    </select>

                    <label for="priority" class="form-label fw-bold">Priority<span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="low" <?= (($priority ?? '') == 'low') ? 'selected' : '' ?>>Low</option>

                        <option value="medium" <?= (($priority ?? '') == 'medium') ? 'selected' : '' ?>>Medium</option>

                        <option value="high" <?= (($priority ?? '') == 'high') ? 'selected' : '' ?>>High</option>
                    </select>

                    <label for="due_date" class="form-label fw-bold">Due Date<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= htmlspecialchars($due_date ?? '') ?>" required />



                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Create Task
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


<?php include 'includes/footer.php'; ?>