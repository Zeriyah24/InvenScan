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

$schools = [
    1 => 'A.+Mabini+Elementary+School',
    2 => 'Amparo+Elementary+School',
    3 => 'Amparo+High+School',
    4 => 'Andres+Bonifacio+Elementary+School',
    5 => 'Antonio+Luna+Elementary+School',
    6 => 'Antonio+Luna+High+School',
    7 => 'Antonio+Uy+Tan+Senior+High+School',
    8 => 'Baesa+Elementary+School',
    9 => 'Baesa+High+School',
    10 => 'Bagbaguin+Elementary+School',
    11 => 'Bagong+Barrio+Elementary+School',
    12 => 'Bagong+Barrio+National+High+School',
    13 => 'Bagong+Barrio+Senior+High+School',
    14 => 'Bagong+Silang+Elementary+School',
    15 => 'Bagong+Silang+Elementary+School+(4th+Ave.)',
    16 => 'Bagong+Silang+High+School',
    17 => 'Bagumbong+Elementary+School',
    18 => 'Bagumbong+High+School',
    19 => 'Benigno+Aquino+Junior+High+School',
    20 => 'Brixton+Senior+High+School',
    21 => 'Caloocan+Central+Elementary+School',
    22 => 'Caloocan+City+Business+High+School',
    23 => 'Caloocan+City+Science+High+School',
    24 => 'Caloocan+High+School',
    25 => 'Caloocan+Nat’L+Science+&+Technology+Hs',
    26 => 'Caloocan+North+Elementary+School',
    27 => 'Camarin+D+Elementary+School',
    28 => 'Camarin+D+Elementary+School-+Unit+II',
    29 => 'Camarin+Elementary+School',
    30 => 'Camarin+High+School',
    31 => 'Caybiga+Elementary+School',
    32 => 'Caybiga+High+School',
    33 => 'Cayetano+Arellano+Elementary+School',
    34 => 'Cecilio+Apostol+Elementary+School',
    35 => 'Cielito+Zamora+Elementary+School',
    36 => 'Cielito+Zamora+Junior+High+School',
    37 => 'Cielito+Zamora+Senior+High+School',
    38 => 'Congress+Elementary+School',
    39 => 'Deparo+Elementary+School',
    40 => 'Deparo+High+School',
    41 => 'East+Bagong+Barrio+Elementary+School',
    42 => 'Eulogio+Rodriguez+Elementary+School',
    43 => 'Gabriela+Silang+Elementary+School',
    44 => 'Gomburza+Elementary+School',
    45 => 'Grace+Park+Elementary+School',
    46 => 'Gregoria+De+Jesus+Elementary+School',
    47 => 'Horacio+Dela+Costa+Elementary+School',
    48 => 'Horacio+Dela+Costa+High+School',
    49 => 'Kalayaan+Elementary+School',
    50 => 'Kalayaan+National+High+School',
    51 => 'Kasarinlan+Elementary+School',
    52 => 'Kasarinlan+High+School',
    53 => 'Kaunlaran+Elementary+School',
    54 => 'Lerma+Elementary+School',
    55 => 'Libis+Baesa+Elementary+School',
    56 => 'Libis+Talisay+Elementary+School',
    57 => 'Llano+Elementary+School',
    58 => 'Llano+High+School',
    59 => 'M.B.+Asistio+High+School+–+Unit+I',
    60 => 'M.B.+Asistio+Senior+High+School',
    61 => 'Ma.+Clara+High+School',
    62 => 'Manuel+L.+Quezon+Elementary+School',
    63 => 'Manuel+L.+Quezon+High+School',
    64 => 'Marcelo+H.+Del+Pilar+Elementary+School',
    65 => 'Maypajo+Elementary+School',
    66 => 'Maypajo+High+School',
    67 => 'Morning+Breeze+Elementary+School',
    68 => 'Mountain+Heights+High+School',
    69 => 'NHC+Elementary+School',
    70 => 'NHC+High+School',
    71 => 'Pag-Asa+Elementary+School',
    72 => 'Pangarap+Elementary+School',
    73 => 'Pangarap+High+School',
    74 => 'Rene+Cayetano+Elementary+School',
    75 => 'Samaria+Senior+High+School',
    76 => 'Sampaguita+Elementary+School',
    77 => 'Sampaguita+High+School',
    78 => 'Sampalukan+Elementary+School',
    79 => 'San+Jose+Elementary+School',
    80 => 'Silanganan+Elementary+School',
    81 => 'Sta.+Quiteria+Elementary+School',
    82 => 'Sto.+Niño+Elementary+School',
    83 => 'Tala+Elementary+School',
    84 => 'Tala+High+School',
    85 => 'Talipapa+Elementary+School',
    86 => 'Talipapa+High+School',
    87 => 'Tandang+Sora+Integrated+School',
    88 => 'Urduja+Elementary+School',
    89 => 'Vicente+Malapitan+Senior+High+School',
    90 => 'mainAdmin_dashboard',
    91 => 'supplier',

];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT id, name, school_id, password FROM users WHERE email = ? AND role = ?");
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $school_id, $hashed_password);
        $stmt->fetch();

        // Verify password using password_verify function
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['school_id'] = $school_id;
            $_SESSION['role'] = $role;

            // Redirect based on role and school_id
