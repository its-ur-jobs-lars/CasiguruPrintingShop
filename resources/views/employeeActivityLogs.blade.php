
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title -->
    <title>Users</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{asset('assets/public/img/casiguroLogo.png')}}">


    <!-- DEMO CHARTS -->
    <link rel="stylesheet" href="{{asset('assets/public/demo/chartist.css')}}">
    <link rel="stylesheet" href="{{asset('assets/public/demo/chartist-plugin-tooltip.css')}}">

    <!-- Template -->
    <link rel="stylesheet" href="{{asset('assets/public/graindashboard/css/graindashboard.css')}}">

    {{-- <script src="https://kit.fontawesome.com/d04f2d2105.js" crossorigin="anonymous"></script> --}}

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
</head>

@livewireScripts
@livewireStyles

<style>
    .sidebar-image {
    width: 100%;
    height: 100vh;
    background: linear-gradient(to bottom,rgb(224, 214, 189), rgba(29, 152, 235, 0.7)),
                url("{{asset('assets/public/img/background.jpg')}}") no-repeat center center/cover ;
}

    .card-background-1 {
        /* background: url("{{asset('assets/public/img/c1.jpg')}}") no-repeat center center/cover; */
        border-radius: 10px;
        color: var(--gray);
        margin-top: 15px;
        margin-bottom: 10px;
        border: 1px solid rgb(243, 243, 243);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-background-2 {
        /* background: url("{{asset('assets/public/img/c2.jpg')}}") no-repeat center center/cover; */
        border-radius: 10px;
        color: var(--gray);
        margin-top: 15px;
        border: 1px solid rgb(243, 243, 243);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-background-3 {
        /* background: url("{{asset('assets/public/img/c3.jpg')}}") no-repeat center center/cover; */
        border-radius: 10px;
        color: var(--gray);
        margin-top: 15px;
        border: 1px solid rgb(243, 243, 243);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-background-4 {
        /* background: url("{{asset('assets/public/img/c4.jpg')}}") no-repeat center center/cover; */
        border-radius: 10px;
        color: var(--gray);
        margin-top: 15px;
        border: 1px solid rgb(243, 243, 243);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
</style>
       
<body class="has-sidebar has-fixed-sidebar-and-header">
<!-- Header -->
<header class="header bg-body"> 
    <nav class="navbar flex-nowrap p-0">
        <div class="navbar-brand-wrapper d-flex align-items-center col-auto">

            <!-- Logo For Mobile View -->
            <a class="navbar-brand navbar-brand-mobile" href="">
                <img class="img-fluid w-100" src="{{asset('assets/public/img/casiguroLogo.png')}}" alt="Graindashboard">
            </a>
            <!-- End Logo For Mobile View -->

            <!-- Logo For Desktop View -->
            <a class="navbar-brand navbar-brand-desktop" href="" >
                <img class="side-nav-show-on-closed" src="{{asset('assets/public/img/casiguroLogo.png')}}"  alt="Graindashboard" style="width: auto; height: 27px;">
                <img class="side-nav-hide-on-closed" src="{{asset('assets/public/img/casiguroLogo.png')}}" alt="Graindashboard" style="width: auto; height: 200px; margin: auto; margin-top: 40px; align-items: center;">
            </a>
            <!-- End Logo For Desktop View -->
        </div>

        <div class="header-content col px-md-3">
            <div class="d-flex align-items-center">
                <!-- Side Nav Toggle -->
                <a  class="js-side-nav header-invoker d-flex mr-md-2" href="#"
                    data-close-invoker="#sidebarClose"
                    data-target="#sidebar"
                    data-target-wrapper="body">
                    <i class="fas fa-sharp fa-solid fa-bars"></i>
                </a>
                
            

                 <!-- User Avatar -->
                 <div class="dropdown mx-3 dropdown ml-2">
                     <a id="profileMenuInvoker" class="header-complex-invoker" href="#" aria-controls="profileMenu" aria-haspopup="true" aria-expanded="false" data-unfold-event="click" data-unfold-target="#profileMenu" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-animation-in="fadeIn" data-unfold-animation-out="fadeOut">
                        <!--img class="avatar rounded-circle mr-md-2" src="#" alt="Jobelle Lariosa"-->
                        {{ Auth::user()->username }}
                        <span style="display:inline-block; vertical-align:middle; margin-left:7px;">
                            <i class="fas fa-circle" style="color: #28d160; font-size: 11px;" title="Active"></i>
                        </span>
                        <i class="fas fa-solid fa-caret-down d-none d-md-block ml-2"></i>
                    </a>

                    <ul id="profileMenu" class="unfold unfold-user unfold-light unfold-top unfold-centered position-absolute pt-2 pb-1 mt-4 unfold-css-animation unfold-hidden fadeOut" aria-labelledby="profileMenuInvoker" style="animation-duration: 300ms;">
                        <!-- <li class="unfold-item">
                            <a class="unfold-link d-flex align-items-center text-nowrap" href="#">
                                <span class="unfold-item-icon mr-2 d-flex align-items-center">
                                    <i class="fas fa-solid fa-user-tie"></i>
                                </span>
                                My Profile 
                            </a>
                        </li> -->
                        
                        <li class="unfold-item">
                            <a class="unfold-link d-flex align-items-center text-nowrap" href="/profile">
                                <span class="unfold-item-icon d-flex align-items-center">
                                    <i class="fas fa-solid fa-user mr-5"></i>
                                    <span style="font-size: 15px;">Profile</span>
                                </span>
                            </a>
                        </li>
                        <li class="unfold-item unfold-item-has-divider">
                        <a class="unfold-link d-flex align-items-center text-nowrap" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="unfold-item-icon d-flex align-items-center">
                                <i class="fas fa-solid fa-power-off mr-5"></i>
                                <span style="font-size: 15px;">Logout</span>
                            </span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                                    
                    </ul>
                </div>
                <!-- End User Avatar -->
            </div>
        </div>
    </nav>
</header>
<!-- End Header -->

<main class="main">
        {{-- <a href="/sample-form">Redirect</a> --}}
        <!-- Sidebar Nav -->
        <aside id="sidebar" class="js-custom-scroll side-nav">
            <ul id="sideNav" class="side-nav-menu side-nav-menu-top-level mb-0">
            <li class="sidebar-heading h6"></li>

        <!-- Dashboard -->
        <li class="side-nav-menu-item">
            <a class="side-nav-menu-link media align-items-center {{ Request::is('dashboard-homepage') ? : '' }}" href="/dashboard-homepage">
                <span class="side-nav-menu-icon d-flex mr-3">
                    <i class="fas fa-sharp fa-solid fa-square-poll-horizontal"></i>
                </span>
                <span class="side-nav-fadeout-on-closed media-body">Dashboard</span>
            </a>
        </li>
        <!-- End Dashboard -->
           
            <!-- Electronics -->
            <li class="side-nav-menu-item side-nav-has-menu">
                <a class="side-nav-menu-link media align-items-center" href="#"
                   data-target="#subElectronics">
                  <span class="side-nav-menu-icon d-flex mr-3">
                    <i class="fas fa-plug"></i>
                  </span>
                    <span class="side-nav-fadeout-on-closed media-body">Electronics</span>
                    <span class="side-nav-control-icon d-flex">
                        <i class="fas fa-solid fa-caret-right"></i>
              </span>
                    <span class="side-nav__indicator side-nav-fadeout-on-closed"></span>
                </a>

              <!-- Electronics: subElectronics -->
              <ul id="subElectronics" class="side-nav-menu side-nav-menu-second-level mb-0">
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/laptopSubcategories">
                        <i class="fas fa-solid fa-laptop"></i>Laptop and Accessories</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/AVRSubcategories">
                        <i class="fas fa-solid fa-bolt"></i>AVR</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/desktopUnitSubcategories">
                        <i class="fas fa-solid fa-desktop"></i>Desktop System Unit</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/externalHDSubcategories">
                        <i class="fas fa-solid fa-hard-drive"></i>External Hard Drive</a>
                    </li>
                 
                </ul>
                <!-- End Electronics: subElectronics -->
            </li>
            <!-- End Electronics -->
            
            <!-- Department -->
            <li class="side-nav-menu-item side-nav-has-menu">
                <a class="side-nav-menu-link media align-items-center" href="#"
                   data-target="#subDepartment">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="fas fa-building"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">Department</span>
                    <span class="side-nav-control-icon d-flex">
                        <i class="fas fa-solid fa-caret-right"></i>
              </span>
                    <span class="side-nav__indicator side-nav-fadeout-on-closed"></span>
                </a>

            <!-- Pages: subDepartment -->
             <ul id="subDepartment" class="side-nav-menu side-nav-menu-second-level mb-0">
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/auditDepartment">
                    <i class="fas fa-solid fa-magnifying-glass-dollar"></i>Audit</a>
                </li>
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/EHSDepartment">
                    <i class="fas fa-solid fa-shield"></i>Environmental Health <br> & Safety</a>
                </li>
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/financeDepartment">
                    <i class="fas fa-solid fa-coins"></i>Finance</a>
                </li>
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/hr&gsDepartment">
                    <i class="fas fa-solid fa-users"></i>Human Resource & <br> General Services</a>
                </li>
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/infotechDepartment">
                    <i class="fas fa-solid fa-microchip"></i>Information Technnology</a>
                </li>
               
            </ul>
            <!-- End Department: subDepartment -->
        </li>
        <!-- End Department -->
             
            <!-- History -->
            <li class="side-nav-menu-item side-nav-has-menu">
                <a class="side-nav-menu-link media align-items-center" href="#"
                   data-target="#subHistory">
              <span class="side-nav-menu-icon d-flex mr-3">
                    <i class="fas fa-solid fa-history"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">History</span>
                    <span class="side-nav-control-icon d-flex">
                        <i class="fas fa-solid fa-caret-right"></i>
              </span>
                    <span class="side-nav__indicator side-nav-fadeout-on-closed"></span>
                </a>

            <!-- History: subHistory -->
            <ul id="subHistory" class="side-nav-menu side-nav-menu-second-level mb-0">
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/goodconditionHistoryProduct">
                    <i class="fas fa-solid fa-circle-check"></i>Good Condition</a>
                </li>
                <li class="side-nav-menu-item">
                    <a class="side-nav-menu-link" href="/defectiveHistoryProduct">
                    <i class="fas fa-solid fa-circle-xmark"></i>Defective</a>
                </li>
                <!-- <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/deletedProducts">
                        <i class="fa-solid fa-trash-can-arrow-up"></i>Deleted Products</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="deletedProductsType">
                        <i class="fas fa-solid fa-square-minus"></i>Deleted Products Type</a>
                    </li> -->
            </ul>
            <!-- End History: subHistory -->
        </li>
        <!-- End History -->

             <!-- Manufacturer -->
             <li class="side-nav-menu-item side-nav-has-menu">
                <a class="side-nav-menu-link media align-items-center" href="#"
                   data-target="#subManufacturer">
              <span class="side-nav-menu-icon d-flex mr-3">
                <i class="fas fa-solid fa-bars-progress"></i>
              </span>
              <span class="side-nav-fadeout-on-closed media-body">Product Management</span>
                    <span class="side-nav-control-icon d-flex">
                        <i class="fas fa-solid fa-caret-right"></i>
              </span>
                    <span class="side-nav__indicator side-nav-fadeout-on-closed"></span>
                </a>

                  <!-- Manufacturer: subManufacturer-->
               <ul id="subManufacturer" class="side-nav-menu side-nav-menu-second-level mb-0">
                    
                  <li class="side-nav-menu-item">
                      <a class="side-nav-menu-link" href="/Manufacturer">
                       <i class="fas fa-industry"></i>Manufacturer</a>
                  </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/brandDivisionManufacturer">
                        <i class="fas fa-solid fa-tags"></i>Brand</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/modelDivisionManufacturer">
                        <i class="fas fa-cubes"></i> Model</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/specDivisionManufacturer">
                        <i class="fas fa-solid fa-clipboard-list"></i>Specification</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/productTypeDivisionManufacturer">
                        <i class="fas fa-solid fa-box-open"></i>Product Type</a>
                    </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/categoryDivisionManufacturer">
                        <i class="fas fa-solid fa-layer-group"></i>Category</a>
                    </li>
                </ul>
                <!-- End Manufacturer: subManufacturer -->
            </li>
            <!-- End Manufacturer -->
            
                <!-- Personnel -->
                <li class="side-nav-menu-item side-nav-has-menu active">
                <a class="side-nav-menu-link media align-items-center" href="#"
                   data-target="#subPersonnel">
              <span class="side-nav-menu-icon d-flex mr-3">
                    <i class="fas fa-sharp fa-solid fa-user-tie"></i>
              </span>
                    <span class="side-nav-fadeout-on-closed media-body">Personnel Profile</span>
                    <span class="side-nav-control-icon d-flex">
                        <i class="fas fa-solid fa-caret-right"></i>
              </span>
                    <span class="side-nav__indicator side-nav-fadeout-on-closed"></span>
                </a>

                <!-- Personnel: subPersonnel -->
                <ul id="subPersonnel" class="side-nav-menu side-nav-menu-second-level mb-0" style="display: block;">
                    <li class="side-nav-menu-item active">
                            <a class="side-nav-menu-link" href="/addUser">
                            <i class="fas fa-solid fa-user-plus"></i>Add User Accounts</a>
                        </li>
                    <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/personnelProductSearching">
                        <i class="fas fa-solid fa-magnifying-glass"></i>Product Searching</a>
                    </li>

                     <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/personnelDepartment">
                        <i class="fas fa-solid fa-building"></i>List of Department</a>
                    </li>

                     <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/personnel">
                        <i class="fas fa-solid fa-user-plus"></i>Personnel Information</a>
                    </li>

                     <li class="side-nav-menu-item">
                        <a class="side-nav-menu-link" href="/employeeActivityLogs">
                         <i class="fas fa-solid fa-chart-line"></i>Employee Activity Logs</a>
                    </li>
                </ul>
                <!-- End Personnel: subPersonnel -->
            </li>
          
       <!-- Dark Mode Toggle -->
       <div class="ml-3">
            <button id="darkModeToggle" class="btn-1 btn-sm-1 btn-outline-secondary">
                <i id="darkModeIcon" class="fas fa-moon"></i>
            </button>
        </div>
        <!-- End Dark Mode Toggle -->

    </ul>
</aside>
<!-- End Sidebar Nav -->
 
    <div class="content">
       <div class="py-4 px-3 px-md-4">

            <div class="mb-3 mb-md-4 d-flex justify-content-between">
                <div class="h3 mb-0">Add Users</div>
                <p id="datetime" class="mb-0"></p>
            </div>  

            <script>
                function updateDateTime() {
                    var now = new Date();
                    var date = now.toLocaleDateString();
                    var time = now.toLocaleTimeString();
                    document.getElementById('datetime').innerHTML = date + " " + time;
                }
                setInterval(updateDateTime, 1000);
            </script>

            <div class="row">
                <div class="col-12">
                    <div class="card mb-3 mb-md-4">
                        <div class="card-header">
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive-xl">
                                    @livewire('add-user')
                                   @livewire('add-user-table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="col-lg text-center fixed-bottom text-lg-right" style="margin-bottom: 10px;">
                    &copy; 2025 Sunwest Inc. All Rights Reserved.
            </div>
        </div>
        </footer>
        <!-- End Footer -->
</main>


        <script src="{{asset('assets/public/graindashboard/js/graindashboard.js')}}"></script>
        <script src="{{asset('assets/public/graindashboard/js/graindashboard.vendor.js')}}"></script>

        <!-- DEMO CHARTS -->
        <script src="{{asset('assets/public/demo/resizeSensor.js')}}"></script>
        <script src="{{asset('assets/public/demo/chartist.js')}}"></script>
        <script src="{{asset('assets/public/demo/chartist-plugin-tooltip.js')}}"></script>
        <script src="{{asset('assets/public/demo/gd.chartist-area.js')}}"></script>
        <script src="{{asset('assets/public/demo/gd.chartist-bar.js')}}"></script>
        <script src="{{asset('assets/public/demo/gd.chartist-donut.js')}}"></script>
        <script>
            $.GDCore.components.GDChartistArea.init('.js-area-chart');
            $.GDCore.components.GDChartistBar.init('.js-bar-chart');
            $.GDCore.components.GDChartistDonut.init('.js-donut-chart');
        </script>

        <!-- Dark Mode Script Function -->

        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const body = document.body;

            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                darkModeIcon.classList.remove('fa-moon');
                darkModeIcon.classList.add('fa-sun');
            }

            darkModeToggle.addEventListener('click', function () {
                if (body.classList.contains('dark-mode')) {
                    body.classList.remove('dark-mode');
                    localStorage.setItem('darkMode', 'disabled');
                    darkModeIcon.classList.remove('fa-sun');
                    darkModeIcon.classList.add('fa-moon');
                } else {
                    body.classList.add('dark-mode');
                    localStorage.setItem('darkMode', 'enabled');
                    darkModeIcon.classList.remove('fa-moon');
                    darkModeIcon.classList.add('fa-sun');
                }
            });
        });
        </script>

        <!-- Dashboard Graphs -->

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
            ['Category', 'Amount'],
            ['Laptop and Accessories', 8000],
            ['Mouse', 5000],
            ['Printer', 3000],
            ['Ups', 9000],
            ['Scanner', 4000],
            ['Keyboard', 2000],
            ['Server', 12000],   
            ['Monitor', 7000],
            ['Desktop System Unit', 9000],
            ['Scanner', 9000],
            ['Ip Phone', 9000],
            ['Signature Pad', 9000],
            ['External Hard-drive', 9000],
            ['AVR', 9000],
            ['Software License', 9000],
            ]);

            var isDarkMode = document.body.classList.contains('dark-mode');

            var options = {
            title: 'Inventory Stock Distribution',
            is3D: true,
            backgroundColor: 'transparent',
            legend: { textStyle: { color: isDarkMode ? 'white' : 'black' } },
            titleTextStyle: { color: isDarkMode ? 'white' : 'black' },
            hAxis: {
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            },
            vAxis: {
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            }
            };

            var chart = new google.visualization.PieChart(document.getElementById('inventoryPieChart'));
            chart.draw(data, options);
        }

            document.addEventListener('DOMContentLoaded', function () {
            const darkModeToggle = document.getElementById('darkModeToggle');
            darkModeToggle.addEventListener('click', function () {
            drawChart(); 
            });
        });

        Livewire.on('redrawChart', function (newData) {
            var data = google.visualization.arrayToDataTable(newData);
            var chart = new google.visualization.PieChart(document.getElementById('inventoryPieChart'));
            chart.draw(data, options);
        });
        </script>

     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawBarChart);

    function drawBarChart() {
        var data = google.visualization.arrayToDataTable([
            ['Department', 'Total Amount', { role: 'style' }],
            ['Audit', 8000, '#FF5733'], // Red
            ['Compliance&GPS', 5000, '#33FF57'], // Green
            ['Finance', 3000, '#3357FF'], // Blue
            ['HR&GS', 9000, '#FFC300'], // Yellow
            ['IT', 4000, '#FF33A1'], // Pink
            ['Marine', 2000, '#33FFF5'], // Cyan
            ['Operations', 2000, '#8E44AD'], // Purple
            ['Procurement', 2000, '#E67E22'], // Orange
            ['Resources', 2000, '#2ECC71'] // Light Green
        ]);

        var isDarkMode = document.body.classList.contains('dark-mode');

        var options = {
            title: 'Electronics Distribution',
            backgroundColor: 'transparent',
            titleTextStyle: { color: isDarkMode ? 'white' : 'black' },
            legend: { position: 'none' },
            hAxis: {
                title: 'Department',
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            },
            vAxis: {
                title: 'Total Amount',
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            },
            bar: { groupWidth: '75%' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('electronicsBarChart'));
        chart.draw(data, options);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle.addEventListener('click', function () {
            drawBarChart();
        });
    });
