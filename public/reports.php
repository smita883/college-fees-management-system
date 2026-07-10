<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';

Auth::requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CFMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f5f7fa; }
        .sidebar { background-color: #2c3e50; color: white; min-height: 100vh; padding-top: 20px; }
        .nav-link { color: #bdc3c7 !important; padding: 12px 20px; border-left: 3px solid transparent; }
        .nav-link:hover { background-color: #34495e; color: white !important; }
        .nav-link.active { background-color: #3498db; border-left-color: #e74c3c; color: white !important; }
        .topbar { background: white; padding: 15px 20px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .report-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; cursor: pointer; transition: all 0.3s; }
        .report-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h5>CFMS</h5>
                </div>
                <nav class="nav flex-column">
                    <a href="./dashboard.php" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                    <a href="./students.php" class="nav-link"><i class="bi bi-people"></i> Students</a>
                    <a href="./reports.php" class="nav-link active"><i class="bi bi-graph-up"></i> Reports</a>
                    <a href="./app/controllers/auth_logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="topbar">
                    <h2>Reports</h2>
                </div>

                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="report-card">
                                <h5><i class="bi bi-bar-chart"></i> Fee Collection Report</h5>
                                <p>View detailed fee collection statistics and trends</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="report-card">
                                <h5><i class="bi bi-people"></i> Student Report</h5>
                                <p>Generate student-wise fee payment reports</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="report-card">
                                <h5><i class="bi bi-receipt"></i> Receipt Report</h5>
                                <p>View all issued receipts and transaction history</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="report-card">
                                <h5><i class="bi bi-graph-down"></i> Pending Fees Report</h5>
                                <p>Track pending fee payments by student and course</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
