<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

Auth::requireLogin();
$dashboardCtrl = new DashboardController();
$stats = $dashboardCtrl->getStatistics();
$activities = $dashboardCtrl->getRecentActivities(5);
$feeCollection = $dashboardCtrl->getFeeCollectionSummary();
$user = Auth::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CFMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .sidebar {
            background-color: #2c3e50;
            color: white;
            min-height: 100vh;
            padding-top: 20px;
        }
        .nav-link {
            color: #bdc3c7 !important;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .nav-link:hover {
            background-color: #34495e;
            color: white !important;
        }
        .nav-link.active {
            background-color: #3498db;
            border-left-color: #e74c3c;
            color: white !important;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 14px;
            color: #7f8c8d;
        }
        .topbar {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h5>CFMS</h5>
                    <small>Fee Management</small>
                </div>
                <nav class="nav flex-column">
                    <a href="./dashboard.php" class="nav-link active"><i class="bi bi-house"></i> Dashboard</a>
                    <a href="./students.php" class="nav-link"><i class="bi bi-people"></i> Students</a>
                    <a href="./courses.php" class="nav-link"><i class="bi bi-book"></i> Courses</a>
                    <a href="./departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
                    <a href="./fee_structure.php" class="nav-link"><i class="bi bi-cash-coin"></i> Fee Structure</a>
                    <a href="./fee_collection.php" class="nav-link"><i class="bi bi-credit-card"></i> Fee Collection</a>
                    <a href="./receipts.php" class="nav-link"><i class="bi bi-receipt"></i> Receipts</a>
                    <a href="./reports.php" class="nav-link"><i class="bi bi-graph-up"></i> Reports</a>
                    <a href="./users.php" class="nav-link"><i class="bi bi-person-gear"></i> Users</a>
                    <a href="./settings.php" class="nav-link"><i class="bi bi-gear"></i> Settings</a>
                    <a href="./app/controllers/auth_logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Topbar -->
                <div class="topbar">
                    <h2>Dashboard</h2>
                    <div class="user-info">
                        <div class="user-avatar"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></div>
                        <div>
                            <strong><?php echo $user['full_name']; ?></strong><br>
                            <small><?php echo ucfirst($user['role']); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="p-4">
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon text-primary"><i class="bi bi-people"></i></div>
                                <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                                <div class="stat-label">Total Students</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon text-success"><i class="bi bi-book"></i></div>
                                <div class="stat-value"><?php echo $stats['total_courses']; ?></div>
                                <div class="stat-label">Total Courses</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon text-warning"><i class="bi bi-building"></i></div>
                                <div class="stat-value"><?php echo $stats['total_departments']; ?></div>
                                <div class="stat-label">Departments</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="stat-card">
                                <div class="stat-icon text-danger"><i class="bi bi-cash-coin"></i></div>
                                <div class="stat-value">₹<?php echo number_format($stats['total_fee_collected'], 0); ?></div>
                                <div class="stat-label">Fee Collected</div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Collection Summary -->
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="stat-card">
                                <h5>Fee Collection Summary</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Collected</td>
                                        <td><strong>₹<?php echo number_format($feeCollection['collected'] ?? 0, 2); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Pending</td>
                                        <td><strong>₹<?php echo number_format($feeCollection['pending'] ?? 0, 2); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Failed</td>
                                        <td><strong>₹<?php echo number_format($feeCollection['failed'] ?? 0, 2); ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="stat-card">
                                <h5>Quick Stats</h5>
                                <p><i class="bi bi-person-check"></i> Total Users: <strong><?php echo $stats['total_users']; ?></strong></p>
                                <p><i class="bi bi-clock-history"></i> Pending Payments: <strong><?php echo $stats['pending_payments']; ?></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="stat-card mt-4">
                        <h5>Recent Activities</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Module</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></td>
                                        <td><?php echo $activity['username'] ?? 'System'; ?></td>
                                        <td><?php echo $activity['action']; ?></td>
                                        <td><span class="badge bg-info"><?php echo $activity['module']; ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
