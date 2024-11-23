<?php
session_start();


// Check if the session variables are set
if (!isset($_SESSION['name']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Check if role is set and if it’s not 'Main' or if school_id is not 0
if (!isset($_SESSION['role']) || !isset($_SESSION['school_id']) || ($_SESSION['role'] !== 'Main' && $_SESSION['school_id'] !== 0)) {
    // Destroy the session
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
          body {
              margin: 0;
              font-family: Arial, sans-serif;
          }

          .breadcrumb-container {
              margin-top: 1px;
              padding-left: 10px;
          }

          .breadcrumb {
              background-color: transparent;
              margin-bottom: 0;
              font-size: 14px;
          }

          .dashboard-panel {
              padding: 20px;
          }

          .card {
              text-align: center;
              background-color: #BFD8AF;
              border: none;
              box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.1);
              border-radius: 10px;
              transition: transform 0.2s ease-in-out;
              height: 75%;
              padding: 30px 20px;
          }

          .card:hover {
              transform: translateY(-5px);
              box-shadow: 0px 10px 12px rgba(0, 0, 0, 0.2);
          }

          .card-title {
              font-size: 20px;
              font-weight: bold;
              margin-bottom: 15px;
          }

          .card-description {
              font-size: 14px;
              color: #7f8c8d;
              margin-bottom: 20px;
          }

          .card-body a {
              color: #fff;
              background-color: #C0EBA6;
              padding: 10px 20px;
              border-radius: 5px;
              text-decoration: none;
              font-size: 16px;
              transition: background-color 0.3s;
          }

          .card-body a:hover {
              background-color: #86D293;
          }

          #pst-container {
              text-align: right;
          }

          #pst-time,
          #pst-date {
              font-size: 14px;
              color: #2c3e50;
          }

          .row .col-md-4 {
              margin-bottom: 20px;
          }

          .dashboard-panel .row {
              justify-content: center;
          }

          /* Center the MAIN ADMIN heading */
          .main-admin-title {
              text-align: center;
              margin-bottom: 40px; /* Space between title and cards */
              font-size: 36px; /* Title size */
              color: #343a40; /* Title color */
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
  <?php include 'header.php'; ?>
      <div class="container mt-4 dashboard-panel">
          <h1 class="main-admin-title">MAIN ADMIN</h1>
          <div class="row">
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Account Creation</h5>
                          <p class="card-description">Create a new account for school staff and students.</p>
                          <a href="register.php">Go to Form</a>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Delivery</h5>
                          <p class="card-description">Manage and track school equipment and inventory items.</p>
                          <a href="delivery.php">Manage Inventory</a>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Inventory</h5>
                          <p class="card-description">Manage and track school equipment and inventory items.</p>
                          <a href="inventory.php">Manage Inventory</a>
                      </div>
                  </div>
              </div>

              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Ticketing System</h5>
                          <p class="card-description">Manage technical issues and track requests from schools.</p>
                          <a href="req_tech.php">Manage Tickets</a>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Schools List</h5>
                          <p class="card-description">View and manage the list of registered schools in the system.</p>
                          <a href="schools_list.php">View Schools</a>
                      </div>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Logout</h5>
                          <p class="card-description">Sign out of the admin panel securely.</p>
                          <br>
                          <a href="logout.php">Logout</a>
                      </div>
                  </div>
              </div>
          </div>
      </div>

<?php include 'footer.php'; ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
