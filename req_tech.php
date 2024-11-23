<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'data_base_school');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$action = isset($_GET['action']) ? $_GET['action'] : ''; // Assuming the action is passed via GET

// Check for approval action
if ($action === 'approve' && isset($_GET['id'])) {
    $currentId = $_GET['id']; // Assuming the ID is passed via GET for approval

    // Insert the approved request into the history table
    $insertHistory = $conn->prepare("INSERT INTO history_of_requests (school_id, school_name, subject, status, description, approved_date)
                                    SELECT school_id, school_name, subject, 'Approved', description, NOW() FROM tickets WHERE id = ?");
    $insertHistory->bind_param("i", $currentId);
    $insertHistory->execute();

    // Now update the status of the request in the original table
    $updateStatus = $conn->prepare("UPDATE technician_requests SET status = 'Approved' WHERE id = ?");
    $updateStatus->bind_param("i", $currentId);
    $updateStatus->execute();
  }

// Check if the request is for approving or canceling a technician request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    // Start transaction
    $conn->begin_transaction();

    // Check current status of the technician request
    if ($stmtCheckStatus = $conn->prepare("SELECT status FROM tickets WHERE id = ?")) {
        $stmtCheckStatus->bind_param("i", $id);
        $stmtCheckStatus->execute();
        $stmtCheckStatus->bind_result($current_status);
        $stmtCheckStatus->fetch();
        $stmtCheckStatus->close();

        if ($action === 'approve' && ($current_status === 'Pending' || $current_status === 'NEW')) {
      // Approve the request
      if ($stmt = $conn->prepare("UPDATE tickets SET status = 'Approved' WHERE id = ?")) {
          $stmt->bind_param("i", $id);

          if ($stmt->execute()) {
              // Fetch school ID to get admin email
              if ($stmtSchoolId = $conn->prepare("SELECT school_id FROM tickets WHERE id = ?")) {
                  $stmtSchoolId->bind_param("i", $id);
                  $stmtSchoolId->execute();
                  $stmtSchoolId->bind_result($school_id);
                  $stmtSchoolId->fetch();
                  $stmtSchoolId->close();

                  // Fetch the admin email for the specific school
                  if ($stmtEmail = $conn->prepare("SELECT email FROM users WHERE school_id = ?")) {
                      $stmtEmail->bind_param("i", $school_id);
                      $stmtEmail->execute();
                      $stmtEmail->bind_result($admin_email);
                      $stmtEmail->fetch();
                      $stmtEmail->close();

                            // Send notification email to the admin using PHPMailer
                            $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();
                                $mail->SMTPOptions = array(
                                    'ssl' => array(
                                        'verify_peer' => false,
                                        'verify_peer_name' => false,
                                        'allow_self_signed' => true
                                    )
                                );
                                // Server settings
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'arellanokahtleenjoy.bsit@gmail.com'; // Your email
                                $mail->Password = 'vnpnzvcftguchpmn'; // Use app password if 2FA is enabled
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;

                                // Recipients
                                $mail->setFrom('arellanokahtleenjoy.bsit@gmail.com', 'School Admin');
                                $mail->addAddress($admin_email);

                                // Content
                                $mail->isHTML(true);
                                $mail->Body = "Your request with ID {$id} has been approved.";
                                $mail->Subject = "Technician Request and Invoice Approval";
                                $mail->Body = "
                                              <html>
                                              <head>
                                                  <style>
                                                      body {
                                                          font-family: Arial, sans-serif;
                                                          margin: 0;
                                                          padding: 0;
                                                          background-color: #f8f8f8;
                                                      }
                                                      .container {
                                                          max-width: 600px;
                                                          margin: 0 auto;
                                                          background: #ffffff;
                                                          padding: 20px;
                                                          border-radius: 8px;
                                                          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                                      }
                                                      h2 {
                                                          color: #4CAF50;
                                                          text-align: center;
                                                      }
                                                      p {
                                                          font-size: 16px;
                                                          color: #333;
                                                      }
                                                      .details {
                                                          background-color: #f9f9f9;
                                                          padding: 15px;
                                                          border-radius: 6px;
                                                          margin-top: 20px;
                                                          box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                                                      }
                                                      .details table {
                                                          width: 100%;
                                                      }
                                                      .details th, .details td {
                                                          padding: 10px;
                                                          text-align: left;
                                                          border-bottom: 1px solid #dddddd;
                                                      }
                                                      .details th {
                                                          background-color: #4CAF50;
                                                          color: white;
                                                      }
                                                      .footer {
                                                          text-align: center;
                                                          margin-top: 30px;
                                                          color: #777777;
                                                      }
                                                  </style>
                                              </head>
                                              <body>
                                                  <div class='container'>
                                                      <h2>Technician Request Approved</h2>
                                                      <p>Dear Admin,</p>
                                                      <p>Your request with ID <strong>{$id}</strong> has been approved. The invoice related to the request is also approved.</p>
                                                      <div class='details'>
                                                          <table>
                                                              <thead>
                                                                  <tr>
                                                                      <th>Detail</th>
                                                                      <th>Information</th>
                                                                  </tr>
                                                              </thead>
                                                              <tbody>
                                                                  <tr>
                                                                      <td><strong>Request ID:</strong></td>
                                                                      <td>{$id}</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>School Name:</strong></td>
                                                                      <td>{$school_name}</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>Created At:</strong></td>
                                                                      <td>{$created_at}</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>Description:</strong></td>
                                                                      <td>{$description}</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>Subject:</strong></td>
                                                                      <td>{$subject}</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>Approval Status:</strong></td>
                                                                      <td>Approved</td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td><strong>Approval Date:</strong></td>
                                                                      <td>" . date('Y-m-d H:i:s') . "</td>
                                                                  </tr>
                                                              </tbody>
                                                          </table>
                                                      </div>
                                                      <div class='footer'>
                                                          <p>Thank you,<br>The Main Admin Team</p>
                                                      </div>
                                                  </div>
                                              </body>
                                              </html>";

                                $mail->send();
                                // Commit transaction
                                $conn->commit();
                                echo "Request approved successfully and notification sent to the admin.";
                            } catch (Exception $e) {
                                // Rollback if email fails
                                $conn->rollback();
                                echo "Request approved but failed to send notification email. Mailer Error: {$mail->ErrorInfo}";
                            }
                        }
                    }
                }
                $stmt->close();
            }
        } elseif ($action === 'cancel' && $current_status === 'Pending') {
            // Cancel the request
            if ($stmt = $conn->prepare("UPDATE tickets SET status = 'Canceled' WHERE id = ?")) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    // Commit transaction
                    $conn->commit();
                    echo "Request canceled successfully.";
                } else {
                    echo "Error canceling technician request: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            echo "This request cannot be processed further. Current status: {$current_status}.";
            $conn->rollback();
        }
    } else {
        echo "Error checking status: " . $conn->error;
    }
}

