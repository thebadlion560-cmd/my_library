<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

// Page title
$pageTitle = 'Dashboard';

try {
    // Get statistics
    $totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $issuedBooks = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status = 'issued'")->fetchColumn();
    $returnedBooks = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status = 'returned'")->fetchColumn();
    $availableBooks = $pdo->query("SELECT SUM(available_quantity) FROM books")->fetchColumn();
    
    // Get recent issued books
    $recentIssued = $pdo->query("
        SELECT ib.*, b.title, s.name as student_name 
        FROM issued_books ib 
        JOIN books b ON ib.book_id = b.id 
        JOIN students s ON ib.student_id = s.id 
        ORDER BY ib.created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
    <p>Welcome back, <?= htmlspecialchars(getCurrentAdminName()) ?>!</p>
</div>

<?php if (isset($error)): ?>
    <?= showError($error) ?>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon books me-3">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Books</h6>
                    <h3 class="mb-0"><?= $totalBooks ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon students me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <h3 class="mb-0"><?= $totalStudents ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon issued me-3">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Issued Books</h6>
                    <h3 class="mb-0"><?= $issuedBooks ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon available me-3">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Available Books</h6>
                    <h3 class="mb-0"><?= $availableBooks ?: 0 ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="add_book.php" class="btn btn-primary-custom btn-custom">
                        <i class="bi bi-plus-circle me-1"></i> Add Book
                    </a>
                    <a href="add_student.php" class="btn btn-success btn-custom">
                        <i class="bi bi-person-plus me-1"></i> Add Student
                    </a>
                    <a href="issue_book.php" class="btn btn-warning btn-custom">
                        <i class="bi bi-box-arrow-right me-1"></i> Issue Book
                    </a>
                    <a href="return_book.php" class="btn btn-info btn-custom">
                        <i class="bi bi-box-arrow-in-left me-1"></i> Return Book
                    </a>
                    <a href="reports.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
                
                <?php if (empty($recentIssued)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>No recent activity found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Student</th>
                                    <th>Issue Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentIssued as $activity): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($activity['title']) ?></td>
                                        <td><?= htmlspecialchars($activity['student_name']) ?></td>
                                        <td><?= date('d M Y', strtotime($activity['issue_date'])) ?></td>
                                        <td>
                                            <?php if ($activity['status'] == 'issued'): ?>
                                                <span class="badge bg-warning">Issued</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Returned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
