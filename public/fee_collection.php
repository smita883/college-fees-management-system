<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';

Auth::requireLogin();
$user = Auth::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Collection - CFMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f5f7fa; }
        .sidebar { background-color: #2c3e50; color: white; min-height: 100vh; padding-top: 20px; }
        .nav-link { color: #bdc3c7 !important; padding: 12px 20px; border-left: 3px solid transparent; }
        .nav-link:hover { background-color: #34495e; color: white !important; }
        .nav-link.active { background-color: #3498db; border-left-color: #e74c3c; color: white !important; }
        .topbar { background: white; padding: 15px 20px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .content-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
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
                    <a href="./courses.php" class="nav-link"><i class="bi bi-book"></i> Courses</a>
                    <a href="./departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
                    <a href="./fee_structure.php" class="nav-link"><i class="bi bi-cash-coin"></i> Fee Structure</a>
                    <a href="./fee_collection.php" class="nav-link active"><i class="bi bi-credit-card"></i> Fee Collection</a>
                    <a href="./receipts.php" class="nav-link"><i class="bi bi-receipt"></i> Receipts</a>
                    <a href="./reports.php" class="nav-link"><i class="bi bi-graph-up"></i> Reports</a>
                    <a href="./app/controllers/auth_logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="topbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Fee Collection</h2>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                            <i class="bi bi-plus-circle"></i> Record Payment
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <div class="content-card">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2024-07-10</td>
                                        <td>John Doe (2024001)</td>
                                        <td>₹50,000</td>
                                        <td>Cash</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="bi bi-receipt"></i> Receipt</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Fee Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Student</label>
                                    <select class="form-control" required>
                                        <option>Select Student</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fee Type</label>
                                    <select class="form-control" required>
                                        <option>Select Fee</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-control">
                                        <option>Cash</option>
                                        <option>Cheque</option>
                                        <option>Online</option>
                                        <option>DD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Record Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
