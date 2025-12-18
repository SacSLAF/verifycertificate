<?php
session_start(); // Start the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
var_dump($_SESSION);
// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: index.php");
    exit;
}

include "../config.php";
include "control/helper.php";
// $stmt = $conn->prepare("SELECT COUNT(*) FROM certificates WHERE directorate_id = ?");
// $stmt->bind_param("i", $_SESSION['directorate']); 
// $stmt->execute();
// // $result = $stmt->get_result();
// $stmt->bind_result($count);
// $stmt->fetch();
// $stmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<?php include "template/head.php"; ?>

<body>
    <div class="wrapper">
        <?php
        include 'template/sidebar.php';
        ?>

        <div class="main-panel">
            <?php
            include 'template/main-header.php';
            //include 'template/main-content.php';
            ?>

            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Create user</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <form action="control/user_process.php?action=add" method="POST" id="createUserForm">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="username">Username</label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="email2" name="username"
                                                                placeholder="Enter Username" />
                                                            <!-- <small id="emailHelp2" class="form-text text-muted">We'll never share your email with anyone
                                                        else.</small> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Password</label>
                                                            <input
                                                                type="password"
                                                                class="form-control" name="password"
                                                                id="password"
                                                                placeholder="Password" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="userType">Select the user type</label>
                                                            <select
                                                                class="form-select"
                                                                id="type" name="type" required>
                                                                <option value="" selected disabled>Select a user type</option>
                                                                <option value="1">Training Establishments under DT - user</option>
                                                                <option value="2">Other Establishments - user</option>
                                                                <option value="3">Leaving service certificate - user</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="userRole">Select the user role</label>
                                                            <select
                                                                class="form-select"
                                                                id="role" name="role" required>
                                                                <option value="" selected disabled>Select a user role</option>
                                                                <option value="admin">Admin</option>
                                                                <option value="user">User</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="directorateRow">
                                                    <div class="col-md-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="directorate">Select Directorate <span class="text-danger">*</span></label>
                                                            <select class="form-control" id="directorate" name="directorate">
                                                                <option value="" selected disabled>Select a directorate</option>
                                                                <?= getDirectorateOptions($conn); ?>
                                                            </select>
                                                            <small class="form-text text-muted">Required for Leaving Service Certificate users</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="campInstituteRow" style="display: none;">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="selectCamp">Select Camp</label>
                                                            <select
                                                                class="form-select"
                                                                id="selectCamp" name="camp" required>
                                                                <option value="" selected disabled>Select the camp</option>
                                                                <?= getCampsOptions($conn); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="campInstituteRow">Select the training institute</label>
                                                            <select class="form-select" id="trainingInstitute" name="institute" required disabled>

                                                            </select>
                                                            <small class="form-text text-muted" id="instituteHelp">Please select a camp first</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary mx-2 mt-2">Create</button>
                                            </form>
                                            <div class="col-md-12 col-lg-4">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php
            include 'template/foot.php';
            ?>
        </div>
    </div>





    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const userTypeSelect = document.getElementById('type');
            const campInstituteRow = document.getElementById('campInstituteRow');
            const campSelect = document.getElementById('selectCamp');
            const instituteSelect = document.getElementById('trainingInstitute');
            const directorateRow = document.getElementById('directorateRow');
            const directorateSelect = document.getElementById('directorate');
            const instituteHelp = document.getElementById('instituteHelp');
            const createUserForm = document.getElementById('createUserForm');

            // Function to show/hide fields based on user type
            function toggleFieldsByUserType(userType) {
                if (userType === '3') {
                    // Show directorate, hide camp & institute
                    directorateRow.style.display = 'flex';
                    campInstituteRow.style.display = 'none';

                    // Update required attributes
                    directorateSelect.required = true;
                    campSelect.required = false;
                    instituteSelect.required = false;

                    // Clear camp and institute
                    campSelect.value = '';
                    instituteSelect.innerHTML = '<option value="" selected disabled>Select camp first</option>';
                    instituteSelect.disabled = true;

                } else if (userType === '1' || userType === '2') {
                    directorateRow.style.display = 'none';
                    campInstituteRow.style.display = 'flex';

                    // Update required attributes
                    directorateSelect.required = false;
                    campSelect.required = true;
                    instituteSelect.required = true;

                    // Clear directorate
                    directorateSelect.value = '';

                    // Enable institute if camp is selected
                    if (campSelect.value) {
                        instituteSelect.disabled = false;
                        instituteHelp.textContent = 'Select a training institute';
                    } else {
                        instituteSelect.disabled = true;
                        instituteHelp.textContent = 'Please select a camp first';
                    }
                } else {
                    // Hide both sections if no valid user type
                    directorateRow.style.display = 'none';
                    campInstituteRow.style.display = 'none';

                    // Clear all selections
                    directorateSelect.value = '';
                    campSelect.value = '';
                    instituteSelect.innerHTML = '<option value="" selected disabled>Select camp first</option>';
                    instituteSelect.disabled = true;

                    // Update required attributes
                    directorateSelect.required = false;
                    campSelect.required = false;
                    instituteSelect.required = false;
                }
            }

            // Initialize on page load
            toggleFieldsByUserType(userTypeSelect.value);

            // User type change event
            userTypeSelect.addEventListener('change', function() {
                toggleFieldsByUserType(this.value);
            });

            // Camp change event
            campSelect.addEventListener('change', function() {
                const campId = this.value;

                if (!campId) {
                    instituteSelect.innerHTML = '<option value="" selected disabled>Select camp first</option>';
                    instituteSelect.disabled = true;
                    instituteHelp.textContent = 'Please select a camp first';
                    return;
                }

                // Show loading state
                instituteSelect.disabled = true;
                instituteSelect.innerHTML = '<option value="" disabled>Loading institutes...</option>';
                instituteHelp.textContent = 'Loading institutes...';

                // Fetch institutes
                fetch('control/get_institutes_by_camp.php?camp_id=' + encodeURIComponent(campId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        instituteSelect.innerHTML = '';

                        if (!data || data.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No institutes found';
                            option.disabled = true;
                            instituteSelect.appendChild(option);
                            instituteHelp.textContent = 'No institutes found for this camp';
                        } else {
                            // Add default option
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.textContent = 'Select an institute';
                            defaultOption.disabled = true;
                            defaultOption.selected = true;
                            instituteSelect.appendChild(defaultOption);

                            // Add institute options
                            data.forEach(institute => {
                                const option = document.createElement('option');
                                option.value = institute.id;
                                option.textContent = institute.name;
                                instituteSelect.appendChild(option);
                            });

                            instituteHelp.textContent = 'Select a training institute';
                        }

                        instituteSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        instituteSelect.innerHTML = '<option value="" disabled>Error loading institutes</option>';
                        instituteHelp.textContent = 'Error loading institutes. Please try again.';
                        instituteSelect.disabled = true;
                    });
            });

            // Form validation
            createUserForm.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Get values
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                const userType = userTypeSelect.value;
                const userRole = document.getElementById('role').value;

                // Basic validation
                if (!username) {
                    isValid = false;
                    errorMessage = 'Username is required';
                } else if (!password) {
                    isValid = false;
                    errorMessage = 'Password is required';
                } else if (password.length < 6) {
                    isValid = false;
                    errorMessage = 'Password must be at least 6 characters';
                } else if (!userType) {
                    isValid = false;
                    errorMessage = 'User type is required';
                } else if (!userRole) {
                    isValid = false;
                    errorMessage = 'User role is required';
                }

                // Conditional validation
                if (isValid && userType === '3') {
                    if (!directorateSelect.value) {
                        isValid = false;
                        errorMessage = 'Directorate is required for Leaving Service Certificate users';
                    }
                } else if (isValid) {
                    if (!campSelect.value) {
                        isValid = false;
                        errorMessage = 'Camp is required';
                    } else if (!instituteSelect.value) {
                        isValid = false;
                        errorMessage = 'Training institute is required';
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Error: ' + errorMessage);
                }
            });

            // Form reset handler
            createUserForm.addEventListener('reset', function() {
                setTimeout(function() {
                    toggleFieldsByUserType(userTypeSelect.value);
                }, 10);
            });

            // If camp is already selected on page load, load institutes
            if (campSelect.value) {
                campSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>


</body>

</html>