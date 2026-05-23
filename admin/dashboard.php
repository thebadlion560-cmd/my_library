<?php
// Admin Dashboard - Library Management System
include '../includes/header.php';

// Get statistics
$total_books = 0;
$total_students = 0;
$issued_books = 0;
$returned_books = 0;
$available_books = 0;

// Total books
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM books");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_books = $row['count'];

// Total students
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM students");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_students = $row['count'];

// Issued books
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM issued_books WHERE status = 'issued'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$issued_books = $row['count'];

// Returned books
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM issued_books WHERE status = 'returned'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$returned_books = $row['count'];

// Available books (sum of available_quantity)
$stmt = $conn->prepare("SELECT SUM(available_quantity) as count FROM books");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$available_books = $row['count'] ? $row['count'] : 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Books</h6>
                        <h2 class="mb-0"><?php echo $total_books; ?></h2>
                    </div>
                    <i class="bi bi-book fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Students</h6>
                        <h2 class="mb-0"><?php echo $total_students; ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Issued Books</h6>
                        <h2 class="mb-0"><?php echo $issued_books; ?></h2>
                    </div>
                    <i class="bi bi-box-arrow-right fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Available Books</h6>
                        <h2 class="mb-0"><?php echo $available_books; ?></h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Returned Books Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-stat bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Returned Books</h6>
                        <h2 class="mb-0"><?php echo $returned_books; ?></h2>
                    </div>
                    <i class="bi bi-box-arrow-in-left fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="add_book.php" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Book
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="add_student.php" class="btn btn-success w-100">
                            <i class="bi bi-person-plus"></i> Add Student
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="issue_book.php" class="btn btn-warning w-100">
                            <i class="bi bi-box-arrow-right"></i> Issue Book
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="return_book.php" class="btn btn-info w-100">
                            <i class="bi bi-box-arrow-in-left"></i> Return Book
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
