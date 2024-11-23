<?php

// Make sure the session variables are set before using them
if (isset($_SESSION['school_id']) && isset($_SESSION['role'])) {
    $school_id = $_SESSION['school_id'];
    $role = $_SESSION['role'];

    // Map school IDs to school names
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
        90 => 'mainAdmin_dashboard'
    ];

    // Check if the school_id exists in the array
    if (array_key_exists($school_id, $schools)) {
        $school_name_url = $schools[$school_id];

        // Determine the dashboard URL based on the role
        if ($role == "Admin") {
            $dashboard_url = "admin_dashboard.php";
        } else if ($role == "Main") {
            $dashboard_url = "{$school_name_url}.php";
        } else {
            // Default case or error handling
            $dashboard_url = "login.php";
        }
    } else {
        // Default case if the school_id doesn't match
        $dashboard_url = "login.php";
    }
} else {
    // Default case if session variables are not set
    $dashboard_url = "login.php"; // Redirect to login or home page
}
?>

<!-- Back button that redirects to the determined dashboard URL -->
<button onclick="window.location.href='<?php echo $dashboard_url; ?>'" class="btn btn-secondary mb-3">Back</button>
