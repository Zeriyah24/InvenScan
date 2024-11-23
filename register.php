<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $role = $_POST['role'];
    $school_id = null;

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("location: register.php?error=Passwords do not match");
        exit;
    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Determine school selection logic
    if ($role === 'Admin') {
        if (!isset($_POST['school']) || empty($_POST['school'])) {
            header("location: register.php?error=Please select a school");
            exit;
        }
        $school_id = $_POST['school'];
    } elseif ($role === 'Main') {
        $school_id = 90; // Set school_id to 90 for Main Admin
    } elseif ($role === 'Supplier') {
        $school_id = 91; // Set school_id to 91 for Supplier
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($check_email === false) {
        die("Prepare failed: " . $conn->error);
    }
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        // Email already exists
        header("location: register.php?error=Email already exists");
        exit;
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, school_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssssi", $name, $email, $password_hashed, $role, $school_id);

        if ($stmt->execute()) {
            // Auto login user
            $last_id = $stmt->insert_id;
            $_SESSION['user_id'] = $last_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_school_id'] = $school_id;

            // Redirect to login page after successful registration
            header("location: register.php");
            exit;
        } else {
            // Redirect back with error message
            header("location: register.php?error=Error in registration");
            exit;
        }
    }

    $check_email->close();
    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .input-group-text {
            cursor: pointer;
        }
        .input-group-text i {
            color: #6c757d;
        }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            padding: 10px 12px;
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
        }
        .btn-submit {
            background-color: #28a745;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #007bff;
        }
        .btn-back:hover {
            background-color: #0069d9;
        }
        .header-content {
            text-align: center;
        }
        .school-division h2 {
            color: green;
            font-size: 36px;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        #school-selection {
            display: none;
        }
    </style>
    <script>
        function toggleSchoolSelection() {
            var role = document.getElementById("role").value;
            var schoolSelection = document.getElementById("school-selection");

            if (role === "Admin") {
                schoolSelection.style.display = "block";
            } else {
                schoolSelection.style.display = "none";
            }
        }

        function togglePasswordVisibility(id) {
            var passwordInput = document.getElementById(id);
            var icon = passwordInput.nextElementSibling.querySelector('.fa');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
      <div class="row justify-content-center">
          <div class="col-md-6">
              <div class="login-container">
                  <div class="d-flex align-items-center mb-4">
                      <a href="mainAdmin_dashboard.php" class="btn-icon btn-back me-3"> <!-- Increased margin here -->
                          <i class="fas fa-arrow-left"></i>
                      </a>
                      <h2 class="m-0">Account Creation Form</h2>
                  </div>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('confirm-password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="school-selection">
                              <label for="school"><i class="fas fa-school"></i> Select School:</label>
                              <select class="form-control" id="school" name="school">
                                  <option value="">- Select School -</option>
                                  <option value="1">A. Mabini Elementary School</option>
                                  <option value="2">Amparo Elementary School</option>
                                  <option value="3">Amparo High School</option>
                                  <option value="4">Andres Bonifacio Elementary School</option>
                                  <option value="5">Antonio Luna Elementary School</option>
                                  <option value="6">Antonio Luna High School</option>
                                  <option value="7">Antonio Uy Tan Senior High School</option>
                                  <option value="8">Baesa Elementary School</option>
                                  <option value="9">Baesa High School</option>
                                  <option value="10">Bagbaguin Elementary School</option>
                                  <option value="11">Bagong Barrio Elementary School</option>
                                  <option value="12">Bagong Barrio National High School</option>
                                  <option value="13">Bagong Barrio Senior High School</option>
                                  <option value="14">Bagong Silang Elementary School</option>
                                  <option value="15">Bagong Silang Elementary School (4th Ave.)</option>
                                  <option value="16">Bagong Silang High School</option>
                                  <option value="17">Bagumbong Elementary School</option>
                                  <option value="18">Bagumbong High School</option>
                                  <option value="19">Benigno Aquino Junior High School</option>
                                  <option value="20">Brixton Senior High School</option>
                                  <option value="21">Caloocan Central Elementary School</option>
                                  <option value="22">Caloocan City Business High School</option>
                                  <option value="23">Caloocan City Science High School</option>
                                  <option value="24">Caloocan High School</option>
                                  <option value="25">Caloocan Nat’L Science & Technology Hs</option>
                                  <option value="26">Caloocan North Elementary School</option>
                                  <option value="27">Camarin D Elementary School</option>
                                  <option value="28">Camarin D Elementary School- Unit II</option>
                                  <option value="29">Camarin Elementary School</option>
                                  <option value="30">Camarin High School</option>
                                  <option value="31">Caybiga Elementary School</option>
                                  <option value="32">Caybiga High School</option>
                                  <option value="33">Cayetano Arellano Elementary School</option>
                                  <option value="34">Cecilio Apostol Elementary School</option>
                                  <option value="35">Cielito Zamora Elementary School</option>
                                  <option value="36">Cielito Zamora Junior High School</option>
                                  <option value="37">Cielito Zamora Senior High School</option>
                                  <option value="38">Congress Elementary School</option>
                                  <option value="39">Deparo Elementary School</option>
                                  <option value="40">Deparo High School</option>
                                  <option value="41">East Bagong Barrio Elementary School</option>
                                  <option value="42">Eulogio Rodriguez Elementary School</option>
                                  <option value="43">Gabriela Silang Elementary School</option>
                                  <option value="44">Gomburza Elementary School</option>
                                  <option value="45">Grace Park Elementary School</option>
                                  <option value="46">Gregoria De Jesus Elementary School</option>
                                  <option value="47">Horacio Dela Costa Elementary School</option>
                                  <option value="48">Horacio Dela Costa High School</option>
                                  <option value="49">Kalayaan Elementary School</option>
                                  <option value="50">Kalayaan National High School</option>
                                  <option value="51">Kasarinlan Elementary School</option>
                                  <option value="52">Kasarinlan High School</option>
                                  <option value="53">Kaunlaran Elementary School</option>
                                  <option value="54">Lerma Elementary School</option>
                                  <option value="55">Libis Baesa Elementary School</option>
                                  <option value="56">Libis Talisay Elementary School</option>
                                  <option value="57">Llano Elementary School</option>
                                  <option value="58">Llano High School</option>
                                  <option value="59">M.B. Asistio High School – Unit I</option>
                                  <option value="60">M.B. Asistio Senior High School</option>
                                  <option value="61">Ma. Clara High School</option>
                                  <option value="62">Manuel L. Quezon Elementary School</option>
                                  <option value="63">Manuel L. Quezon High School</option>
                                  <option value="64">Marcelo H. Del Pilar Elementary School</option>
                                  <option value="65">Maypajo Elementary School</option>
                                  <option value="66">Maypajo High School</option>
                                  <option value="67">Morning Breeze Elementary School</option>
                                  <option value="68">Mountain Heights High School</option>
                                  <option value="69">NHC Elementary School</option>
                                  <option value="70">NHC High School</option>
                                  <option value="71">Pag-Asa Elementary School</option>
                                  <option value="72">Pangarap Elementary School</option>
                                  <option value="73">Pangarap High School</option>
                                  <option value="74">Rene Cayetano Elementary School</option>
                                  <option value="75">Samaria Senior High School</option>
                                  <option value="76">Sampaguita Elementary School</option>
                                  <option value="77">Sampaguita High School</option>
                                  <option value="78">Sampalukan Elementary School</option>
                                  <option value="79">San Jose Elementary School</option>
                                  <option value="80">Silanganan Elementary School</option>
                                  <option value="81">Sta. Quiteria Elementary School</option>
                                  <option value="82">Sto. Niño Elementary School</option>
                                  <option value="83">Tala Elementary School</option>
                                  <option value="84">Tala High School</option>
                                  <option value="85">Talipapa Elementary School</option>
                                  <option value="86">Talipapa High School</option>
                                  <option value="87">Tandang Sora Integrated School</option>
                                  <option value="88">Urduja Elementary School</option>
                                  <option value="89">Vicente Malapitan Senior High School</option>
                              </select>
                          </div>

                        <div class="form-group">
                            <label for="role"><i class="fas fa-user-tag"></i> Role:</label>
                            <select class="form-control" id="role" name="role" onchange="toggleSchoolSelection()" required>
                                <option value="">- Select Role -</option>
                                <option value="Admin">Admin</option>
                                <option value="Main">Main Admin</option>
                                <option value="Supplier">Supplier</option>
                            </select>
                        </div>
                        <div class="form-group" id="school-selection">
                            <label for="school"><i class="fas fa-school"></i> Select School:</label>
                            <select class="form-control" id="school" name="school">
                                <option value="">- Select School -</option>
                                <!-- School options go here -->
                            </select>
                        </div>
                        <div class="d-flex justify-content-end"> <!-- Changed to justify-content-end for right alignment -->
                            <button type="submit" class="btn-icon btn-submit">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
