<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Return Book';

$message = '';
$messageType = '';

// Handle return request
if (isset($_GET['return'])) {
    $issuedId = intval($_GET['return']);
    try {
        // Get issued book details
        $stmt = $pdo->prepare("
            SELECT ib.*, b.title, s.name as student_name 
            FROM issued_books ib 
            JOIN books b ON ib.book_id = b.id 
            JOIN students s ON ib.student_id = s.id 
            WHERE ib.id = ? AND ib.status = 'issued'
        ");
        $stmt->execute([$issuedId]);
        $issuedBook = $stmt->fetch();
        
        if (!$issuedBook) {
            $message = 'Issued book not found or already returned';
            $messageType = 'error';
        } else {
            // Mark as returned
            $stmt = $pdo->prepare("UPDATE issued_books SET status = 'returned', return_date = ? WHERE id = ?");
            $stmt->execute([date('Y-m-d'), $issuedId]);
            
            // Increase available quantity
            $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->execute([$issuedBook['book_id']]);
            
            $message = 'Book returned successfully!';
            $messageType = 'success';
        }
    } catch(PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

try {
    // Get all issued books
    $stmt = $pdo->query("
        SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
        FROM issued_books ib 
        JOIN books b ON ib.book_id = b.id 
        JOIN students s ON ib.student_id = s.id 
        WHERE ib.status = 'issued' 
        ORDER BY ib.issue_date DESC
    ");
    $issuedBooks = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-box-arrow-in-left me-2"></i>Return Book</h2>
    <p>Mark issued books as returned</p>
</div>

<?php if ($message): ?>
    <?php if ($messageType == 'error'): ?>
        <?= showError($message) ?>
    <?php else: ?>
        <?= showSuccess($message) ?>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($error)): ?>
    <?= showError($error) ?>
<?php endif; ?>

<!-- Issued Books Table -->
<div class="card table-custom border-0">
    <div class="card-body p-0">
        <?php if (empty($issuedBooks)): ?>
            <div class="text-center p-5">
                <i class="bi bi-box-arrow-in-left display-4 text-muted"></i>
                <p class="mt-3 text-muted">No books currently issued.</p>
                <a href="issue_book.php" class="btn btn-primary-custom btn-custom">Issue a Book</a>
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
                            <th>Action</th>
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
                                <td>
                                    <a href="return_book.php?return=<?= $issued['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to mark this book as returned?');">
                                        <i class="bi bi-check-circle"></i> Return
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics -->
<?php if (!empty($issuedBooks)): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Statistics</h5>
                    <table class="table table-sm mb-0">
                        <tr>
                            <th>Total Issued:</th>
                            <td><?= count($issuedBooks) ?></td>
                        </tr>
                        <?php
                        $overdueCount = 0;
                        foreach ($issuedBooks as $issued) {
                            if ($issued['return_date'] < date('Y-m-d')) {
                                $overdueCount++;
                            }
                        }
                        ?>
                        <tr>
                            <th>Overdue:</th>
                            <td><span class="badge bg-danger"><?= $overdueCount ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Close layout -->
    </div>
</body>
</html>
