<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Issue Book';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookId = intval($_POST['book_id']);
    $studentId = intval($_POST['student_id']);
    $returnDate = sanitize($_POST['return_date']);
    $issueDate = date('Y-m-d');
    
    // Validate input
    if (empty($bookId) || empty($studentId) || empty($returnDate)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } elseif ($returnDate < $issueDate) {
        $message = 'Return date cannot be before issue date';
        $messageType = 'error';
    } else {
        try {
            // Check if book is available
            $stmt = $pdo->prepare("SELECT available_quantity, title FROM books WHERE id = ?");
            $stmt->execute([$bookId]);
            $book = $stmt->fetch();
            
            if (!$book) {
                $message = 'Book not found';
                $messageType = 'error';
            } elseif ($book['available_quantity'] <= 0) {
                $message = 'Book is not available';
                $messageType = 'error';
            } else {
                // Check if student already has this book issued
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) FROM issued_books 
                    WHERE book_id = ? AND student_id = ? AND status = 'issued'
                ");
                $stmt->execute([$bookId, $studentId]);
                
                if ($stmt->fetchColumn() > 0) {
                    $message = 'Student already has this book issued';
                    $messageType = 'error';
                } else {
                    // Issue book using prepared statement
                    $stmt = $pdo->prepare("
                        INSERT INTO issued_books (book_id, student_id, issue_date, return_date, status) 
                        VALUES (?, ?, ?, ?, 'issued')
                    ");
                    $stmt->execute([$bookId, $studentId, $issueDate, $returnDate]);
                    
                    // Decrease available quantity
                    $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
                    $stmt->execute([$bookId]);
                    
                    $message = 'Book issued successfully to student!';
                    $messageType = 'success';
                }
            }
        } catch(PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

try {
    // Get available books
    $books = $pdo->query("SELECT id, title, author, available_quantity FROM books WHERE available_quantity > 0 ORDER BY title")->fetchAll();
    
    // Get all students
    $students = $pdo->query("SELECT id, name, email FROM students ORDER BY name")->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-box-arrow-right me-2"></i>Issue Book</h2>
    <p>Issue a book to a student</p>
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

<!-- Issue Book Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="book_id" class="form-label">
                            <i class="bi bi-book me-1"></i>Select Book *
                        </label>
                        <select class="form-select" id="book_id" name="book_id" required>
                            <option value="">Select a book</option>
                            <?php foreach ($books as $book): ?>
                                <option value="<?= $book['id'] ?>">
                                    <?= htmlspecialchars($book['title']) ?> by <?= htmlspecialchars($book['author']) ?> 
                                    (Available: <?= $book['available_quantity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="student_id" class="form-label">
                            <i class="bi bi-person me-1"></i>Select Student *
                        </label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Select a student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= htmlspecialchars($student['name']) ?> (<?= htmlspecialchars($student['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="return_date" class="form-label">
                            <i class="bi bi-calendar-check me-1"></i>Expected Return Date *
                        </label>
                        <input type="date" class="form-control" id="return_date" name="return_date" required 
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="bi bi-box-arrow-right me-1"></i> Issue Book
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary btn-custom">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Instructions</h5>
                <ul class="mb-0">
                    <li>Select a book from the dropdown</li>
                    <li>Only available books are shown</li>
                    <li>Select a student to issue the book</li>
                    <li>Set expected return date</li>
                    <li>Available quantity will be decreased automatically</li>
                </ul>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Statistics</h5>
                <table class="table table-sm mb-0">
                    <tr>
                        <th>Available Books:</th>
                        <td><?= count($books) ?></td>
                    </tr>
                    <tr>
                        <th>Total Students:</th>
                        <td><?= count($students) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
