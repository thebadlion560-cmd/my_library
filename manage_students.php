<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Manage Students';

$message = '';
$messageType = '';

// Handle delete request
if (isset($_GET['delete'])) {
    $studentId = intval($_GET['delete']);
    try {
        // Check if student has issued books
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE student_id = ? AND status = 'issued'");
        $stmt->execute([$studentId]);
        $issuedCount = $stmt->fetchColumn();
        
        if ($issuedCount > 0) {
            $message = 'Cannot delete student. They have currently issued books.';
            $messageType = 'error';
        } else {
            // Delete student using prepared statement
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$studentId]);
            $message = 'Student deleted successfully!';
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
    // Get students with optional search
    if ($search) {
        $stmt = $pdo->prepare("
            SELECT * FROM students 
            WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? 
            ORDER BY created_at DESC
        ");
        $searchParam = "%$search%";
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
    } else {
        $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
    }
    $students = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-people me-2"></i>Manage Students</h2>
    <p>View, edit, and delete students</p>
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
                <input type="text" class="form-control" name="search" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if ($search): ?>
                    <a href="manage_students.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="col-md-4 text-end">
        <a href="add_student.php" class="btn btn-success btn-custom">
            <i class="bi bi-person-plus me-1"></i> Add New Student
        </a>
    </div>
</div>

<!-- Students Table -->
<div class="card table-custom border-0">
    <div class="card-body p-0">
        <?php if (empty($students)): ?>
            <div class="text-center p-5">
                <i class="bi bi-people display-4 text-muted"></i>
                <p class="mt-3 text-muted">No students found.</p>
                <a href="add_student.php" class="btn btn-primary-custom btn-custom">Add Your First Student</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered Date</th>
                            <th>Books Issued</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php
                            // Get issued books count for this student
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE student_id = ? AND status = 'issued'");
                            $stmt->execute([$student['id']]);
                            $issuedCount = $stmt->fetchColumn();
                            ?>
                            <tr>
                                <td><?= $student['id'] ?></td>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['phone']) ?></td>
                                <td><?= date('d M Y', strtotime($student['created_at'])) ?></td>
                                <td>
                                    <?php if ($issuedCount > 0): ?>
                                        <span class="badge bg-warning"><?= $issuedCount ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-info" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="manage_students.php?delete=<?= $student['id'] ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student?');">
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
