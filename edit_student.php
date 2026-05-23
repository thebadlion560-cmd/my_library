<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Edit Student';

$message = '';
$messageType = '';

// Get student ID from URL
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($studentId == 0) {
    redirect('manage_students.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($phone)) {
        $message = 'Please fill in all fields';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $messageType = 'error';
    } else {
        try {
            // Check if email exists for another student
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
            $stmt->execute([$email, $studentId]);
            
            if ($stmt->fetch()) {
                $message = 'Student with this email already exists';
                $messageType = 'error';
            } else {
                // Update student using prepared statement
                $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $studentId]);
                
                $message = 'Student updated successfully!';
                $messageType = 'success';
            }
        } catch(PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get student details
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    
    if (!$student) {
        redirect('manage_students.php');
    }
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Get issued books count for this student
$stmt = $pdo->prepare("SELECT COUNT(*) FROM issued_books WHERE student_id = ? AND status = 'issued'");
$stmt->execute([$studentId]);
$issuedCount = $stmt->fetchColumn();
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="bi bi-pencil me-2"></i>Edit Student</h2>
    <p>Edit student details</p>
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

<!-- Edit Student Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-1"></i>Student Name *
                        </label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               placeholder="Enter student name" value="<?= htmlspecialchars($student['name']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Enter email address" value="<?= htmlspecialchars($student['email']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone me-1"></i>Phone Number *
                        </label>
                        <input type="text" class="form-control" id="phone" name="phone" required 
                               placeholder="Enter phone number" value="<?= htmlspecialchars($student['phone']) ?>">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="bi bi-check-circle me-1"></i> Update Student
                        </button>
                        <a href="manage_students.php" class="btn btn-secondary btn-custom">
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
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Student Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th>Registered Date:</th>
                        <td><?= date('d M Y', strtotime($student['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Books Issued:</th>
                        <td><?= $issuedCount ?></td>
                    </tr>
                </table>
                <?php if ($issuedCount > 0): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Student has <?= $issuedCount ?> book(s) currently issued.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
