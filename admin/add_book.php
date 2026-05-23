<?php
// Add Book Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    $added_date = date('Y-m-d');
    
    // Validation
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || $quantity <= 0) {
        $error = "Please fill in all fields with valid values";
    } else {
        // Check if ISBN already exists
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "A book with this ISBN already exists";
        } else {
            // Insert book using prepared statement
            $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, quantity, available_quantity, added_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiis", $title, $author, $category, $isbn, $quantity, $quantity, $added_date);
            
            if ($stmt->execute()) {
                $success = "Book added successfully!";
            } else {
                $error = "Error adding book";
            }
        }
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Add New Book</h2>
    <a href="manage_books.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Books</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> Book Information</h5>
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
                            <input type="text" name="title" class="form-control" required placeholder="Enter book title">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Author *</label>
                            <input type="text" name="author" class="form-control" required placeholder="Enter author name">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Fiction">Fiction</option>
                                <option value="Non-Fiction">Non-Fiction</option>
                                <option value="Science">Science</option>
                                <option value="Technology">Technology</option>
                                <option value="History">History</option>
                                <option value="Biography">Biography</option>
                                <option value="Education">Education</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN *</label>
                            <input type="text" name="isbn" class="form-control" required placeholder="Enter ISBN number">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" required min="1" value="1">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
