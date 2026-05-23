<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Add Book';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $category = sanitize($_POST['category']);
    $isbn = sanitize($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    $added_date = date('Y-m-d');
    
    // Validate input
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || empty($quantity)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } elseif ($quantity < 1) {
        $message = 'Quantity must be at least 1';
        $messageType = 'error';
    } else {
        try {
            // Check if ISBN already exists
            $stmt = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
            $stmt->execute([$isbn]);
            
            if ($stmt->fetch()) {
                $message = 'Book with this ISBN already exists';
                $messageType = 'error';
            } else {
                // Insert new book using prepared statement
                $stmt = $pdo->prepare("
                    INSERT INTO books (title, author, category, isbn, quantity, available_quantity, added_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $author, $category, $isbn, $quantity, $quantity, $added_date]);
                
                $message = 'Book added successfully!';
                $messageType = 'success';
            }
        } catch(PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-plus-circle me-2"></i>Add New Book</h2>
    <p>Add a new book to the library</p>
</div>

<?php if ($message): ?>
    <?php if ($messageType == 'error'): ?>
        <?= showError($message) ?>
    <?php else: ?>
        <?= showSuccess($message) ?>
    <?php endif; ?>
<?php endif; ?>

<!-- Add Book Form -->
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
                                   placeholder="Enter book title" value="<?= htmlspecialchars($title ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">
                                <i class="bi bi-person me-1"></i>Author *
                            </label>
                            <input type="text" class="form-control" id="author" name="author" required 
                                   placeholder="Enter author name" value="<?= htmlspecialchars($author ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">
                                <i class="bi bi-tag me-1"></i>Category *
                            </label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Computer Science" <?= (isset($category) && $category == 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
                                <option value="Mathematics" <?= (isset($category) && $category == 'Mathematics') ? 'selected' : '' ?>>Mathematics</option>
                                <option value="Physics" <?= (isset($category) && $category == 'Physics') ? 'selected' : '' ?>>Physics</option>
                                <option value="Chemistry" <?= (isset($category) && $category == 'Chemistry') ? 'selected' : '' ?>>Chemistry</option>
                                <option value="Literature" <?= (isset($category) && $category == 'Literature') ? 'selected' : '' ?>>Literature</option>
                                <option value="History" <?= (isset($category) && $category == 'History') ? 'selected' : '' ?>>History</option>
                                <option value="Other" <?= (isset($category) && $category == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">
                                <i class="bi bi-upc-scan me-1"></i>ISBN *
                            </label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required 
                                   placeholder="Enter ISBN number" value="<?= htmlspecialchars($isbn ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">
                            <i class="bi bi-stack me-1"></i>Quantity *
                        </label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required 
                               min="1" placeholder="Enter quantity" value="<?= htmlspecialchars($quantity ?? '') ?>">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="bi bi-plus-circle me-1"></i> Add Book
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
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Instructions</h5>
                <ul class="mb-0">
                    <li>All fields marked with * are required</li>
                    <li>ISBN must be unique for each book</li>
                    <li>Quantity must be at least 1</li>
                    <li>Available quantity will be set equal to total quantity</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