$result = $conn->query("
    SELECT t.id, t.school_id, t.subject, t.status, t.description, t.school_name
    FROM tickets t
    JOIN users u ON t.school_id = u.school_id
    ORDER BY t.created_at DESC
");

if (!$result) {
    die("Query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Additional styling for the header */
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: #4CAF50; /* Updated color for better visibility */
            text-align: center;
        }
        #pst-container {
            text-align: right;
            color: #555; /* Subtle color for time display */
        }
        .table th, .table td {
            vertical-align: middle; /* Aligns items vertically centered */
        }
        .icon-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: inherit; /* Inherit color for better UI */
            transition: color 0.3s; /* Smooth transition for hover effect */
        }
        .icon-button:hover {
            color: #4CAF50; /* Change color on hover */
        }
        .table-header {
            background-color: #343a40; /* Dark header for better contrast */
            color: white; /* White text for header */
        }
        .container {
            border: 1px solid #e0e0e0; /* Soft border around container */
            border-radius: 8px; /* Rounded corners */
            padding: 20px; /* Padding around the container */
            background-color: #fff; /* White background for clarity */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
        }
        .btn-secondary {
            background-color: #007bff; /* Primary blue color for buttons */
            border-color: #007bff; /* Border matches button color */
        }
        .btn-secondary:hover {
            background-color: #0056b3; /* Darker blue on hover */
            border-color: #0056b3; /* Darker border on hover */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
        <a class="navbar-brand" href="#">
            <img src="logo6.png" alt="School Logo" style="height: 75px;">
        </a>
        <div class="school-division mx-auto">
            <h2>
                SCHOOL DIVISION
                <br>
                OFFICE OF CALOOCAN
            </h2>
        </div>
        <div id="pst-container" class="ml-auto text-right">
            <div>Philippine Standard Time:</div>
            <div id="pst-time"></div>
            <div id="pst-date"></div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button class="icon-button" title="Back" onclick="window.location.href='mainAdmin_dashboard.php';">
                    <i class="fas fa-arrow-left fa-lg"></i>
                </button>
            </div>
            <h1 class="font-weight-bold">Technician Requests</h1>
            <button class="btn btn-primary" onclick="window.print();">Generate Report</button>
            <button class="btn btn-secondary" onclick="window.location.href='history_request.php';">History of Request</button>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="table-header">
                <tr>
                    <th>School ID</th>
                    <th>School Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
              <?php
                while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                            <td>{$row['school_id']}</td>
                            <td>{$row['school_name']}</td>
                            <td>{$row['subject']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['description']}</td>
                            <td>
                                <button class='icon-button' title='Approve' onclick=\"showConfirmModal('approve', '{$row['id']}');\">
                                    <i class='fas fa-check-circle fa-lg'></i>
                                </button>
                                <button class='icon-button' title='Cancel' onclick=\"showConfirmModal('cancel', '{$row['id']}');\">
                                    <i class='fas fa-times-circle fa-lg'></i>
                                </button>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Approval Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Are you sure you want to approve this request?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Yes, proceed</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Time and date update function
        $(document).ready(function() {
            function updateTimeAndDate() {
                const optionsTime = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
                const optionsDate = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };

                const formatterTime = new Intl.DateTimeFormat('en-US', optionsTime);
                const formatterDate = new Intl.DateTimeFormat('en-US', optionsDate);

                $('#pst-time').text(formatterTime.format(new Date()));
                $('#pst-date').text(formatterDate.format(new Date()));
            }

            updateTimeAndDate();
            setInterval(updateTimeAndDate, 1000);
        });

    let currentAction;
    let currentId;

    function showConfirmModal(action, id) {
        currentAction = action;
        currentId = id;

        if (action === 'approve') {
            document.getElementById('confirmMessage').innerText = 'Are you sure you want to approve this request?';
        } else {
            document.getElementById('confirmMessage').innerText = 'Are you sure you want to decline this request?';
        }

        $('#confirmModal').modal('show');
    }

    document.getElementById('confirmButton').addEventListener('click', function() {
        // Create a form dynamically and submit it
        let form = document.createElement('form');
        form.action = 'req_tech.php';
        form.method = 'POST';

        let inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = currentId;

        let inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = currentAction;

        form.appendChild(inputId);
        form.appendChild(inputAction);
        document.body.appendChild(form);
        form.submit();
    });

    </script>
</body>
</html>

<?php
$conn->close();
?>
