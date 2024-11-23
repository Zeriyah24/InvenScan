<?php
session_start();

require 'phpmailer-master/src/Exception.php';
require 'phpmailer-master/src/PHPMailer.php';
require 'phpmailer-master/src/SMTP.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$school_filter = ''; // Initialize the variable to avoid undefined warning
$status_filter = ''; // You can do the same for other filters if needed

// Fetch available schools
$school_sql = "SELECT school_id, school_name FROM schools";
$school_result = $conn->query($school_sql);
$schools = [];
if ($school_result->num_rows > 0) {
    while ($row = $school_result->fetch_assoc()) {
        $schools[] = $row;
    }
}

// Fetch available user emails
$emails_sql = "SELECT email FROM users";
$email_result = $conn->query($emails_sql);
$emails = [];
if ($email_result->num_rows > 0) {
    while ($row = $email_result->fetch_assoc()) {
        $emails[] = $row['email'];
    }
}

// Create a ticket and send notification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_email'])) {
    $school_id = $_POST['school_id'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $request_email = $_POST['request_email'];
    $creator_name = $_SESSION['name'];
    $creator_email = $_SESSION['email'];

    $sql = "INSERT INTO tickets (school_id, subject, description, request_email, creator_name, creator_email, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'NEW', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $school_id, $subject, $description, $request_email, $creator_name, $creator_email);
    $stmt->execute();

    // Set up email notification
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'arellanokahtleenjoy.bsit@gmail.com'; // Change to your email
        $mail->Password = 'vnpnzvcftguchpmn'; // Change to your password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipient and sender
        $mail->setFrom('arellanokahtleenjoy.bsit@gmail.com', 'School Ticketing System');
        $mail->addAddress($request_email);

        // Email subject and body
        $mail->Subject = "New Ticket Created: $subject";
        $mail->Body = "A new ticket has been created.\n\n"
                    . "School: $school_id\n"
                    . "Subject: $subject\n"
                    . "Description: $description\n"
                    . "Requested by: $creator_name\n"
                    . "Contact: $creator_email";

        $mail->send();
        $message = "Ticket created successfully, and notification sent.";
    } catch (Exception $e) {
        $message = "Ticket created, but notification could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }
}

// Fetch tickets filtered by specific school
// Fetch tickets filtered by specific school
if (isset($_SESSION['school_id'])) {
    $school_id = $_SESSION['school_id'];  // Ensure this session variable is set upon login
} else {
    // Handle the case when school_id is not set
    $message = "School ID is not set. Please log in again.";
    // Optionally, redirect to login or another appropriate page
    header("Location: school_admin.php"); // or another page to handle this case
    exit();
}

// Initialize the sorting variables
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'asc'; // Default to ascending if not set

// Handle school filter if it's set
if (isset($_POST['school_filter'])) {
    $school_filter = $_POST['school_filter'];
}

// Fetch tickets filtered by specific school and sort them
$sql = "SELECT t.*, s.school_name FROM tickets t JOIN schools s ON t.school_id = s.school_id WHERE t.school_id = ?";
if ($school_filter !== '') {
    $sql .= " AND t.school_id = ?"; // Add filtering by school_id if specified
}
if ($sort_order === 'asc') {
    $sql .= " ORDER BY t.created_at ASC"; // Change to ascending order
} else {
    $sql .= " ORDER BY t.created_at DESC"; // Change to descending order
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $school_id); // Bind school_id
$stmt->execute();
$result = $stmt->get_result();
$tickets = [];

