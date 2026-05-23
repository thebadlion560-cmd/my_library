<?php
// Issue Book Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $student_id = intval($_POST['student_id']);
    $return_date = $_POST['return_date'];
    $issue_date = date('Y-m-d');
    
    // Validation
    if (empty($book_id) || empty($student_id) || empty($return_date)) {
        $error = "Please fill in all fields";
    } else {
        // Check book availability
        $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        if ($book['available_quantity'] <= 0) {
            $error = "This book is not available";
        } else {
            // Insert issued book record
            $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_id, issue_date, return_date, status) VALUES (?, ?, ?, ?, 'issued')");
            $stmt->bind_param("iiss", $book_id, $student_id, $issue_date, $return_date);
            
            if ($stmt->execute()) {
                // Decrease available quantity
                $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                
                $success = "Book issued successfully!";
            } else {
                $error = "Error issuing book";
            }
        }
        $stmt->close();
    }
}

// Fetch available books
$books_query = "SELECT id, title, author, available_quantity FROM books WHERE available_quantity > 0 ORDER BY title ASC";
$books_result = $conn->query($books_query);

// Fetch students
$students_query = "SELECT id, name, email FROM students ORDER BY name ASC";
$students_result = $conn->query($students_query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-right"></i> Issue Book</h2>
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> Issue Book to Student</h5>
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
                    <div class="mb-3">
                        <label class="form-label">Select Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            <?php if ($students_result->num_rows > 0): ?>
                                <?php while ($student = $students_result->fetch_assoc()): ?>
                                    <option value="<?php echo $student['id']; ?>">
                                        <?php echo htmlspecialchars($student['name']) . ' (' . htmlspecialchars($student['email']) . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select Book *</label>
                        <select name="book_id" class="form-select" required>
                            <option value="">Select Book</option>
                            <?php if ($books_result->num_rows > 0): ?>
                                <?php while ($book = $books_result->fetch_assoc()): ?>
                                    <option value="<?php echo $book['id']; ?>">
                                        <?php echo htmlspecialchars($book['title']) . ' - ' . htmlspecialchars($book['author']) . ' (Available: ' . $book['available_quantity'] . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="" disabled>No books available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Return Date *</label>
                        <input type="date" name="return_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-box-arrow-right"></i> Issue Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
