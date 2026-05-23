<?php
// Header File - Library Management System
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .card-stat {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .table-responsive {
                padding: 10px;
                font-size: 0.9rem;
            }
            .table-responsive .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
            .card-stat {
                margin-bottom: 1rem;
            }
            .navbar-brand {
                font-size: 1rem;
            }
            .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.95rem;
            }
            .d-flex .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
        }
        @media (max-width: 576px) {
            .table-responsive {
                padding: 5px;
                font-size: 0.8rem;
            }
            .table th, .table td {
                padding: 0.5rem;
            }
            .card-body {
                padding: 1rem;
            }
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php if (basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-none d-md-block">
            <h4 class="text-white mb-4"><i class="bi bi-book"></i> Library</h4>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="manage_books.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_books.php' || basename($_SERVER['PHP_SELF']) == 'add_book.php' || basename($_SERVER['PHP_SELF']) == 'edit_book.php' ? 'active' : ''; ?>">
                <i class="bi bi-book"></i> Books
            </a>
            <a href="manage_students.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_students.php' || basename($_SERVER['PHP_SELF']) == 'add_student.php' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Students
            </a>
            <a href="issue_book.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'issue_book.php' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-right"></i> Issue Book
            </a>
            <a href="return_book.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'return_book.php' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-in-left"></i> Return Book
            </a>
            <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
            <a href="logout.php">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </div>

        <!-- Mobile Navigation -->
        <nav class="navbar navbar-dark bg-dark d-md-none">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span class="navbar-brand"><i class="bi bi-book"></i> Library</span>
                <div class="collapse navbar-collapse" id="mobileNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="manage_books.php">Books</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="manage_students.php">Students</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="issue_book.php">Issue Book</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="return_book.php">Return Book</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="reports.php">Reports</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
<?php endif; ?>
