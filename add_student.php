<?php
session_start();
require_once 'db.php';
require_once 'layout.php';

$pageTitle = 'Add Student';

$message = '';
$messageType = '';

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
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = 'Student with this email already exists';
                $messageType = 'error';
            } else {
                // Insert new student using prepared statement
                $stmt = $pdo->prepare("INSERT INTO students (name, email, phone) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $phone]);
                
                $message = 'Student added successfully!';
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
    <h2><i class="bi bi-person-plus me-2"></i>Add New Student</h2>
    <p>Add a new student to the library</p>
</div>

<?php if ($message): ?>
    <?php if ($messageType == 'error'): ?>
        <?= showError($message) ?>
    <?php else: ?>
        <?= showSuccess($message) ?>
    <?php endif; ?>
<?php endif; ?>

<!-- Add Student Form -->
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
                               placeholder="Enter student name" value="<?= htmlspecialchars($name ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Enter email address" value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone me-1"></i>Phone Number *
                        </label>
                        <input type="text" class="form-control" id="phone" name="phone" required 
                               placeholder="Enter phone number" value="<?= htmlspecialchars($phone ?? '') ?>">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-custom">
                            <i class="bi bi-plus-circle me-1"></i> Add Student
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
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Instructions</h5>
                <ul class="mb-0">
                    <li>All fields marked with * are required</li>
                    <li>Email must be unique for each student</li>
                    <li>Phone number should be valid</li>
                    <li>Students can be issued books once added</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Close layout -->
    </div>
</body>
</html>
