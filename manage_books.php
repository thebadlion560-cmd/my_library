<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Manage Books';

$message = '';
$messageType = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $bookId = intval($_GET['delete']);
    try {
        // Check if book is issued
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE book_id = ? AND status = 'issued'");
        $stmt->execute([$bookId]);
        $issuedCount = $stmt->fetchColumn();
        
        if ($issuedCount > 0) {
            $message = 'Cannot delete book. It is currently issued to a student.';
            $messageType = 'error';
        } else {
            // Delete book using prepared statement
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
            $stmt->execute([$bookId]);
            $message = 'Book deleted successfully!';
            $messageType = 'success';
        }
    } catch(PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

try {
    // Get books with optional search
    if ($search) {
        $stmt = $pdo->prepare("
            SELECT * FROM books 
            WHERE title LIKE ? OR author LIKE ? OR category LIKE ? OR isbn LIKE ? 
            ORDER BY added_date DESC
        ");
        $searchParam = "%$search%";
        $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
    } else {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY added_date DESC");
    }
    $books = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-book me-2"></i>Manage Books</h2>
    <p>View, edit, and delete books</p>
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

<!-- Search and Add Button -->
<div class="row mb-4">
    <div class="col-md-8">
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by title, author, category, or ISBN..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($search): ?>
                    <a href="manage_books.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="col-md-4 text-end">
        <a href="add_book.php" class="btn btn-success btn-custom">
            <i class="bi bi-plus-circle me-1"></i> Add New Book
        </a>
    </div>
</div>

<!-- Books Table -->
<div class="card table-custom border-0">
    <div class="card-body p-0">
        <?php if (empty($books)): ?>
            <div class="text-center p-5">
                <i class="bi bi-book display-4 text-muted"></i>
                <p class="mt-3 text-muted">No books found.</p>
                <a href="add_book.php" class="btn btn-primary-custom btn-custom">Add Your First Book</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>ISBN</th>
                            <th>Total</th>
                            <th>Available</th>
                            <th>Added Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= $book['id'] ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($book['category']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($book['isbn']) ?></td>
                                <td><?= $book['quantity'] ?></td>
                                <td>
                                    <?php if ($book['available_quantity'] > 0): ?>
                                        <span class="badge bg-success"><?= $book['available_quantity'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">0</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($book['added_date'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-info" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="manage_books.php?delete=<?= $book['id'] ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this book?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
