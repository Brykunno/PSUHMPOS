<?php
$conn = new mysqli("localhost", "root", "", "psuhm_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Insert
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === 'create') {
    $name = $conn->real_escape_string($_POST['name']);
    $percentage = floatval($_POST['percentage']);
    if (!empty($name) && $percentage > 0) {
        $conn->query("INSERT INTO discount_list (name, percentage) VALUES ('$name', $percentage)");
        $_SESSION['toast'] = "Discount successfully added!";
    }
}

// Update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === 'update') {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $percentage = floatval($_POST['percentage']);
    if (!empty($name) && $percentage > 0) {
        $conn->query("UPDATE discount_list SET name='$name', percentage=$percentage WHERE id=$id");
        $_SESSION['toast'] = "Discount successfully updated!";
    }
}

// Delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM discount_list WHERE id=$id");
    $_SESSION['toast'] = "Discount deleted successfully!";
}

$discounts = $conn->query("SELECT * FROM discount_list ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discount Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', sans-serif;
    }
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: #5b9bd5;
        color: white;
        font-weight: bold;
        font-size: 1.3rem;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }
    .table thead th {
        background-color: #5b9bd5;
        color: white !important;
        text-align: center;
    }
    .table tbody td {
        color: black !important;
    }
    .table tbody tr:hover {
        background-color: #eef4fb;
    }
    .badge-success {
        background: linear-gradient(to right, #4caf50, #81c784);
        color: white;
    }
    #create_new {
        background-color: #5b9bd5;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 0.9rem 1rem;
        font-weight: 500;
        font-size: 0.75rem;
        transition: transform 0.2s ease;
        margin-left: auto;
    }
    #create_new:hover {
        transform: scale(1.05);
    }
    .modal-content {
        border-radius: 1rem;
    }
    .btn-primary,
    .btn-success {
        background:rgb(0, 179, 68);
        border: none;
    }
    .btn-danger {
        background: linear-gradient(to right, #ff416c, #ff4b2b);
        border: none;
    }
    .btn-group .btn {
        transition: 0.2s ease;
        color: black;
        background-color:rgb(255, 255, 255);
        border: none;
    }

    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">List of Discounts</h6>
            <button id="create_new" data-toggle="modal" data-target="#discountModal" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Discount</button>
        </div>
        <div class="card-body">
            <input type="search" id="searchBox" class="form-control mb-3" placeholder="Search discount...">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Percentage</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="discountTable">
                    <?php while($row = $discounts->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge badge-success"><?= $row['percentage'] ?>%</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-sm" style="background-color: #007bff; color: white;" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button class="btn btn-danger" onclick="return confirm('Delete this discount?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="discountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discount</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="discount_id">
                <input type="hidden" name="action" id="form_action">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="discount_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Percentage</label>
                    <input type="number" name="percentage" id="discount_percentage" class="form-control" step="0.01" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['toast'])): ?>
<div class="toast-container position-fixed bottom-0 right-0 p-3" style="z-index: 1080; right: 20px; bottom: 20px;">
  <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white">
      <strong class="mr-auto"><i class="fa fa-check-circle mr-2"></i>Success</strong>
      <small>Now</small>
      <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      <?= $_SESSION['toast'] ?>
    </div>
  </div>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openAddModal() {
    document.getElementById('discount_id').value = '';
    document.getElementById('discount_name').value = '';
    document.getElementById('discount_percentage').value = '';
    document.getElementById('form_action').value = 'create';
    document.querySelector('.modal-title').innerText = 'Add Discount';
}
function openEditModal(data) {
    document.getElementById('discount_id').value = data.id;
    document.getElementById('discount_name').value = data.name;
    document.getElementById('discount_percentage').value = data.percentage;
    document.getElementById('form_action').value = 'update';
    document.querySelector('.modal-title').innerText = 'Edit Discount';
    $('#discountModal').modal('show');
}
$(document).ready(function () {
    $('.toast').toast({ delay: 3000 }).toast('show');
});
</script>
</body>
</html>
