<?php
include 'includes/header.php';
require_once 'config/database.php';

// variables utk statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'in-progress' => 0,
    'completed' => 0,
    'low' => 0,
    'medium' => 0,
    'high' => 0
];

// count dari DB
try {
    $conn = getDBConnection();

    //total tasks
    $result = $conn->query("SELECT COUNT(*) as count FROM tasks");
    $stats['total'] = $result->fetch_assoc()['count'];

    //tasks by status
    $result = $conn->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status");
    while ($row = $result->fetch_assoc()) {
        $stats[$row['status']] = $row['count'];
    }

    //tasks by priority
    $result = $conn->query("SELECT priority, COUNT(*) as count FROM tasks GROUP BY priority");
    while ($row = $result->fetch_assoc()) {
        $stats[$row['priority']] = $row['count'];
    }

    $conn->close();
} catch (Exception $e) {
    $errorMessage = 'Error fetching statistics: ' . $e->getMessage();
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><i class="bi bi-bar-chart-fill"></i> Task Report & Statistics</h5>
        <a href="index.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
    </div>

    <hr />

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

<div class="row">
    <div class="col-md-3">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-5">Total Task</div>
                <div class="fs-3 fw-bold"><?= $stats['total'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-5">Pending Task</div>
                <div class="fs-3 fw-bold"><?= $stats['pending'] ?></div>
            </div>
        </div>        
    </div>
    <div class="col-md-3">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-5">In Progress</div>
                <div class="fs-3 fw-bold"><?= $stats['in-progress'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-5">Completed</div>
                <div class="fs-3 fw-bold"><?= $stats['completed'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-6">Low Priority Task</div>
                <div class=".fs-3 fw-bold"><?=$stats['low'] ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-6">Medium Priority Task</div>
                <div class=".fs-3 fw-bold"><?=$stats['medium'] ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow bg-body-tertiary border-0 mb-4">
            <div class="card-body">
                <div class="fs-6">High Priority Task</div>
                <div class=".fs-3 fw-bold"><?=$stats['high'] ?></div>
            </div>
        </div>
    </div>
</div>

    <?= $stats['total'] ?>
    <?= $stats['pending'] ?>
    <?= $stats['in-progress'] ?>
    <?= $stats['completed'] ?>
    <?= $stats['low'] ?>
    <?= $stats['medium'] ?>
    <?= $stats['high'] ?>
    <hr />

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm bg-body-tertiary h-100">
                <div class="card-header ">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-warning"></i> Pending</span>
                            <strong><?= $stats['pending'] ?> (<?= ($stats['total'] > 0) ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['pending'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-info"></i> In Progress</span>
                            <strong><?= $stats['in-progress'] ?> (<?= ($stats['total'] > 0) ? round(($stats['in-progress'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['in-progress'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-success"></i> Completed</span>
                            <strong><?= $stats['completed'] ?> (<?= ($stats['total'] > 0) ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['completed'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Breakdown -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm bg-body-tertiary h-100">
                <div class="card-header ">
                    <h5 class="mb-0">Priority</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-danger"></i> High Priority</span>
                            <strong><?= $stats['high'] ?> (<?= ($stats['total'] > 0) ? round(($stats['high'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['high'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-warning"></i> Medium Priority</span>
                            <strong><?= $stats['medium'] ?> (<?= ($stats['total'] > 0) ? round(($stats['medium'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['medium'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-secondary"></i> Low Priority</span>
                            <strong><?= $stats['low'] ?> (<?= ($stats['total'] > 0) ? round(($stats['low'] / $stats['total']) * 100, 1) : 0 ?>%)</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-secondary" role="progressbar"
                                style="width: <?= ($stats['total'] > 0) ? ($stats['low'] / $stats['total']) * 100 : 0 ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-clipboard-check"></i> Completion Rate</h6>
                    <h2 class="mb-0">
                        <?= ($stats['total'] > 0) ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 ?>%
                    </h2>
                    <small><?= $stats['completed'] ?> of <?= $stats['total'] ?> tasks completed</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-lightning-fill"></i> Active Tasks</h6>
                    <h2 class="mb-0"><?= $stats['pending'] + $stats['in-progress'] ?></h2>
                    <small>Pending + In Progress</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-flag-fill"></i> High Priority</h6>
                    <h2 class="mb-0"><?= $stats['high'] ?></h2>
                    <small>Urgent tasks requiring attention</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary ">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Status Chart</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [<?= $stats['pending'] ?>, <?= $stats['in-progress'] ?>, <?= $stats['completed'] ?>],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745'],
                borderColor: ['#fff', '#fff', '#fff'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php
include 'includes/footer.php';
?>