// Fetch tickets data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        h1 {
            color: #007bff;
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 20px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .modal-title {
            margin: 0;
        }
        .form-control, .btn {
            border-radius: 0;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        table {
            table-layout: fixed;
            width: 75%;
            margin-top: 20px;
        }
        th, td {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            max-width: 100px;
        }
        td.subject {
            font-weight: bold;
        }
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
        .subject {
            font-weight: bold;
        }
        .alert {
            margin-bottom: 20px;
        }
        .filter-container {
            margin-bottom: 10px;
            background-color: white;
            padding: 8px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .icon-button {
            display: inline-flex;
            align-items: center;
            margin-right: 5px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="container">
        <h1>Ticketing System</h1>
        <!-- Back Button Section -->
        <div class="text-left mb-3">
            <button class="btn btn-secondary" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </div>

        <div class="filter-container">
          <form method="POST" class="form-inline">
              <div class="row align-items-center">
                  <div class="col-auto form-group">
                      <label for="status_filter" class="mr-2">Status:</label>
                      <select name="status_filter" class="form-control">
                          <option value="">All</option>
                          <option value="NEW" <?php if ($status_filter == 'NEW') echo 'selected'; ?>>NEW</option>
                          <option value="WORK IN_PROGRESS" <?php if ($status_filter == 'WORK IN_PROGRESS') echo 'selected'; ?>>WORK IN PROGRESS</option>
                          <option value="RESOLVED" <?php if ($status_filter == 'RESOLVED') echo 'selected'; ?>>RESOLVED</option>
                          <option value="RE-OPENED" <?php if ($status_filter == 'RE-OPENED') echo 'selected'; ?>>PENDING</option>
                          <option value="CLOSED" <?php if ($status_filter == 'CLOSED') echo 'selected'; ?>>CLOSED</option>
                      </select>
                  </div>
                  <div class="col-auto form-group">
                      <label for="school_filter" class="mr-2">School:</label>
                      <select name="school_filter" class="form-control">
                          <option value="">All Schools</option>
                          <?php foreach ($schools as $school): ?>
                              <option value="<?php echo $school['school_id']; ?>" <?php if ($school_filter == $school['school_id']) echo 'selected'; ?>>
                                  <?php echo $school['school_name']; ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="col-auto form-group">
                      <label for="sort_order" class="mr-2">Sort By:</label>
                      <select name="sort_order" class="form-control">
                          <option value="asc" <?php if ($sort_order == 'asc') echo 'selected'; ?>>Date Created Asc</option>
                          <option value="desc" <?php if ($sort_order == 'desc') echo 'selected'; ?>>Date Created Desc</option>
                      </select>
                      <button type="submit" class="btn btn-primary icon-button ml-2">
                          <i class="fas fa-filter"></i> Filter
                      </button>
                  </div>
              </div>
          </form>
      </div>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>School</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['school_name']; ?></td>
                            <td class="subject"><?php echo $ticket['subject']; ?></td>
                            <td><?php echo $ticket['description']; ?></td>
                            <td><?php echo $ticket['status']; ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($ticket['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No tickets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-right mt-4">
    <form method="POST">
        <button type="button" class="btn btn-primary icon-button" data-toggle="modal" data-target="#createTicketModal">
            <i class="fas fa-plus-circle"></i> Create Ticket
        </button>
    </form>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1" role="dialog" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTicketModalLabel">Create Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="school_id">School:</label>
                        <select name="school_id" class="form-control" required>
                            <option value="">Select School</option>
                            <?php foreach ($schools as $school): ?>
                                <option value="<?php echo $school['school_id']; ?>"><?php echo $school['school_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="request_email">Request Email:</label>
                        <input type="email" name="request_email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="laptop_brand">Laptop Brand:</label>
                        <select name="laptop_brand" class="form-control" required>
                            <option value="">Select Brand</option>
                            <option value="Dell">Dell</option>
                            <option value="HP">HP</option>
                            <option value="Lenovo">Lenovo</option>
                            <option value="Asus">Asus</option>
                            <option value="Acer">Acer</option>
                            <!-- Add other brands as needed -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Add Attachment:</label>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"> <!-- File input for attachment -->
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> <!-- Font Awesome close icon -->
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> <!-- Font Awesome check icon -->
                            Create Ticket
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
       function goBack() {
        window.history.back();
    }
    </script>

</body>
</html>
