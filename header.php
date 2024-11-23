<!-- header.php -->
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
<style>
        .school-division h2 {
            margin: 0;
            font-size: 36px;
            color: green;
            text-align: center;
        }
        #pst-container {
            text-align: right;
        }
        .navbar {
            border-bottom: 2px solid #5DA14E;
        } 
    </style>

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
