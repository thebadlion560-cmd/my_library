<?php
// Reports Page - Library Management System
include '../includes/header.php';

// Fetch issued books
$issued_stmt = $conn->prepare("SELECT ib.id, b.title, b.author, s.name as student_name, ib.issue_date, ib.return_date 
                                FROM issued_books ib 
                                JOIN books b ON ib.book_id = b.id 
                                JOIN students s ON ib.student_id = s.id 
                                WHERE ib.status = 'issued' 
                                ORDER BY ib.issue_date DESC");
$issued_stmt->execute();
$issued_result = $issued_stmt->get_result();

// Fetch returned books
$returned_stmt = $conn->prepare("SELECT ib.id, b.title, b.author, s.name as student_name, ib.issue_date, ib.return_date 
                                  FROM issued_books ib 
                                  JOIN books b ON ib.book_id = b.id 
                                  JOIN students s ON ib.student_id = s.id 
                                  WHERE ib.status = 'returned' 
                                  ORDER BY ib.return_date DESC");
$returned_stmt->execute();
$returned_result = $returned_stmt->get_result();

// Fetch overdue books
$today = date('Y-m-d');
$overdue_stmt = $conn->prepare("SELECT ib.id, b.title, b.author, s.name as student_name, s.email as student_email, ib.issue_date, ib.return_date 
                                 FROM issued_books ib 
                                 JOIN books b ON ib.book_id = b.id 
                                 JOIN students s ON ib.student_id = s.id 
                                 WHERE ib.status = 'issued' AND ib.return_date < ? 
                                 ORDER BY ib.return_date ASC");
$overdue_stmt->bind_param("s", $today);
$overdue_stmt->execute();
$overdue_result = $overdue_stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-earmark-bar-graph"></i> Reports</h2>
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="reportsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="issued-tab" data-bs-toggle="tab" data-bs-target="#issued" type="button" role="tab">
            <i class="bi bi-box-arrow-right"></i> Issued Books
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
            <i class="bi bi-box-arrow-in-left"></i> Returned Books
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue" type="button" role="tab">
            <i class="bi bi-exclamation-triangle"></i> Overdue Books
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="reportsTabContent">
    <!-- Issued Books Tab -->
    <div class="tab-pane fade show active" id="issued" role="tabpanel">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-box-arrow-right"></i> Currently Issued Books (<?php echo $issued_result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Student</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($issued_result->num_rows > 0): ?>
                                <?php while ($row = $issued_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['issue_date'])); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['return_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No issued books</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Returned Books Tab -->
    <div class="tab-pane fade" id="returned" role="tabpanel">
        <div class="card">
            <div class="card-header bg-success">
                <h5 class="mb-0"><i class="bi bi-box-arrow-in-left"></i> Returned Books (<?php echo $returned_result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Student</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($returned_result->num_rows > 0): ?>
                                <?php while ($row = $returned_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['issue_date'])); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['return_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No returned books</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Books Tab -->
    <div class="tab-pane fade" id="overdue" role="tabpanel">
        <div class="card">
            <div class="card-header bg-danger">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Overdue Books (<?php echo $overdue_result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Student</th>
                                <th>Student Email</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($overdue_result->num_rows > 0): ?>
                                <?php while ($row = $overdue_result->fetch_assoc()): 
                                    $due_date = new DateTime($row['return_date']);
                                    $today = new DateTime();
                                    $interval = $today->diff($due_date);
                                    $days_overdue = $interval->days;
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['issue_date'])); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['return_date'])); ?></td>
                                        <td><span class="badge bg-danger"><?php echo $days_overdue; ?> days</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No overdue books</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
