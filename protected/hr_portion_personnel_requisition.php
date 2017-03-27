<?php
//import database configuration file
include 'config.php';
//starting of session for data storage purpose
session_start();

//check session storage if there is logged_user and user_type values
if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type']))
{ //do nothing 
}
else
{
    //if no session data for logged_user and user_type redirect to login page
    header("Location: index.php");
}

//prepare an SQL statement
$l_statement = $conn->prepare("SELECT
     employeesx.`s_lastname`, employeesx.`s_frstname`, employeesx.`n_contnmbr`,
     department.`n_deptnmbr`, department.`s_deptdesc`,
     positionxx.`n_posinmbr`, positionxx.`s_posidesc`
     FROM
     `employeesx` employeesx INNER JOIN `empjobhist` empjobhist ON employeesx.`n_contnmbr` = empjobhist.`n_contnmbr`
     INNER JOIN `department` department ON empjobhist.`n_deptnmbr` = department.`n_deptnmbr`
     INNER JOIN `positionxx` positionxx ON empjobhist.`n_posinmbr` = positionxx.`n_posinmbr` WHERE hris.employeesx.n_contnmbr = ? ORDER BY empjobhist.`d_hiredate` DESC LIMIT 0,1");
//bind the variables
    $l_statement->bind_param('s',$_SESSION['logged_user']) or die ($l_statement->error);
//execute the statement
    $l_statement->execute();
//bind the result to output
    $l_statement->bind_result($lastname,$firstname,$count_no,$deptno,$deptdesc,$jobCode,$jobDesc);
    
//loop through the result and output it on the page
     while($l_statement->fetch())
    {
        
    }
    $_SESSION['deptCode'] = $deptno;
    $_SESSION['jobCode'] = $jobCode;

?>
<?php
function showButtonIfExecutive(){
    if($_SESSION['user_type'] == "TOP_MNGT" || $_SESSION['user_type'] == "HR"){
        echo '<button id="approve_request" class="btn btn-primary">Approve Request</button>';
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Dashboard</title>
        <!-- JQuery Core -->
        <script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
        <!-- Bootstrap Core -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <!-- Custom CSS -->
        <link rel="stylesheet" href="css/ccu_stylesheet.css">
         <!--datatables core -->
         <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.13/datatables.min.css"/>
         <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.13/datatables.min.js"></script>
         
         <!-- Sweetalert Library -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
         
         <!-- File Upload core -->
         <script src="js/file-input_plugins/canvas-to-blob.min.js"></script>
         <script src="js/file-input_plugins/sortable.min.js"></script>
         <script src="js/file-input_plugins/purify.min.js"></script>
         <script src="js/fileinput.min.js"></script>
         <script src="js/file-input_themes/fa/theme.js"></script>
         
         <!-- DateTime Picker Core -->
         <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
         <script src="js/bootstrap-datetimepicker.min.js"></script>
    </head>

    <body>
       <?php include 'add_personnel_request.php'; include 'add_request_details.php'; include 'edit_personnel_request.php'; include 'hr_portion_personnel_request.php' ?>
        <div class="ccu_sidenav">
            <div class="ccu_brand">woodfields consultants, inc.</div> <img src="images/profile.jpg" alt="" class="ccu_profile img-circle">
            <h3 class="ccu_title"><?php echo $lastname." ".$firstname ?></h3>
            <h4 class="ccu_title_second" style="margin-bottom: 0"><?php echo $jobDesc ?></h4>
            <h4 class="ccu_title_second"><?php echo $deptdesc ?></h4> <a href="./dashboard.php">Home</a> <a href="#">Training</a> <a href="#">Leave</a> <a href="logout.php">Logout</a> </div>
        <div class="ccu_main">
            <h2 class="ccu_heading">Employee Requisition - HR Portion </h2>
            <div class="table-responsive">
                <table id="HR_PORTION_table" class="table">
                    <thead>
                    <th>Requesting Dept</th>
                    <th>Job Title</th>
                    <th>Request Date</th>
                    <th>Mobilization Date</th>
                    <th>Employment Type</th>
                    <th>Employment Duration</th>
                    <th>Replacement</th>
                    <th>Budgetary Details</th>
                    <th>Justification</th>
                    <th>Job Summary</th>
                    <th>Qualification and Others</th>
                    <th>Approvals</th>
                    <th>HR Portion</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
    <script src="js/ccu_script.js"></script>
    </body>
</html>