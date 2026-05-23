<?php
// Return Book Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Handle return request
if (isset($_GET['return'])) {
    $issue_id = intval($_GET['return']);
    
    // Get issue details
    $stmt = $conn->prepare("SELECT book_id, student_id FROM issued_books WHERE id = ? AND status = 'issued'");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $issue = $result->fetch_assoc();
        $book_id = $issue['book_id'];
        
        // Update issued_books status
        $stmt = $conn->prepare("UPDATE issued_books SET status = 'returned' WHERE id = ?");
        $stmt->bind_param("i", $issue_id);
        
        if ($stmt->execute()) {
            // Increase available quantity
            $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            
            $success = "Book returned successfully!";
        } else {
            $error = "Error returning book";
        }
    } else {
        $error = "Invalid issue record";
    }
    $stmt->close();
}

// Fetch issued books
$stmt = $conn->prepare("SELECT ib.id, b.title, b.author, s.name as student_name, s.email as student_email, ib.issue_date, ib.return_date 
                        FROM issued_books ib 
                        JOIN books b ON ib.book_id = b.id 
                        JOIN students s ON ib.student_id = s.id 
                        WHERE ib.status = 'issued' 
                        ORDER BY ib.issue_date DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-in-left"></i> Return Book</h2>
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
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

<!-- Issued Books Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Student Name</th>
                <th>Student Email</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['issue_date'])); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['return_date'])); ?></td>
                        <td>
                            <?php 
                            $today = date('Y-m-d');
                            $due_date = date('Y-m-d', strtotime($row['return_date']));
                            if ($today > $due_date): 
                                echo '<span class="badge bg-danger">Overdue</span>';
                            else: 
                                echo '<span class="badge bg-warning">Issued</span>';
                            endif; 
                            ?>
                        </td>
                        <td>
                            <a href="return_book.php?return=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to mark this book as returned?');">
                                <i class="bi bi-check-circle"></i> Return
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No issued books found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
