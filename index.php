
<?php
include 'includes/header.php';
require_once 'config/database.php';

// Fetch all tasks from database
$activeTasks = [];
$completedTasks = [];
try {
    $conn = getDBConnection();
    $sql = "SELECT * FROM tasks ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        $tasks = $result->fetch_all(MYSQLI_ASSOC);

        // Separate tasks into active and completed
        foreach ($tasks as $task) {
            if ($task['status'] == 'completed') {
                $completedTasks[] = $task;
            } else {
                $activeTasks[] = $task;
            }
        }
    }

    $conn->close();
} catch (Exception $e) {
    $errorMessage = 'Error fetching tasks: ' . $e->getMessage();
}
?>



<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><i class="bi bi-list-task"></i> Task List</div>
        <a href="create.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Task
        </a>
    </div>

    <hr />

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($activeTasks) && empty($completedTasks)): ?>
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle"></i> No tasks found. Create your first task!
        </div>
    <?php else: ?>

        <!-- Active Tasks Section -->
        <h5 class="mb-3"><i class="bi bi-hourglass-split"></i> Active Tasks (<?= count($activeTasks) ?>)</h5>
        <?php if (empty($activeTasks)): ?>
            <div class="alert alert-secondary" role="alert">
                No active tasks. All tasks are completed!
            </div>
        <?php else: ?>
            <div class="row mb-5">
                <?php foreach ($activeTasks as $task): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?= htmlspecialchars($task['title']) ?></h5>
                                    <?php
                                    $priorityBadge = [
                                        'low' => 'bg-secondary',
                                        'medium' => 'bg-warning',
                                        'high' => 'bg-danger'
                                    ];
                                    $priorityClass = $priorityBadge[$task['priority']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $priorityClass ?>">
                                        <?= ucfirst(htmlspecialchars($task['priority'])) ?>
                                    </span>
                                </div>

                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($task['description'], 0, 100)) ?>
                                    <?= strlen($task['description']) > 100 ? '...' : '' ?>
                                </p>

                                <div class="mb-3">
                                    <?php
                                    $statusBadge = [
                                        'pending' => 'bg-warning text-dark',
                                        'in-progress' => 'bg-info text-dark',
                                        'completed' => 'bg-success'
                                    ];
                                    $statusClass = $statusBadge[$task['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <i class="bi bi-circle-fill"></i> <?= ucfirst(str_replace(['-', '_'], ' ', htmlspecialchars($task['status']))) ?>
                                    </span>
                                </div>

                                <div class="small text-muted mb-3">
                                    <i class="bi bi-calendar-event"></i> Due:
                                    <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <form method="POST" action="handler/complete-task.php" class="d-inline">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Mark this task as completed?')">
                                            <i class="bi bi-check-circle"></i> Complete
                                        </button>
                                    </form>
                                    <a href="update.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this task?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer text-muted small">
                                <i class="bi bi-clock"></i> Created: <?= date('M d, Y', strtotime($task['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Completed Tasks Section -->
        <h5 class="mb-3 mt-5"><i class="bi bi-check-circle-fill text-success"></i> Completed Tasks (<?= count($completedTasks) ?>)</h5>
        <?php if (empty($completedTasks)): ?>
            <div class="alert alert-secondary" role="alert">
                No completed tasks yet.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($completedTasks as $task): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm opacity-75">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title text-decoration-line-through"><?= htmlspecialchars($task['title']) ?></h5>
                                    <?php
                                    $priorityBadge = [
                                        'low' => 'bg-secondary',
                                        'medium' => 'bg-warning',
                                        'high' => 'bg-danger'
                                    ];
                                    $priorityClass = $priorityBadge[$task['priority']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $priorityClass ?>">
                                        <?= ucfirst(htmlspecialchars($task['priority'])) ?>
                                    </span>
                                </div>

                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($task['description'], 0, 100)) ?>
                                    <?= strlen($task['description']) > 100 ? '...' : '' ?>
                                </p>

                                <div class="mb-3">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </span>
                                </div>

                                <div class="small text-muted mb-3">
                                    <i class="bi bi-calendar-event"></i> Due:
                                    <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="delete-task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to delete this task?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer text-muted small">
                                <i class="bi bi-clock"></i> Created: <?= date('M d, Y', strtotime($task['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
include 'includes/footer.php';
?>
