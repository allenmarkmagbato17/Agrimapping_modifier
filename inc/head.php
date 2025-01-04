<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- para sa mobile device mo shink -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- TItle sa System Makita sa tab -->
    <title><?php echo TITLE ?? 'SYSTEM'; ?></title>
    <link rel="shortcut icon" href="images/map.svg" type="image/x-icon">
    <!-- link sa leaftlet para sa mapping -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Link sa bootstrap nga CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/lumen/bootstrap.min.css"
        integrity="sha384-GzaBcW6yPIfhF+6VpKMjxbTx6tvR/yRd/yJub90CqoIn2Tz4rRXlSpTFYMKHCifX" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/sandstone/bootstrap.min.css"
        integrity="sha384-zEpdAL7W11eTKeoBJK1g79kgl9qjP7g84KfK3AZsuonx38n8ad+f5ZgXtoSDxPOh" crossorigin="anonymous">

    <!-- link para mga icons nga css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Compiled and minified CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">

<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <!-- Link sa Jquery  -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script> -->

    <!-- Link sa bootstrap nga javascript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Link para sa leaflet para mapping -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Link para sa javascript nga font -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"
        integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Materialize Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        /* Custom header styling */
        nav {
            padding: 0 20px;
            background: linear-gradient(135deg, #2d5a27, #1b4332);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            font-size: 25px !important;
            font-weight: bold;
            padding: 0 !important;
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .brand-logo img {
            margin-right: 10px;
            width: 40px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        /* Right-aligned navigation */
        .nav-links {
            margin-left: auto !important;
            display: flex !important;
            height: 100%;
            gap: 8px;
        }

        .nav-links li a {
            height: 40px;
            padding: 0 20px !important;
            display: flex !important;
            align-items: center;
            border-radius: 20px;
            margin: 10px 0;
            background: #388e3c;
            transition: all 0.3s ease;
            color: white !important;
            position: relative;
            overflow: visible;
        }

        .nav-links li a:hover {
            background: #2e7d32;
        }

        .nav-links a.active {
            background: #388e3c;
        }

        .nav-links i {
            margin-right: 8px;
            font-size: 20px;
        }

        /* Logout button special styling */
        .nav-links li a.logout-btn {
            background: #d32f2f;  /* Solid red for logout */
        }

        .nav-links li a.logout-btn:hover {
            background: #b71c1c;  /* Darker red on hover */
        }

        /* Dropdown styling */
        .dropdown-content {
            border-radius: 8px;
            overflow: hidden;
            background: #388e3c !important;  /* Match the button color */
        }

        .dropdown-content li > a {
            color: white !important;
            padding: 16px 24px !important;
            transition: all 0.3s ease;
        }

        .dropdown-content li > a:hover {
            background: #2e7d32 !important;  /* Match the hover color */
        }

        /* Responsive adjustments */
        @media screen and (max-width: 992px) {
            .nav-links {
                display: none !important;
            }
        }

        /* Mobile sidenav styling */
        .sidenav {
            background: linear-gradient(135deg, #2d5a27, #1b4332);
        }

        .sidenav li > a {
            color: white !important;
            transition: all 0.3s ease;
        }

        .sidenav li > a:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 35px;
        }

        /* Custom animations for each icon */
        @keyframes homeAnimation {
            0% { transform: scale(1); }
            50% { transform: scale(1.2) rotate(10deg); }
            100% { transform: scale(1); }
        }

        @keyframes farmerAnimation {
            0% { transform: translateY(0); }
            50% { transform: translateY(-5px) rotate(15deg); }
            100% { transform: translateY(0); }
        }

        @keyframes farmAnimation {
            0% { transform: scale(1) rotate(0); }
            50% { transform: scale(1.2) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }

        @keyframes locationAnimation {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-5px) scale(1.3); }
            100% { transform: translateY(0) scale(1); }
        }

        @keyframes settingsAnimation {
            from { transform: rotate(0deg); }
            to { transform: rotate(180deg); }
        }

        @keyframes logoutAnimation {
            0% { transform: translateX(0); }
            50% { transform: translateX(5px); }
            100% { transform: translateX(0); }
        }

        /* Update the nav-links styles */
        .nav-links li a:hover i.material-icons {
            animation-duration: 0.6s;
            animation-fill-mode: both;
            animation-timing-function: ease-in-out;
        }

        /* Specific animations for each icon */
        .nav-links li a:hover i.material-icons:is([innerHTML="home"]) {
            animation-name: homeAnimation;
        }

        .nav-links li a:hover i.material-icons:is([innerHTML="agriculture"]) {
            animation-name: farmerAnimation;
        }

        .nav-links li a:hover i.material-icons:is([innerHTML="landscape"]) {
            animation-name: farmAnimation;
        }

        .nav-links li a:hover i.material-icons:is([innerHTML="place"]) {
            animation-name: locationAnimation;
        }

        .nav-links li a:hover i.material-icons:is([innerHTML="settings"]) {
            animation-name: settingsAnimation;
        }

        .nav-links li a:hover i.material-icons:is([innerHTML="logout"]) {
            animation-name: logoutAnimation;
        }

        /* Enhanced button hover effect */
        .nav-links li a {
            /* ... existing styles ... */
            transform-origin: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-links li a:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        /* Glowing effect for active button */
        .nav-links a.active {
            background: #4caf50;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
            animation: glowingEffect 2s infinite;
        }

        @keyframes glowingEffect {
            0% { box-shadow: 0 0 5px #4caf50; }
            50% { box-shadow: 0 0 20px #4caf50; }
            100% { box-shadow: 0 0 5px #4caf50; }
        }

        /* Enhanced ripple effect */
        .waves-effect.waves-light .waves-ripple {
            display: none !important;
        }

        /* Add new active state effect */
        .nav-links li a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: #4caf50;
            animation: activeGlow 2s infinite;
            transition: all 0.3s ease;
        }

        @keyframes activeGlow {
            0% { box-shadow: 0 0 5px #4caf50; opacity: 0.7; }
            50% { box-shadow: 0 0 20px #4caf50; opacity: 1; }
            100% { box-shadow: 0 0 5px #4caf50; opacity: 0.7; }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav>
        <div class="nav-wrapper">
            <!-- Logo -->
            <a href="#" class="brand-logo">
                <img src="images/map.svg" alt="Farm Mapping"> Farm Mapping
            </a>
            
            <!-- Mobile Menu Trigger -->
            <a href="#" data-target="mobile-menu" class="sidenav-trigger">
                <i class="material-icons">menu</i>
            </a>

            <!-- Right-aligned Navigation Links -->
            <ul class="nav-links hide-on-med-and-down">
                <li><a href="index.php" class="active">
                    <i class="material-icons">home</i>Map
                </a></li>
                <li><a href="farmers.php">
                    <i class="material-icons">agriculture</i>Farmers
                </a></li>
                <li><a href="lands.php">
                    <i class="material-icons">landscape</i>Farm List
                </a></li>
                <li><a href="#" class="dropdown-trigger" data-target="settingsDropdown">
                    <i class="material-icons">settings</i>Settings
                </a></li>
                <li><a href="#" onclick="confirmLogout()" class="logout-btn">
                    <i class="material-icons">logout</i>Logout
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- Dropdown Content -->
    <ul id="settingsDropdown" class="dropdown-content">
        <li><a href="crops.php">
            <i class="material-icons">eco</i>Crops
        </a></li>
    </ul>

    <!-- Sidenav for Mobile -->
    <ul class="sidenav" id="mobile-menu">
        <li><a href="index.php">Map</a></li>
        <li><a href="farmers.php">Farmers</a></li>
        <li><a href="lands.php">Farm List</a></li>
        <li><a href="#barangayModal" class="modal-trigger">Barangay Tagged</a></li>
        <li><a href="crops.php">Crops</a></li>
        <li><a href="#" onclick="confirmLogout()">Logout</a></li>
    </ul>

    <!-- Modal Structure -->
    <div id="barangayModal" class="modal">
        <div class="modal-content">
            <h4>Barangay Total Tagged</h4>
            <p>Details about barangay tagging can be displayed here.</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
        </div>
    </div>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Materialize Components
            M.Sidenav.init(document.querySelectorAll('.sidenav'));
            M.Dropdown.init(document.querySelectorAll('.dropdown-trigger'), { coverTrigger: false });
            M.Modal.init(document.querySelectorAll('.modal'));

            // Add click handlers for navigation buttons
            const navLinks = document.querySelectorAll('.nav-links li a:not(.logout-btn)');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't handle clicks for dropdown and modal triggers
                    if(this.classList.contains('dropdown-trigger') || 
                       this.classList.contains('modal-trigger')) {
                        return;
                    }

                    // Remove active class from all links
                    navLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');

                    // Store the active link in localStorage
                    if(this.getAttribute('href')) {
                        localStorage.setItem('activeLink', this.getAttribute('href'));
                    }
                });
            });

            // Set active state based on current page or stored state
            const currentPath = window.location.pathname;
            const storedActiveLink = localStorage.getItem('activeLink');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if(href && (
                    currentPath.includes(href) || 
                    (storedActiveLink && href.includes(storedActiveLink))
                )) {
                    // Remove active class from all links first
                    navLinks.forEach(l => l.classList.remove('active'));
                    // Add active class to current link
                    link.classList.add('active');
                }
            });
        });

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                localStorage.removeItem('activeLink'); // Clear stored active state
                window.location.href = "logout.php";
            }
        }
    </script>
</body>

    