// Check if session variables are set
if (isset($_SESSION['id']) && isset($_SESSION['name']) && isset($_SESSION['role'])) {
    // Redirect based on role and school_id
    if (isset($schools[$school_id])) {
        $school_name_url = $schools[$school_id];

        if ($role == "Admin") {
            header("location: admin_dashboard.php");
        } else if ($role == "User") {
            header("location: dashboard_{$school_name_url}.php");
        } else if($role == "Main"){
            header("location: {$school_name_url}.php");
          } else if($role == "Supplier"){
              header("location: supplier.php");
        } else {
            // Redirect to a generic dashboard for other roles
            header("location: dashboard_generic.php");
        }
        exit;
    }
}


        } else {
            // Incorrect password
            header("location: login.php?error=Incorrect password");
            exit;
        }
    } else {
        // No user found for the given email and role combination
        header("location: login.php?error=Email not found for the selected role");
        exit;
    }

    $stmt->close();
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #f0f4f8, #e0e6e9);
        }
        .navbar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }
        .school-division {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex-grow: 1;
        }
        .login-container {
            background-color: #fff;
            padding: 35px 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 40px;
            max-width: 400px;
            width: 100%;
            transition: box-shadow 0.3s ease;
        }
        .login-container:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        h2 {
            margin-bottom: 20px;
            color: green;
        }
        label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #007BFF;
            outline: none;
        }
        .icon-button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }
        .icon-button:hover {
            background-color: #0056b3;
        }
        .icon-button i {
            margin-right: 8px; /* Space between icon and text */
        }
        p {
            margin-top: 10px;
        }
        #error {
            color: #8B0000;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .footer-link {
            margin: 0 10px;
            color: #007BFF;
            text-decoration: none;
        }
        .footer-link:hover {
            text-decoration: underline;
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

        document.addEventListener('DOMContentLoaded', (event) => {
            <?php if (isset($_GET['error'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?php echo htmlspecialchars($_GET['error']); ?>',
                });
            <?php endif; ?>
        });

        
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo6.png" alt="School Logo" style="height: 75px; width: auto;">
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
    <div class="login-container">
        <h2>Login</h2>
        <form id="login-form" action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">

            <label for="role">Role:</label>
            <select id="role" name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Main">Main Admin</option>
                <option value="Supplier">Supplier</option>
            </select>

            <button type="submit" class="icon-button">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        <div class="footer-links">
            <a class="footer-link" href="forgot-password.php">
                <i class="fas fa-question-circle"></i> Forgot Password?
            </a>
            <a class="footer-link" href="view_school.php">
                <i class="fas fa-school"></i> View Schools
            </a>
        </div>
    </div>
</body>
</html>
