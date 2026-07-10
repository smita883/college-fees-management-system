<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/StudentController.php';

Auth::requireLogin();
$user = Auth::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - CFMS</title>
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
                    <a href="./students.php" class="nav-link active"><i class="bi bi-people"></i> Students</a>
                    <a href="./courses.php" class="nav-link"><i class="bi bi-book"></i> Courses</a>
                    <a href="./departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
                    <a href="./fee_structure.php" class="nav-link"><i class="bi bi-cash-coin"></i> Fee Structure</a>
                    <a href="./fee_collection.php" class="nav-link"><i class="bi bi-credit-card"></i> Fee Collection</a>
                    <a href="./receipts.php" class="nav-link"><i class="bi bi-receipt"></i> Receipts</a>
                    <a href="./reports.php" class="nav-link"><i class="bi bi-graph-up"></i> Reports</a>
                    <a href="./users.php" class="nav-link"><i class="bi bi-person-gear"></i> Users</a>
                    <a href="./app/controllers/auth_logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="topbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Student Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="bi bi-plus-circle"></i> Add Student
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <div class="content-card">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Roll Number</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Course</th>
                                        <th>Department</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2024001</td>
                                        <td>John Doe</td>
                                        <td>john@example.com</td>
                                        <td>B.Tech CSE</td>
                                        <td>Computer Science</td>
                                        <td>
                                            <button class="btn btn-sm btn-info"><i class="bi bi-pencil"></i> Edit</button>
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Roll Number</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Course</label>
                                    <select class="form-control" required>
                                        <option>Select Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-control" required>
                                        <option>Select Department</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
