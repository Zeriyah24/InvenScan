<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schools</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 75px;
            width: auto;
        }

        .school-division h2 {
            color: green;
            font-size: 36px;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #D4E7C5;
            margin-top: 100px;
            overflow-y: auto;
        }

        .panel {
            background-color: #D4E7C5;
            padding: 20px;
            border-radius: 4px;
            color: #2c3e50;
        }

        .list-view {
            list-style-type: none;
            padding: 0;
        }

        .list-view li {
            display: flex;
            align-items: center;
            background-color: #E1F0DA;
            margin-bottom: 15px; /* Increased margin for better spacing */
            padding: 15px; /* Increased padding for a more comfortable feel */
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
            transition: transform 0.2s, box-shadow 0.2s; /* Smooth transition */
        }

        .list-view li:hover {
            transform: translateY(-2px); /* Slight upward movement on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Stronger shadow on hover */
        }

        .list-view li a {
            text-decoration: none; /* Remove underline from links */
            color: #2c3e50; /* Link color */
            flex: 1; /* Allow link to take full width */
            display: flex;
            align-items: center;
        }

        .list-view li a .icon {
            margin-right: 10px; /* Space between icon and text */
        }

        .list-view li a:hover {
            color: #007bff; /* Change color on hover */
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 800px;
            border-radius: 4px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 16px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        #pst-container {
            text-align: right;
        }

        .btn-icon {
            padding: 10px;
            border: none;
            background: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-icon:hover {
            background-color: #e1e1e1;
            border-radius: 4px;
        }
    </style>
    <script>
        // Function to display time and date
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

        // Display school list directly on page load
        window.onload = function () {
          const schoolData = {
        school01: {
            schoolName: "A. Mabini Elementary School",
            dashboard: "dashboard_A.+Mabini+Elementary+School.php",
        },
        school02: {
            schoolName: "Amparo Elementary School",
            dashboard: "dashboard_Amparo+Elementary+School.php",
        },
        school03: {
            schoolName: "Amparo High School",
            dashboard: "dashboard_Amparo+High+School.php",
        },
        school04: {
            schoolName: "Andres Bonifacio Elementary School",
            dashboard: "dashboard_Andres+Bonifacio+Elementary+School.php",
        },
        school05: {
            schoolName: "Antonio Luna Elementary School",
            dashboard: "dashboard_Antonio+Luna+Elementary+School.php",
        },
        school06: {
            schoolName: "Antonio Luna High School",
            dashboard: "dashboard_Antonio+Luna+High+School.php",
        },
        school07: {
            schoolName: "Antonio Uy Tan Senior High School",
            dashboard: "dashboard_AntonioUy+Tan+Senior+High+School.php",
        },
        school08: {
            schoolName: "Baesa Elementary School",
            dashboard: "dashboard_Baesa+Elementary+School.php",
        },
        school09: {
            schoolName: "Baesa High School",
            dashboard: "dashboard_Baesa+High+School.php",
        },
        school10: {
                schoolName: "Bagbaguin Elementary School",
                dashboard: "dashboard_Bagbaguin+Elementary+School.php",
        },
        school11: {
          schoolName: "Bagong Barrio Elementary School",
          dashboard: "dashboard_Bagong+Barrio+Elementary+School.php",
        },
        school12: {
          schoolName: "Bagong Barrio National High School",
          dashboard: "dashboard_Bagong+Barrio+National+High+School.php",
        },
        school13: {
          schoolName: "Bagong Barrio Senior High School",
          dashboard: "dashboard_Bagong+Barrio+Senior+High+School.php",
        },
        school14: {
          schoolName: "Bagong Silang Elementary School",
          dashboard: "dashboard_Bagong+Silang+Elementary+School.php",
        },
        school15: {
          schoolName: "Bagong Silang Elementary School (4th Ave.)",
          dashboard: "dashboard_Bagong+Silang+Elementary+School+(4th+Ave.).php",
        },
        school16: {
          schoolName: "Bagong Silang High School",
          dashboard: "dashboard_Bagong+Silang+High+School.php",
        },
        school17: {
          schoolName: "Bagumbong Elementary School",
          dashboard: "dashboard_Bagumbong+Elementary+School.php",
        },
        school18: {
          schoolName: "Bagumbong High School",
          dashboard: "dashboard_Bagumbong+High+School.php",
        },
        school19: {
          schoolName: "Benigno Aquino Junior High School",
          dashboard: "dashboard_Benigno+Aquino+Junior+High+School.php",
        },
        school20: {
          schoolName: "Brixton Senior High School",
          dashboard: "dashboard_Brixton+Senior+High+School.php",
        },
        school21: {
          schoolName: "Caloocan Central Elementary School",
          dashboard: "dashboard_Caloocan+Central+Elementary+School.php",
        },
        school22: {
          schoolName: "Caloocan City Business High School",
          dashboard: "dashboard_Caloocan+City+Business+High+School.php",
        },
        school23: {
          schoolName: "Caloocan City Science High School",
          dashboard: "dashboard_Caloocan+City+Science+High+School.php",
        },
        school24: {
          schoolName: "Caloocan High School",
          dashboard: "dashboard_Caloocan+High+School.php",
        },
        school25: {
          schoolName: "Caloocan Nat’l Science & Technology Hs",
          dashboard: "dashboard_Caloocan+Nat’l+Science+&+Technology+Hs.php",
        },
        school26: {
          schoolName: "Caloocan North Elementary School",
          dashboard: "dashboard_Caloocan+North+Elementary+School.php",
        },
        school27: {
          schoolName: "Camarin D Elementary School",
          dashboard: "dashboard_Camarin+D+Elementary+School.php",
        },
        school28: {
          schoolName: "Camarin D Elementary School-Unit II",
          dashboard: "dashboard_Camarin+D+Elementary+School-Unit+II.php",
        },
        school29: {
          schoolName: "Camarin Elementary School",
          dashboard: "dashboard_Camarin+Elementary+School.php",
        },
        school30: {
          schoolName: "Camarin High School",
          dashboard: "dashboard_Camarin+High+School.php",
        },
        school31: {
          schoolName: "Caybiga Elementary School",
          dashboard: "dashboard_Caybiga+Elementary+School.php",
        },
        school32: {
          schoolName: "Caybiga High School",
          dashboard: "dashboard_Caybiga+High+School.php",
        },
        school33: {
          schoolName: "Cayetano Arellano Elementary School",
          dashboard: "dashboard_Cayetano+Arellano+Elementary+School.php",
        },
        school34: {
          schoolName: "Cecilio Apostol Elementary School",
          dashboard: "dashboard_Cecilio+Apostol+Elementary+School.php",
        },
        school35: {
          schoolName: "Cielito Zamora Elementary School",
          dashboard: "dashboard_Cielito+Zamora+Elementary+School.php",
        },
        school36: {
          schoolName: "Cielito Zamora Junior High School",
          dashboard: "dashboard_Cielito+Zamora+Junior+High+School.php",
        },
        school37: {
          schoolName: "Cielito Zamora Senior High School",
          dashboard: "dashboard_Cielito+Zamora+Senior+High+School.php",
        },
        school38: {
          schoolName: "Congress Elementary School",
          dashboard: "dashboard_Congress+Elementary+School.php",
        },
        school39: {
          schoolName: "Deparo Elementary School",
          dashboard: "dashboard_Deparo+Elementary+School.php",
        },
        school40: {
          schoolName: "Deparo High School",
          dashboard: "dashboard_Deparo+High+School.php",
        },
        school41: {
          schoolName: "East Bagong Barrio Elementary School",
          dashboard: "dashboard_East+Bagong+Barrio+Elementary+School.php",
        },
        school42: {
          schoolName: "Eulogio Rodriguez Elementary School",
          dashboard: "dashboard_Eulogio+Rodriguez+Elementary+School.php",
        },
        school43: {
          schoolName: "Gabriela Silang Elementary School",
          dashboard: "dashboard_Gabriela+Silang+Elementary+School.php",
        },
        school44: {
          schoolName: "Gomburza Elementary School",
          dashboard: "dashboard_Gomburza+Elementary+School.php",
        },
        school45: {
          schoolName: "Grace Park Elementary School",
          dashboard: "dashboard_Grace+Park+Elementary+School.php",
        },
        school46: {
          schoolName: "Gregoria De Jesus Elementary School",
          dashboard: "dashboard_Gregoria+De+Jesus+Elementary+School.php",
        },
        school47: {
          schoolName: "Horacio Dela Costa Elementary School",
          dashboard: "dashboard_Horacio+Dela+Costa+Elementary+School.php",
        },
        school48: {
          schoolName: "Horacio Dela Costa High School",
          dashboard: "dashboard_Horacio+Dela+Costa+High+School.php",
        },
        school49: {
          schoolName: "Kalayaan Elementary School",
          dashboard: "dashboard_Kalayaan+Elementary+School.php",
        },
        school50: {
          schoolName: "Kalayaan National High School",
          dashboard: "dashboard_Kalayaan+National+High+School.php",
        },
        school51: {
          schoolName: "Kasarinlan Elementary School",
          dashboard: "dashboard_Kasarinlan+Elementary+School.php",
        },
        school52: {
          schoolName: "Kasarinlan High School",
          dashboard: "dashboard_Kasarinlan+High+School.php",
        },
        school53: {
          schoolName: "Kaunlaran Elementary School",
          dashboard: "dashboard_Kaunlaran+Elementary+School.php",
        },
        school54: {
          schoolName: "Lerma Elementary School",
          dashboard: "dashboard_Lerma+Elementary+School.php",
        },
        school55: {
          schoolName: "Libis Baesa Elementary School",
          dashboard: "dashboard_Libis+Baesa+Elementary+School.php",
        },
        school56: {
          schoolName: "Libis Talisay Elementary School",
          dashboard: "dashboard_Libis+Talisay+Elementary+School.php",
        },
        school57: {
          schoolName: "Llano Elementary School",
          dashboard: "dashboard_Llano+Elementary+School.php",
        },
        school58: {
          schoolName: "Llano High School",
          dashboard: "dashboard_Llano+High+School.php",
        },
        school59: {
          schoolName: "M.B. Asistio High School-Unit I",
          dashboard: "dashboard_MB+Asistio+High+School-Unit+I.php",
        },
        school60: {
          schoolName: "M.B. Asistio Senior High School",
          dashboard: "dashboard_MB+Asistio+Senior+High+School.php",
        },
        school61: {
          schoolName: "Ma. Clara High School",
          dashboard: "dashboard_Ma+Clara+High+School.php",
        },
        school62: {
          schoolName: "Manuel L. Quezon Elementary School",
          dashboard: "dashboard_Manuel+L+Quezon+Elementary+School.php",
        },
        school63: {
          schoolName: "Manuel L. Quezon High School",
          dashboard: "dashboard_Manuel+L+Quezon+High+School.php",
        },
        school64: {
          schoolName: "Marcelo H. Del Pilar Elementary School",
          dashboard: "dashboard_Marcelo+H+Del+Pilar+Elementary+School.php",
        },
        school65: {
          schoolName: "Maypajo Elementary School",
          dashboard: "dashboard_Maypajo+Elementary+School.php",
        },
        school66: {
          schoolName: "Maypajo High School",
          dashboard: "dashboard_Maypajo+High+School.php",
        },
        school67: {
          schoolName: "Morning Breeze Elementary School",
          dashboard: "dashboard_Morning+Breeze+Elementary+School.php",
        },
        school68: {
          schoolName: "Mountain Heights High School",
          dashboard: "dashboard_Mountain+Heights+High+School.php",
        },
        school69: {
          schoolName: "NHC Elementary School",
          dashboard: "dashboard_NHC+Elementary+School.php",
        },
        school70: {
          schoolName: "NHC High School",
          dashboard: "dashboard_NHC+High+School.php",
        },
        school71: {
          schoolName: "Pag-Asa Elementary School",
          dashboard: "dashboard_Pag-Asa+Elementary+School.php",
        },
        school72: {
          schoolName: "Pangarap Elementary School",
          dashboard: "dashboard_Pangarap+Elementary+School.php",
        },
        school73: {
          schoolName: "Pangarap High School",
          dashboard: "dashboard_Pangarap+High+School.php",
        },
        school74: {
          schoolName: "Rene Cayetano Elementary School",
          dashboard: "dashboard_Rene+Cayetano+Elementary+School.php",
        },
        school75: {
          schoolName: "Samaria Senior High School",
          dashboard: "dashboard_Samaria+Senior+High+School.php",
        },
        school76: {
          schoolName: "Sampaguita Elementary School",
          dashboard: "dashboard_Sampaguita+Elementary+School.php",
        },
        school77: {
          schoolName: "Sampaguita High School",
          dashboard: "dashboard_Sampaguita+High+School.php",
        },
        school78: {
          schoolName: "Sampalukan Elementary School",
          dashboard: "dashboard_Sampalukan+Elementary+School.php",
        },
        school79: {
          schoolName: "San Jose Elementary School",
          dashboard: "dashboard_San+Jose+Elementary+School.php",
        },
        school80: {
          schoolName: "Silanganan Elementary School",
          dashboard: "dashboard_Silanganan+Elementary+School.php",
        },
        school81: {
          schoolName: "Sta. Quiteria Elementary School",
          dashboard: "dashboard_Sta+Quiteria+Elementary+School.php",
        },
        school82: {
          schoolName: "Sto. Niño Elementary School",
          dashboard: "dashboard_Sto+Niño+Elementary+School.php",
        },
        school83: {
          schoolName: "Tala Elementary School",
          dashboard: "dashboard_Tala+Elementary+School.php",
        },
        school84: {
          schoolName: "Tala High School",
          dashboard: "dashboard_Tala+High+School.php",
        },
        school85: {
          schoolName: "Talipapa Elementary School",
          dashboard: "dashboard_Talipapa+Elementary+School.php",
        },
        school86: {
          schoolName: "Talipapa High School",
          dashboard: "dashboard_Talipapa+High+School.php",
        },
        school87: {
          schoolName: "Tandang Sora Integrated School",
          dashboard: "dashboard_Tandang+Sora+Integrated+School.php",
        },
        school88: {
          schoolName: "Urduja Elementary School",
          dashboard: "dashboard_Urduja+Elementary+School.php",
        },
        school89: {
          schoolName: "Vicente Malapitan Senior High School",
          dashboard: "dashboard_Vicente+Malapitan+Senior+High+School.php",
                  }
                };


            const itemList = document.getElementById("item-list");
            // Loop through schoolData and create list items
            Object.keys(schoolData).forEach(key => {
                const school = schoolData[key];
                const listItem = document.createElement("li");
                const link = document.createElement("a");
                link.href = school.dashboard;
                link.className = 'dropdown-item';

                // Create icon element
                const icon = document.createElement("i");
                icon.className = "fas fa-school icon"; // Font Awesome icon

                link.appendChild(icon);
                link.appendChild(document.createTextNode(school.schoolName));

                listItem.appendChild(link);
                itemList.appendChild(listItem);
            });
        };
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo6.png" alt="School Logo">
        </a>
        <div class="school-division text-center mx-auto">
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
    <div class="container-fluid">
        <div class="content">
            <div class="d-flex align-items-center mb-3">
                <a href="login.php" class="btn btn-icon" title="Back">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="mb-0">School Information</h2>
            </div>
            <div class="panel" id="batch-info">
                <ul class="list-view" id="item-list">
                    <!-- School list will be populated here -->
                </ul>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-text"></div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
