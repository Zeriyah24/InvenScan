<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Technician</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        h1 {
            color: #343a40;
            margin-bottom: 30px;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        #pst-container {
            margin-left: auto;
            text-align: right;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .navbar {
            margin-bottom: 30px;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 30px;
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
        <div id="pst-container">
            <div>Philippine Standard Time:</div>
            <div id="pst-time"></div>
            <div id="pst-date"></div>
        </div>
    </nav>

    <div class="container">
        <h1>Request Technician</h1>
        <div class="card">
            <div class="card-body">
                <form action="submit_ticket.php" method="POST">
                    <div class="form-group">
                        <label for="school_id">School ID:</label>
                        <input type="number" name="school_id" id="school_id" class="form-control" required placeholder="Enter School ID">
                    </div>

                    <div class="form-group">
                        <label for="request_date">Request Date:</label>
                        <input type="date" name="request_date" id="request_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="technician_needed">Technician Needed:</label>
                        <select name="technician_needed" id="technician_needed" class="form-control" required>
                            <option value="">Select a Technician</option>
                            <option value="software_support">Software Support</option>
                            <option value="networking_support">Networking Support</option>
                            <option value="equipment_repair">Repair and Replacement of Equipment</option>
                            <option value="diagnoses">Diagnoses</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <small class="form-text text-muted">Please select the type of technician needed for the maintenance.</small>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes:</label>
                        <textarea name="notes" id="notes" class="form-control" rows="6" placeholder="Please provide details about the issue or request..."></textarea>
                        <small class="form-text text-muted">Include any specific details that can help the technician address the issue.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 School Division Office of Caloocan. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function () {
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
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