</script>
                
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawLineChart);

        function drawLineChart() {
            var data = google.visualization.arrayToDataTable([
            ['Month', 'Depreciation'],
            ['January', 12000],
            ['February', 11000],
            ['March', 10500],
            ['April', 10000],
            ['May', 9500],
            ['June', 9000],
            ['July', 8500],
            ['August', 8000],
            ['September', 7500],
            ['October', 7000],
            ['November', 6500],
            ['December', 6000]
            ]);

            var isDarkMode = document.body.classList.contains('dark-mode');

            var options = {
            title: 'Depreciation Over Time',
            curveType: 'function',
            legend: { position: 'bottom', textStyle: { color: isDarkMode ? 'white' : 'black' } },
            backgroundColor: 'transparent',
            titleTextStyle: { color: isDarkMode ? 'white' : 'black' },
            hAxis: {
                title: 'Months',
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            },
            vAxis: {     
                title: 'Depreciation (USD)',
                textStyle: { color: isDarkMode ? 'white' : 'black' },
                titleTextStyle: { color: isDarkMode ? 'white' : 'black' }
            },
            colors: ['#FF9D23']
            };

            var chart = new google.visualization.LineChart(document.getElementById('depreciationLineChart'));
            chart.draw(data, options);
        }

            document.addEventListener('DOMContentLoaded', function () {
            const darkModeToggle = document.getElementById('darkModeToggle');
            darkModeToggle.addEventListener('click', function () {
            drawLineChart();d
            });
        });
        </script>
        
    </body>
</html>