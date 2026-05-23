<?php
// Edit Book Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Get book ID
if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$book_id = intval($_GET['id']);

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: manage_books.php");
    exit();
}

$book = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    
    // Calculate new available quantity
    $issued_count = $book['quantity'] - $book['available_quantity'];
    $new_available = $quantity - $issued_count;
    
    if ($new_available < 0) {
        $error = "Cannot reduce quantity below currently issued books (" . $issued_count . ")";
    } else {
        // Update book using prepared statement
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, isbn = ?, quantity = ?, available_quantity = ? WHERE id = ?");
        $stmt->bind_param("ssssiii", $title, $author, $category, $isbn, $quantity, $new_available, $book_id);
        
        if ($stmt->execute()) {
            $success = "Book updated successfully!";
            // Refresh book data
            $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();
        } else {
            $error = "Error updating book";
        }
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Edit Book</h2>
    <a href="manage_books.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Books</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> Edit Book Information</h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Book Title *</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($book['title']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Author *</label>
                            <input type="text" name="author" class="form-control" required value="<?php echo htmlspecialchars($book['author']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Fiction" <?php echo $book['category'] == 'Fiction' ? 'selected' : ''; ?>>Fiction</option>
                                <option value="Non-Fiction" <?php echo $book['category'] == 'Non-Fiction' ? 'selected' : ''; ?>>Non-Fiction</option>
                                <option value="Science" <?php echo $book['category'] == 'Science' ? 'selected' : ''; ?>>Science</option>
                                <option value="Technology" <?php echo $book['category'] == 'Technology' ? 'selected' : ''; ?>>Technology</option>
                                <option value="History" <?php echo $book['category'] == 'History' ? 'selected' : ''; ?>>History</option>
                                <option value="Biography" <?php echo $book['category'] == 'Biography' ? 'selected' : ''; ?>>Biography</option>
                                <option value="Education" <?php echo $book['category'] == 'Education' ? 'selected' : ''; ?>>Education</option>
                                <option value="Other" <?php echo $book['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN *</label>
                            <input type="text" name="isbn" class="form-control" required value="<?php echo htmlspecialchars($book['isbn']); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" required min="<?php echo $book['quantity'] - $book['available_quantity']; ?>" value="<?php echo $book['quantity']; ?>">
                        <small class="text-muted">Currently issued: <?php echo $book['quantity'] - $book['available_quantity']; ?> books</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Update Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
