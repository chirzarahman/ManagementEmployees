<?php
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $address = $salary = $department = $position = $allowance = "";
$name_err = $address_err = $salary_err = $department_err = $position_err = $allowance_err = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Get hidden input value
    $id = $_POST["id"];

    // Validate name
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $name_err = "Please enter a valid name.";
    } else {
        $name = $input_name;
    }

    // Validate address address
    $input_address = trim($_POST["address"]);
    if (empty($input_address)) {
        $address_err = "Please enter an address.";
    } else {
        $address = $input_address;
    }

    // Validate salary
    $input_salary = trim($_POST["salary"]);
    if (empty($input_salary)) {
        $salary_err = "Please enter the salary amount.";
    } elseif (!ctype_digit($input_salary)) {
        $salary_err = "Please enter a positive integer value.";
    } else {
        $salary = $input_salary;
    }

    // validasi field department
    $input_department = trim($_POST["department"]);
    if (empty($input_department)) {
        $department_err = "Mohon masukkan sebuah department.";
    } else {
        $department = $input_department;
    }

    // validasi field position
    $input_position = trim($_POST["position"]);
    if (empty($input_position)) {
        $position_err = "Mohon masukkan sebuah position.";
    } else {
        $position = $input_position;
    }

    // validasi field allowance
    $input_allowance = trim($_POST["allowance"]);
    if (empty($input_allowance)) {
        $allowance_err = "Mohon masukkan sebuah allowance.";
    } else {
        $allowance = $input_allowance;
    }

    // Check input errors before inserting in database
    if (empty($name_err) && empty($address_err) && empty($salary_err) && empty($department_err) && empty($position_err) && empty($allowance_err)) {
        // Prepare an update statement
        $sql = "UPDATE employees SET name=?, address=?, salary=?, department=?, position=?, allowance=? WHERE id=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_name, $param_address, $param_salary, $param_department, $param_position, $param_allowance, $param_id);

            // Set parameters
            $param_name = $name;
            $param_address = $address;
            $param_salary = $salary;
            $param_department = $department;
            $param_position = $position;
            $param_allowance = $allowance;
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records updated successfully. Redirect to landing page
                header("location: table.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id =  trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM employees WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $name = $row["name"];
                    $address = $row["address"];
                    $salary = $row["salary"];
                    $department = $row["department"];
                    $position = $row["position"];
                    $allowance = $row["allowance"];
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($link);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PAS Admin - Edit Form Employe</title>

    <!-- Custom fonts for this template-->
    <link href="fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PAS Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Management Employees
            </div>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="table.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables Employees</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <?php
                        if (isset($_SESSION["username"])) {
                            echo '<li class="nav-item dropdown no-arrow">
                                    <a class="nav-link dropdown-toggle" href="login.php" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">' . $_SESSION["username"] . '</span>
                                    </a>
                                    <!-- Dropdown - User Information -->
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                    </a>
                                    </div>
                                </li>';
                        } else {
                            echo '<a href="login.php" class="my-auto">Login</a>';
                        }
                        ?>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Edit Employe</h1>
                    </div>

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <a href="table.php" class="m-0 font-weight-bold text-primary"><i class="fas fa-arrow-left"></i>
                                            Tables Employees</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <form method="POST" action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" enctype="multipart/form-data">
                                                <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputName">Name</label>
                                                    <input type="text" class="form-control" id="InputName" name="name" value="<?php echo $name; ?>">
                                                    <span class="help-block"><?php echo $name_err; ?></span>
                                                </div>
                                                <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputAddress">Address</label>
                                                    <textarea class="form-control" name="address" id="InputAddrress" rows="3"><?php echo $address; ?></textarea>
                                                    <span class="help-block"><?php echo $address_err; ?></span>
                                                </div>
                                                <div class="form-group <?php echo (!empty($salary_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputSalary">Salary</label>
                                                    <input class="form-control" name="salary" id="InputSalary" value="<?php echo $salary; ?>">
                                                    <span class="help-block"><?php echo $salary_err; ?></span>
                                                </div>
                                                <div class="form-group <?php echo (!empty($department_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputDepartment">Department</label>
                                                    <input class="form-control" id="InputDepartment" name="department" value="<?php echo $department; ?>">
                                                    <span class="help-block"><?php echo $department_err; ?></span>
                                                </div>
                                                <div class="form-group <?php echo (!empty($position_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputPosition">Position</label>
                                                    <input class="form-control" id="InputPosition" name="position" value="<?php echo $position; ?>">
                                                    <span class="help-block"><?php echo $position_err; ?></span>
                                                </div>
                                                <div class="form-group <?php echo (!empty($allowance_err)) ? 'has-error' : ''; ?>">
                                                    <label for="InputAllowance ">Allowance</label>
                                                    <input class="form-control" id="InputAllowance" name="allowance" value="<?php echo $allowance; ?>">
                                                    <span class="help-block"><?php echo $allowance_err; ?></span>
                                                </div>

                                                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                                <input type="submit" class="btn btn-primary" value="Submit">

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PAS - Sistem Manajemen Karyawan Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

</body>

<!-- Bootstrap core JavaScript-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</html>