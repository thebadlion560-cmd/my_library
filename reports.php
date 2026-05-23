<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Reports';

try {
    // Get issued books
    $issuedBooks = $pdo->query("
        SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
        FROM issued_books ib 
        JOIN books b ON ib.book_id = b.id 
        JOIN students s ON ib.student_id = s.id 
        WHERE ib.status = 'issued' 
        ORDER BY ib.issue_date DESC
    ")->fetchAll();
    
    // Get returned books
    $returnedBooks = $pdo->query("
        SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
        FROM issued_books ib 
        JOIN books b ON ib.book_id = b.id 
        JOIN students s ON ib.student_id = s.id 
        WHERE ib.status = 'returned' 
        ORDER BY ib.return_date DESC
        LIMIT 50
    ")->fetchAll();
    
    // Get overdue books
    $overdueBooks = $pdo->query("
        SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
        FROM issued_books ib 
        JOIN books b ON ib.book_id = b.id 
        JOIN students s ON ib.student_id = s.id 
        WHERE ib.status = 'issued' AND ib.return_date < CURDATE()
        ORDER BY ib.return_date ASC
    ")->fetchAll();
    
    // Statistics
    $totalIssued = count($issuedBooks);
    $totalReturned = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status = 'returned'")->fetchColumn();
    $totalOverdue = count($overdueBooks);
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</h2>
    <p>View library reports and statistics</p>
</div>

<?php if (isset($error)): ?>
    <?= showError($error) ?>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon issued me-3">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Currently Issued</h6>
                    <h3 class="mb-0"><?= $totalIssued ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon books me-3">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Returned</h6>
                    <h3 class="mb-0"><?= $totalReturned ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon available me-3" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Overdue Books</h6>
                    <h3 class="mb-0"><?= $totalOverdue ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for different reports -->
<ul class="nav nav-tabs mb-4" id="reportsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="issued-tab" data-bs-toggle="tab" data-bs-target="#issued" type="button">
            <i class="bi bi-box-arrow-right me-1"></i>Issued Books (<?= $totalIssued ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button">
            <i class="bi bi-check-circle me-1"></i>Returned Books
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue" type="button">
            <i class="bi bi-exclamation-triangle me-1"></i>Overdue Books (<?= $totalOverdue ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="reportsTabContent">
    <!-- Issued Books Tab -->
    <div class="tab-pane fade show active" id="issued" role="tabpanel">
        <div class="card table-custom border-0">
            <div class="card-body p-0">
                <?php if (empty($issuedBooks)): ?>
                    <div class="text-center p-5">
                        <i class="bi bi-box-arrow-right display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No books currently issued.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Student Name</th>
                                    <th>Student Email</th>
                                    <th>Issue Date</th>
                                    <th>Expected Return</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($issuedBooks as $issued): ?>
                                    <?php
                                    $isOverdue = $issued['return_date'] < date('Y-m-d');
                                    ?>
                                    <tr class="<?= $isOverdue ? 'table-warning' : '' ?>">
                                        <td><?= htmlspecialchars($issued['title']) ?></td>
                                        <td><?= htmlspecialchars($issued['author']) ?></td>
                                        <td><?= htmlspecialchars($issued['student_name']) ?></td>
                                        <td><?= htmlspecialchars($issued['student_email']) ?></td>
                                        <td><?= date('d M Y', strtotime($issued['issue_date'])) ?></td>
                                        <td>
                                            <?= date('d M Y', strtotime($issued['return_date'])) ?>
                                            <?php if ($isOverdue): ?>
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Issued</span>
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
    
    <!-- Returned Books Tab -->
    <div class="tab-pane fade" id="returned" role="tabpanel">
        <div class="card table-custom border-0">
            <div class="card-body p-0">
                <?php if (empty($returnedBooks)): ?>
                    <div class="text-center p-5">
                        <i class="bi bi-check-circle display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No returned books found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Student Name</th>
                                    <th>Student Email</th>
                                    <th>Issue Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($returnedBooks as $returned): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($returned['title']) ?></td>
                                        <td><?= htmlspecialchars($returned['author']) ?></td>
                                        <td><?= htmlspecialchars($returned['student_name']) ?></td>
                                        <td><?= htmlspecialchars($returned['student_email']) ?></td>
                                        <td><?= date('d M Y', strtotime($returned['issue_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($returned['return_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-success">Returned</span>
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
    
    <!-- Overdue Books Tab -->
    <div class="tab-pane fade" id="overdue" role="tabpanel">
        <div class="card table-custom border-0">
            <div class="card-body p-0">
                <?php if (empty($overdueBooks)): ?>
                    <div class="text-center p-5">
                        <i class="bi bi-check-circle display-4 text-success"></i>
                        <p class="mt-3 text-muted">No overdue books. Great job!</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= $totalOverdue ?> book(s) are overdue. Please follow up with students.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Student Name</th>
                                    <th>Student Email</th>
                                    <th>Issue Date</th>
                                    <th>Expected Return</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueBooks as $overdue): ?>
                                    <?php
                                    $daysOverdue = floor((time() - strtotime($overdue['return_date'])) / (60 * 60 * 24));
                                    ?>
                                    <tr class="table-danger">
                                        <td><?= htmlspecialchars($overdue['title']) ?></td>
                                        <td><?= htmlspecialchars($overdue['author']) ?></td>
                                        <td><?= htmlspecialchars($overdue['student_name']) ?></td>
                                        <td><?= htmlspecialchars($overdue['student_email']) ?></td>
                                        <td><?= date('d M Y', strtotime($overdue['issue_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($overdue['return_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-danger"><?= $daysOverdue ?> days</span>
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
