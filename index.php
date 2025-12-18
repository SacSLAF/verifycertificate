<?php
$msg = isset($_GET['msg']) && $_GET['msg'] != null ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter ID for Certificate</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <?php if ($msg == 'success') { ?>
            <div class="alert alert-success text-center" role="alert">Certificate was created successfully!</div>
        <?php
        } else if ($msg == 'failed') { ?>
            <div class="alert alert-danger text-center" role="alert">Certificate creation was failed!</div>
        <?php
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <b>E-Certificates for Directorate Of Logistics</b>
                    </div>
                    <div class="card-body">
                        <form action="certificate.php" method="GET">
                            <div class="form-group">
                                <label for="id">Certificate ID:</label>
                                <input type="text" class="form-control" id="uuid" name="certificate_id" placeholder="Enter UUID" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Show E-Certificate</button>
                            <p>404670a3-c5c5-4198-ba7f-c531f28ab106</p>
                        </form>
                    </div>
                </div>
                <!-- <div class="mt-2">
                    <a href="insert_certificate.php"><button class="btn btn-info">Add a new certificate</button></a>
                </div> -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>