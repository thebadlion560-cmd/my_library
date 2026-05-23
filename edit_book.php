<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Edit Book';

$message = '';
$messageType = '';

// Get book ID from URL
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookId == 0) {
    redirect('manage_books.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $category = sanitize($_POST['category']);
    $isbn = sanitize($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    
    // Validate input
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || empty($quantity)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } elseif ($quantity < 1) {
        $message = 'Quantity must be at least 1';
        $messageType = 'error';
    } else {
        try {
            // Check if ISBN exists for another book
            $stmt = $pdo->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
            $stmt->execute([$isbn, $bookId]);
            
            if ($stmt->fetch()) {
                $message = 'Book with this ISBN already exists';
                $messageType = 'error';
            } else {
                // Get current available quantity
                $stmt = $pdo->prepare("SELECT available_quantity FROM books WHERE id = ?");
                $stmt->execute([$bookId]);
                $currentAvailable = $stmt->fetchColumn();
                
                // Calculate new available quantity
                $issuedCount = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE book_id = $bookId AND status = 'issued'")->fetchColumn();
                $newAvailable = $quantity - $issuedCount;
                
                if ($newAvailable < 0) {
                    $message = 'Cannot reduce quantity below currently issued books (' . $issuedCount . ')';
                    $messageType = 'error';
                } else {
                    // Update book using prepared statement
                    $stmt = $pdo->prepare("
                        UPDATE books 
                        SET title = ?, author = ?, category = ?, isbn = ?, quantity = ?, available_quantity = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $author, $category, $isbn, $quantity, $newAvailable, $bookId]);
                    
                    $message = 'Book updated successfully!';
                    $messageType = 'success';
                }
            }
        } catch(PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get book details
try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        redirect('manage_books.php');
    }
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-pencil me-2"></i>Edit Book</h2>
    <p>Edit book details</p>
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

<!-- Edit Book Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-book me-1"></i>Book Title *
                            </label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   placeholder="Enter book title" value="<?= htmlspecialchars($book['title']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">
                                <i class="bi bi-person me-1"></i>Author *
                            </label>
                            <input type="text" class="form-control" id="author" name="author" required 
                                   placeholder="Enter author name" value="<?= htmlspecialchars($book['author']) ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">
                                <i class="bi bi-tag me-1"></i>Category *
                            </label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Computer Science" <?= $book['category'] == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                                <option value="Mathematics" <?= $book['category'] == 'Mathematics' ? 'selected' : '' ?>>Mathematics</option>
                                <option value="Physics" <?= $book['category'] == 'Physics' ? 'selected' : '' ?>>Physics</option>
                                <option value="Chemistry" <?= $book['category'] == 'Chemistry' ? 'selected' : '' ?>>Chemistry</option>
                                <option value="Literature" <?= $book['category'] == 'Literature' ? 'selected' : '' ?>>Literature</option>
                                <option value="History" <?= $book['category'] == 'History' ? 'selected' : '' ?>>History</option>
                                <option value="Other" <?= $book['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">
                                <i class="bi bi-upc-scan me-1"></i>ISBN *
                            </label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required 
                                   placeholder="Enter ISBN number" value="<?= htmlspecialchars($book['isbn']) ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">
                            <i class="bi bi-stack me-1"></i>Total Quantity *
                        </label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required 
                               min="1" placeholder="Enter quantity" value="<?= $book['quantity'] ?>">
                        <small class="text-muted">Currently available: <?= $book['available_quantity'] ?></small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="bi bi-check-circle me-1"></i> Update Book
                        </button>
                        <a href="manage_books.php" class="btn btn-secondary btn-custom">
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
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Book Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th>Added Date:</th>
                        <td><?= date('d M Y', strtotime($book['added_date'])) ?></td>
                    </tr>
                    <tr>
                        <th>Available:</th>
                        <td><?= $book['available_quantity'] ?></td>
                    </tr>
                    <tr>
                        <th>Issued:</th>
                        <td><?= $book['quantity'] - $book['available_quantity'] ?></td>
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
