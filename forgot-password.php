<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "data_base_school";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mode = 'enter_email';
$message = '';
$message2 = '';

function send_mail($recipient, $subject, $message)
{
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
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->Host       = 'smtp.gmail.com';
        $mail->Username   = 'arellanokahtleenjoy.bsit@gmail.com';
        $mail->Password   = 'vnpnzvcftguchpmn';

        $mail->isHTML(true);
        $mail->addAddress($recipient, "Esteemed user");
        $mail->setFrom("arellanokahtleenjoy.bsit@gmail.com", "DEPED Computer Laboratory");
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Error while sending Email: {$mail->ErrorInfo}";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['check-email'])) {
        $email = $_POST['email'];
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email exists
            $reset_code = rand(100000, 999999);
            $_SESSION['reset_code'] = $reset_code;
            $_SESSION['reset_email'] = $email;

            $subject = "Password Reset Code";
            $message = "Your password reset code is: $reset_code";

            if (send_mail($email, $subject, $message)) {
                $mode = 'enter_code';
                $message2 = 'A reset code has been sent to your email.';
            } else {
                $message = 'Failed to send reset code. Please try again.';
            }
        } else {
            $message = 'Email not found';
        }
    } elseif (isset($_POST['check_code'])) {
        $code = $_POST['code'];
        if ($code == $_SESSION['reset_code']) {
            $mode = 'enter_password';
        } else {
            $message = 'Invalid reset code';
        }
    } elseif (isset($_POST['SavePassChanges'])) {
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $email = $_SESSION['reset_email'];

        if ($password === $cpassword) {
            // Update the password in the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($stmt === false) {
                die("Error preparing the statement: " . $conn->error);
            }
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            $message2 = 'Password has been reset successfully.';
            header("location: login.php?message=$message2");
            exit;
        } else {
            $message = 'Passwords do not match';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-box {
            width: 50%;
            margin: 50px auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            background-color: white;
        }

        .header-content {
            text-align: center;
        }

        #error {
            color: #8B0000;
        }

        .school-division {
            text-align: center;
        }

        .school-division h2 {
            color: green;
            font-size: 36px;
            margin: 0;
        }

        .navbar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
        }

        .navbar-brand img {
            height: 75px;
            width: auto;
        }

        button i {
            margin-right: 5px;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <script>
        function updateTime() {
            const options = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const timeString = formatter.format(new Date());
            document.getElementById('pst-time').textContent = timeString;
        }

        function updateDate() {
            const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric' };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const dateString = formatter.format(new Date());
            document.getElementById('pst-date').textContent = dateString;
        }

        function updateDateTime() {
            updateTime();
            updateDate();
        }

        setInterval(updateDateTime, 1000);
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo6.png" alt="School Logo">
    </a>
    <div class="school-division">
        <h2>
            SCHOOL DIVISION
            <br>
            OFFICE OF CALOOCAN
        </h2>
    </div>
    <div class="ml-auto text-right">
        <div id="pst-container">
            <div>Philippine Standard Time:</div>
            <div id="pst-time"></div>
            <div id="pst-date"></div>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="register-box">
                <?php if ($mode === 'enter_email'): ?>
                    <h2 class="text-center">Forgot Password</h2>
                    <form action="forgot-password.php" method="POST">
                        <div class="form-group">
                            <label for="email">Enter your email address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <button type="submit" name="check-email" class="btn btn-primary btn-block btn-icon">
                            <i class="fas fa-paper-plane"></i> Reset
                        </button>
                    </form>
                    <small id="emailHelp" class="form-text text-center text-danger"><?php echo $message; ?></small>
                    <div class="form-group text-center">
                        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to login</a>
                    </div>
                <?php elseif ($mode === 'enter_code'): ?>
                    <h2 class="text-center">Code Verification</h2>
                    <p class="text-center">Enter the code that was sent to your email. <strong>Code expires in 10 minutes!</strong></p>
                    <form action="forgot-password.php?mode=enter_code" method="POST">
                        <div class="form-group">
                            <input type="text" name="code" class="form-control" placeholder="Enter Code" required>
                        </div>
                        <button type="submit" name="check_code" class="btn btn-primary btn-block btn-icon">
                            <i class="fas fa-check"></i> Verify Code
                        </button>
                    </form>
                    <small id="emailHelp" class="form-text text-center text-danger"><?php echo $message; ?></small>
                    <div class="form-group text-center">
                        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to login</a>
                    </div>
                <?php elseif ($mode === 'enter_password'): ?>
                    <h2 class="text-center">Reset Password</h2>
                    <form action="forgot-password.php?mode=enter_password" method="POST">
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="cpassword" class="form-control" placeholder="Confirm New Password" required>
                        </div>
                        <button type="submit" name="SavePassChanges" class="btn btn-primary btn-block btn-icon">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                    <small id="emailHelp" class="form-text text-center text-danger"><?php echo $message; ?></small>
                    <div class="form-group text-center">
                        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
