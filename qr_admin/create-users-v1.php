<?php
session_start(); // Start the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['directorate']) || $_SESSION['role'] !== 'admin') {
    header("location: index.php");
    exit;
}

include "../config.php";

// Fetch all users from database
$users_query = "SELECT u.*, d.directorate_name 
                FROM users u 
                LEFT JOIN directorates d ON u.directorate = d.id 
                ORDER BY u.created_at DESC";
$users_result = mysqli_query($conn, $users_query);
$users = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include "template/head.php"; ?>

<body>
    <div class="wrapper">
        <?php include 'template/sidebar.php'; ?>

        <div class="main-panel">
            <?php include 'template/main-header.php'; ?>

            <div class="container">
                <div class="page-inner">
                    <!-- Create User Form -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Create New User</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <form action="control/user_process-v1.php?action=add" method="POST">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="username" name="username"
                                                        placeholder="Enter Username" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input
                                                        type="email"
                                                        class="form-control"
                                                        id="email" name="email"
                                                        placeholder="Enter Email" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input
                                                        type="password"
                                                        class="form-control" name="password"
                                                        id="password"
                                                        placeholder="Password" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="directorate">Select Directorate</label>
                                                    <select
                                                        class="form-select"
                                                        id="directorate" name="directorate" required>
                                                        <option value="" selected disabled>Select a directorate</option>
                                                        <option value="1">Directorate of Logistics</option>
                                                        <option value="2">Directorate of Health Services</option>
                                                        <option value="6">Directorate of Admin</option>
                                                        <option value="8">Directorate of Training</option>
                                                        <option value="3">Directorate of Air Operations</option>
                                                        <option value="4">Directorate of Civil Engineering</option>
                                                        <option value="5">Directorate of Electronics and Computer Engineering</option>
                                                        <option value="7">Flight Safety Inspectorate</option>
                                                        <option value="9">Directorate of Aeronautical Engineering</option>
                                                        <option value="10">Basic Trade Course TTS EKA - Directorate of Training</option>
                                                        <option value="11">Advanced Trade Course TTS EKA - Directorate of Training</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="role">User Role</label>
                                                    <select class="form-select" id="role" name="role" required>
                                                        <option value="user" selected>Regular User</option>
                                                        <option value="admin">Administrator</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary mx-2 mt-2">Create User</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users List -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Manage Users</div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="users-table" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Directorate</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Created Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($user['directorate_name'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $user['role'] == 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                                                <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo $user['is_active'] == 1 ? 'badge-success' : 'badge-danger'; ?>">
                                                                <?php echo $user['is_active'] == 1 ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                                        <td style="white-space: nowrap;">
                                                            <!-- Enable/Disable Toggle -->
                                                            <?php if ($user['id'] != $_SESSION['id']): // Don't allow disabling own account ?>
                                                                <form action="control/user_process-v1.php?action=toggle_status" method="POST" style="display: inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="current_status" value="<?php echo $user['is_active']; ?>">
                                                                    <?php if ($user['is_active'] == 1): ?>
                                                                        <button type="submit" class="btn btn-warning btn-sm" title="Disable User">
                                                                            <i class="fas fa-ban"></i> Disable
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="submit" class="btn btn-success btn-sm" title="Enable User">
                                                                            <i class="fas fa-check"></i> Enable
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </form>
                                                            <?php else: ?>
                                                                <button class="btn btn-secondary btn-sm" disabled title="Cannot modify own account">
                                                                    <i class="fas fa-user"></i> Current User
                                                                </button>
                                                            <?php endif; ?>

                                                            <!-- Delete User -->
                                                            <?php if ($user['id'] != $_SESSION['id']): ?>
                                                                <form action="control/user_process-v1.php?action=delete" method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete User">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'template/foot.php'; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                pageLength: 10,
                order: [[0, 'desc']], // Order by ID descending
                columnDefs: [
                    {
                        targets: [7], // Actions column
                        orderable: false
                    }
                ]
            });
        });

        function confirmDelete() {
            return confirm('Are you sure you want to delete this user? This action cannot be undone.');
        }

        // Show success message if redirected with success parameter
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'User operation completed successfully!',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error processing your request.',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>

    <style>
        .badge {
            font-size: 0.75em;
            padding: 4px 8px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 2px;
        }
    </style>
</body>
</html>