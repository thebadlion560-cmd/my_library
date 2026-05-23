<?php
// Manage Books Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    
    // Check if book is issued
    $stmt = $conn->prepare("SELECT id FROM issued_books WHERE book_id = ? AND status = 'issued'");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Cannot delete book - it is currently issued to a student";
    } else {
        // Delete book
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        
        if ($stmt->execute()) {
            $success = "Book deleted successfully!";
        } else {
            $error = "Error deleting book";
        }
    }
    $stmt->close();
}

// Handle search
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Fetch books
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? ORDER BY id DESC");
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM books ORDER BY id DESC");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Manage Books</h2>
    <a href="add_book.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New Book</a>
</div>

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

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, or ISBN..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Books Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>ISBN</th>
                <th>Quantity</th>
                <th>Available</th>
                <th>Added Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <span class="badge <?php echo $row['available_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $row['available_quantity']; ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($row['added_date'])); ?></td>
                        <td>
                            <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="manage_books.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No books found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
