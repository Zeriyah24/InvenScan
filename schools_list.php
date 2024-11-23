<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    // Redirect to the login page or another appropriate page
    header("Location: login.php"); // Adjust as per your application flow
    exit;
}

// Sample school data (you can replace this with a database query)
  $schools = [
      ['name' => 'A. Mabini Elementary School', 'dashboard' => 'dashboard_A.+Mabini+Elementary+School.php'],
      ['name' => 'Amparo Elementary School', 'dashboard' => 'dashboard_Amparo+Elementary+School.php'],
      ['name' => 'Amparo High School', 'dashboard' => 'dashboard_Amparo+High+School.php'],
      ['name' => 'Andres Bonifacio Elementary School', 'dashboard' => 'dashboard_Andres+Bonifacio+Elementary+School.php'],
      ['name' => 'Antonio Luna Elementary School', 'dashboard' => 'dashboard_Antonio+Luna+Elementary+School.php'],
      ['name' => 'Antonio Luna High School', 'dashboard' => 'dashboard_Antonio+Luna+High+School.php'],
      ['name' => 'AntonioUy Tan Senior High School', 'dashboard' => 'dashboard_AntonioUy+Tan+Senior+High+School.php'],
      ['name' => 'Baesa Elementary School', 'dashboard' => 'dashboard_Baesa+Elementary+School.php'],
      ['name' => 'Baesa High School', 'dashboard' => 'dashboard_Baesa+High+School.php'],
      ['name' => 'Bagbaguin Elementary School', 'dashboard' => 'dashboard_Bagbaguin+Elementary+School.php'],
      ['name' => 'Bagong Barrio Elementary School', 'dashboard' => 'dashboard_Bagong+Barrio+Elementary+School.php'],
      ['name' => 'Bagong Barrio National High School', 'dashboard' => 'dashboard_Bagong+Barrio+National+High+School.php'],
      ['name' => 'Bagong Barrio Senior High School', 'dashboard' => 'dashboard_Bagong+Barrio+Senior+High+School.php'],
      ['name' => 'Bagong Silang Elementary School', 'dashboard' => 'dashboard_Bagong+Silang+Elementary+School.php'],
      ['name' => 'Bagong Silang Elementary School (4th Ave.)', 'dashboard' => 'dashboard_Bagong+Silang+Elementary+School+(4th+Ave.).php'],
      ['name' => 'Bagong Silang High School', 'dashboard' => 'dashboard_Bagong+Silang+High+School.php'],
      ['name' => 'Bagumbong Elementary School', 'dashboard' => 'dashboard_Bagumbong+Elementary+School.php'],
      ['name' => 'Bagumbong High School', 'dashboard' => 'dashboard_Bagumbong+High+School.php'],
      ['name' => 'Benigno Aquino Junior High School', 'dashboard' => 'dashboard_Benigno+Aquino+Junior+High+School.php'],
      ['name' => 'Brixton Senior High School', 'dashboard' => 'dashboard_Brixton+Senior+High+School.php'],
      ['name' => 'Caloocan Central Elementary School', 'dashboard' => 'dashboard_Caloocan+Central+Elementary+School.php'],
      ['name' => 'Caloocan City Business High School', 'dashboard' => 'dashboard_Caloocan+City+Business+High+School.php'],
      ['name' => 'Caloocan City Science High School', 'dashboard' => 'dashboard_Caloocan+City+Science+High+School.php'],
      ['name' => 'Caloocan High School', 'dashboard' => 'dashboard_Caloocan+High+School.php'],
      ['name' => 'Caloocan Nat’l Science & Technology Hs', 'dashboard' => 'dashboard_Caloocan+Nat’l+Science+&+Technology+Hs.php'],
      ['name' => 'Caloocan North Elementary School', 'dashboard' => 'dashboard_Caloocan+North+Elementary+School.php'],
      ['name' => 'Camarin D Elementary School', 'dashboard' => 'dashboard_Camarin+D+Elementary+School.php'],
      ['name' => 'Camarin D Elementary School-Unit II', 'dashboard' => 'dashboard_Camarin+D+Elementary+School-Unit+II.php'],
      ['name' => 'Camarin Elementary School', 'dashboard' => 'dashboard_Camarin+Elementary+School.php'],
      ['name' => 'Camarin High School', 'dashboard' => 'dashboard_Camarin+High+School.php'],
      ['name' => 'Caybiga Elementary School', 'dashboard' => 'dashboard_Caybiga+Elementary+School.php'],
      ['name' => 'Caybiga High School', 'dashboard' => 'dashboard_Caybiga+High+School.php'],
      ['name' => 'Cayetano Arellano Elementary School', 'dashboard' => 'dashboard_Cayetano+Arellano+Elementary+School.php'],
      ['name' => 'Cecilio Apostol Elementary School', 'dashboard' => 'dashboard_Cecilio+Apostol+Elementary+School.php'],
      ['name' => 'Cielito Zamora Elementary School', 'dashboard' => 'dashboard_Cielito+Zamora+Elementary+School.php'],
      ['name' => 'Cielito Zamora Junior High School', 'dashboard' => 'dashboard_Cielito+Zamora+Junior+High+School.php'],
      ['name' => 'Cielito Zamora Senior High School', 'dashboard' => 'dashboard_Cielito+Zamora+Senior+High+School.php'],
      ['name' => 'Congress Elementary School', 'dashboard' => 'dashboard_Congress+Elementary+School.php'],
      ['name' => 'Deparo High School', 'dashboard' => 'dashboard_Deparo+High+School.php'],
      ['name' => 'East Bagong Barrio Elementary School', 'dashboard' => 'dashboard_East+Bagong+Barrio+Elementary+School.php'],
      ['name' => 'Eulogio Rodriguez Elementary School', 'dashboard' => 'dashboard_Eulogio+Rodriguez+Elementary+School.php'],
      ['name' => 'Gabriela Silang Elementary School', 'dashboard' => 'dashboard_Gabriela+Silang+Elementary+School.php'],
      ['name' => 'Gomburza Elementary School', 'dashboard' => 'dashboard_Gomburza+Elementary+School.php'],
      ['name' => 'Grace Park Elementary School', 'dashboard' => 'dashboard_Grace+Park+Elementary+School.php'],
      ['name' => 'Gregoria De Jesus Elementary School', 'dashboard' => 'dashboard_Gregoria+De+Jesus+Elementary+School.php'],
      ['name' => 'Horacio Dela Costa Elementary School', 'dashboard' => 'dashboard_Horacio+Dela+Costa+Elementary+School.php'],
      ['name' => 'Horacio Dela Costa High School', 'dashboard' => 'dashboard_Horacio+Dela+Costa+High+School.php'],
      ['name' => 'Kalayaan Elementary School', 'dashboard' => 'dashboard_Kalayaan+Elementary+School.php'],
      ['name' => 'Kalayaan National High School', 'dashboard' => 'dashboard_Kalayaan+National+High+School.php'],
      ['name' => 'Kasarinlan Elementary School', 'dashboard' => 'dashboard_Kasarinlan+Elementary+School.php'],
      ['name' => 'Kasarinlan High School', 'dashboard' => 'dashboard_Kasarinlan+High+School.php'],
      ['name' => 'Kaunlaran Elementary School', 'dashboard' => 'dashboard_Kaunlaran+Elementary+School.php'],
      ['name' => 'Lerma Elementary School', 'dashboard' => 'dashboard_Lerma+Elementary+School.php'],
      ['name' => 'Libis Baesa Elementary School', 'dashboard' => 'dashboard_Libis+Baesa+Elementary+School.php'],
      ['name' => 'Libis Talisay Elementary School', 'dashboard' => 'dashboard_Libis+Talisay+Elementary+School.php'],
      ['name' => 'Llano Elementary School', 'dashboard' => 'dashboard_Llano+Elementary+School.php'],
      ['name' => 'Llano High School', 'dashboard' => 'dashboard_Llano+High+School.php'],
      ['name' => 'M.B. Asistio High School-Unit I', 'dashboard' => 'dashboard_MB+Asistio+High+School-Unit+I.php'],
      ['name' => 'M.B. Asistio Senior High School', 'dashboard' => 'dashboard_MB+Asistio+Senior+High+School.php'],
      ['name' => 'Ma. Clara High School', 'dashboard' => 'dashboard_Ma+Clara+High+School.php'],
      ['name' => 'Manuel L. Quezon Elementary School', 'dashboard' => 'dashboard_Manuel+L+Quezon+Elementary+School.php'],
      ['name' => 'Manuel L. Quezon High School', 'dashboard' => 'dashboard_Manuel+L+Quezon+High+School.php'],
      ['name' => 'Marcelo H. Del Pilar Elementary School', 'dashboard' => 'dashboard_Marcelo+H+Del+Pilar+Elementary+School.php'],
      ['name' => 'Maypajo Elementary School', 'dashboard' => 'dashboard_Maypajo+Elementary+School.php'],
      ['name' => 'Maypajo High School', 'dashboard' => 'dashboard_Maypajo+High+School.php'],
      ['name' => 'Morning Breeze Elementary School', 'dashboard' => 'dashboard_Morning+Breeze+Elementary+School.php'],
      ['name' => 'Mountain Heights High School', 'dashboard' => 'dashboard_Mountain+Heights+High+School.php'],
      ['name' => 'NHC Elementary School', 'dashboard' => 'dashboard_NHC+Elementary+School.php'],
      ['name' => 'NHC High School', 'dashboard' => 'dashboard_NHC+High+School.php'],
      ['name' => 'Pag-Asa Elementary School', 'dashboard' => 'dashboard_Pag-Asa+Elementary+School.php'],
      ['name' => 'Pangarap Elementary School', 'dashboard' => 'dashboard_Pangarap+Elementary+School.php'],
      ['name' => 'Pangarap High School', 'dashboard' => 'dashboard_Pangarap+High+School.php'],
      ['name' => 'Rene Cayetano Elementary School', 'dashboard' => 'dashboard_Rene+Cayetano+Elementary+School.php'],
      ['name' => 'Samaria Senior High School', 'dashboard' => 'dashboard_Samaria+Senior+High+School.php'],
      ['name' => 'Sampaguita Elementary School', 'dashboard' => 'dashboard_Sampaguita+Elementary+School.php'],
      ['name' => 'Sampaguita High School', 'dashboard' => 'dashboard_Sampaguita+High+School.php'],
      ['name' => 'Sampalukan Elementary School', 'dashboard' => 'dashboard_Sampalukan+Elementary+School.php'],
      ['name' => 'San Jose Elementary School', 'dashboard' => 'dashboard_San+Jose+Elementary+School.php'],
      ['name' => 'Silanganan Elementary School', 'dashboard' => 'dashboard_Silanganan+Elementary+School.php'],
      ['name' => 'Sta. Quiteria Elementary School', 'dashboard' => 'dashboard_Sta+Quiteria+Elementary+School.php'],
      ['name' => 'Sto. Niño Elementary School', 'dashboard' => 'dashboard_Sto+Niño+Elementary+School.php'],
      ['name' => 'Tala Elementary School', 'dashboard' => 'dashboard_Tala+Elementary+School.php'],
      ['name' => 'Tala High School', 'dashboard' => 'dashboard_Tala+High+School.php'],
      ['name' => 'Talipapa Elementary School', 'dashboard' => 'dashboard_Talipapa+Elementary+School.php'],
      ['name' => 'Talipapa High School', 'dashboard' => 'dashboard_Talipapa+High+School.php'],
      ['name' => 'Tandang Sora Integrated School', 'dashboard' => 'dashboard_Tandang+Sora+Integrated+School.php'],
      ['name' => 'Urduja Elementary School', 'dashboard' => 'dashboard_Urduja+Elementary+School.php'],
      ['name' => 'Vicente Malapitan Senior High School', 'dashboard' => 'dashboard_Vicente+Malapitan+Senior+High+School.php'],
  ];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schools List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 40px;
            max-width: 700px;
        }

        h1 {
            color: #007bff;
            font-weight: bold;
            font-size: 28px;
            text-align: center;
            margin-bottom: 30px;
        }

        .list-group-item {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 8px;
            padding: 15px 20px;
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f1f3f5;
        }

        .more-info {
            text-decoration: none;
            color: #007bff;
            display: flex;
            align-items: center;
        }

        .more-info:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .back-icon {
            font-size: 22px;
            color: #ffffff;
        }

        .back-btn {
            background-color: #007bff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-start mb-3">
            <a href="mainAdmin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left back-icon"></i>
            </a>
        </div>
        <h1>Public Schools in Caloocan City</h1>
        <ul class="list-group">
            <?php foreach ($schools as $school): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold"><?php echo $school['name']; ?></span>
                    <a href="<?php echo $school['dashboard']; ?>" class="more-info">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>

</html>
