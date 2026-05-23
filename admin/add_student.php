<?php
// Add Student Page - Library Management System
include '../includes/header.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Please fill in all fields";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "A student with this email already exists";
        } else {
            // Insert student using prepared statement
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $phone);
            
            if ($stmt->execute()) {
                $success = "Student added successfully!";
            } else {
                $error = "Error adding student";
            }
        }
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-plus"></i> Add New Student</h2>
    <a href="manage_students.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Students</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Student Information</h5>
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
                        <label class="form-label">Student Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter student name">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required placeholder="Enter email address">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-control" required placeholder="Enter phone number">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
