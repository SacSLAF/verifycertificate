<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Certificate Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Insert Certificate Details</h2>
        <form action="process_insert.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="certificate_id">Certificate ID:</label>
                <input type="text" class="form-control" id="certificate_id" name="certificate_id" required>
            </div>
            <div class="form-group">
                <label for="date_of_issue">Date of Issue:</label>
                <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" required>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="service_no">Service No:</label>
                <input type="text" class="form-control" id="service_no" name="service_no">
            </div>
            <div class="form-group">
                <label for="rank">Rank:</label>
                <input type="text" class="form-control" id="rank" name="rank">
            </div>
            <div class="form-group">
                <label for="passport_no">Passport No:</label>
                <input type="text" class="form-control" id="passport_no" name="passport_no">
            </div>
            <div class="form-group">
                <label for="nic_no">NIC No:</label>
                <input type="text" class="form-control" id="nic_no" name="nic_no">
            </div>
            <div class="form-group">
                <label for="date_of_enlistment">Date of Enlistment:</label>
                <input type="date" class="form-control" id="date_of_enlistment" name="date_of_enlistment">
            </div>
            <div class="form-group">
                <label for="date_of_retirement">Date of Retirement:</label>
                <input type="date" class="form-control" id="date_of_retirement" name="date_of_retirement">
            </div>
            <div class="form-group">
                <label for="total_service">Total Service:</label>
                <input type="text" class="form-control" id="total_service" name="total_service">
            </div>
            <!-- <div class="form-group">
                <label for="experience">Experience:</label>
                <textarea class="form-control" id="experience" name="experience"></textarea>
            </div>
            <div class="form-group">
                <label for="knowledge">Knowledge:</label>
                <textarea class="form-control" id="knowledge" name="knowledge"></textarea>
            </div>
            <div class="form-group">
                <label for="skills">Skills:</label>
                <textarea class="form-control" id="skills" name="skills"></textarea>
            </div> -->
            <div class="form-group">
                <label for="experience">Experience, Knowledge & Skills:</label>
                <textarea class="form-control" id="experience" name="experience" rows="5"></textarea>
                <small class="form-text text-muted">Enter each experience item separated by commas.</small>
            </div>

            <div class="form-group">
                <label for="knowledge">qualifications,Awards & Achievements:</label>
                <textarea class="form-control" id="qualifications" name="qualifications" rows="5"></textarea>
                <small class="form-text text-muted">Enter each knowledge item separated by commas.</small>
            </div>

            <!-- <div class="form-group">
                <label for="skills">Skills:</label>
                <textarea class="form-control" id="skills" name="skills" rows="5"></textarea>
                <small class="form-text text-muted">Enter each skill item separated by commas.</small>
            </div> -->

            <div class="form-group">
                <label for="issuing_authority_name">Issuing Authority Name:</label>
                <input type="text" class="form-control" id="issuing_authority_name" name="issuing_authority_name" required>
            </div>
            <div class="form-group">
                <label for="issuing_authority_rank">Issuing Authority Rank:</label>
                <input type="text" class="form-control" id="issuing_authority_rank" name="issuing_authority_rank">
            </div>
            <div class="form-group">
                <label for="issuing_authority_appointment">Issuing Authority Appointment:</label>
                <input type="text" class="form-control" id="issuing_authority_appointment" name="issuing_authority_appointment">
            </div>
            <div class="form-group">
                <label for="issuing_authority_email">Issuing Authority Email:</label>
                <input type="email" class="form-control" id="issuing_authority_email" name="issuing_authority_email">
            </div>
            <div class="form-group">
                <label for="issuing_authority_contact">Issuing Authority Contact:</label>
                <input type="text" class="form-control" id="issuing_authority_contact" name="issuing_authority_contact">
            </div>
            <div class="form-group">
                <label for="verified_by">Verified By:</label>
                <input type="text" class="form-control" id="verified_by" name="verified_by" required>
            </div>
            <div class="form-group">
                <label for="verified_date">Verified Date:</label>
                <input type="date" class="form-control" id="verified_date" name="verified_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>