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
            <h2 class="ccu_heading" style="text-align:left; margin-top: 10px">Quick Links</h2> <a class="btn btn-primary">Request Leave</a> <a href="add_training.php" class="btn btn-primary">Request Training</a>
            <button class="btn btn-primary" id="add_new_personnel_request">Request Personnel</button>
            <hr>
            <h2 class="ccu_heading" style="text-align:left;" id="trainings_label">Training Request</h2>
            
            <table id="training_request_table">
			<thead>
					<th>Training Title</th>
					<th>Request By</th>
					<th>Job - Department</th>
					<th>Schedule</th>
					<th>Cost</th>
					<th>Organization</th>
					<th>Venue</th>
					<th>Charging</th>
					<th>Status</th>
					<th>View Request</th>
			</thead>
			</table>
<br>
<br>          
<h2 class="ccu_heading" style="text-align:left;" id="emp_request_label">Employee Requisition </h2>
<table id="main_table">
			<thead>
					<th></th>
					<th></th>
					<th>Requesting Dept</th>
					<th>Job Title</th>
					<th>Request Date</th>
					<th>Mobilization Date</th>
					<th>Request Details</th>
					<th>Approvals</th>
					<th>HR Portion</th>
			</thead>
        </table>
        </div>

<div class="modal fade" id="get_request_details_modal" tabindex="-1" role="dialog" aria-labelledby="get_request_details_modal" style="overflow: auto">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Personnel Requisition Details</h4>
      </div>
      <div class="modal-body">
      <button class="btn btn-primary" id="request_details_add_btn">Add</button>
      <button class="btn btn-warning" id="request_details_edit_btn">Edit</button>
      <button class="btn btn-danger" id="request_details_delete_btn">Delete</button>
      <table id="request_details_table"  width="100%">
          <thead>
              <tr>
                  <th>Employment Type</th>
                  <th>Duration From</th>
                  <th>Duration Until</th>
                  <th>Replacement</th>
                  <th>Budget Included</th>
                  <th>Budget Clearance</th>
                  <th>Date</th>
              </tr>
          </thead>
      </table>
      <table id="request_details_table2"  width="100%">
        <thead>
            <tr>
                <th>Justification</th>
            </tr>
        </thead>
        </table>
          <table id="request_details_table3" width="100%">
              <thead>
                  <tr>
                      <th>Job Summary</th>
                  </tr>
              </thead>
          </table>
          <table id="request_details_table4" width="100%">
              <thead>
                  <tr>
                      <th>Qualifications and Other Requirements</th>
                  </tr>
              </thead>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
    </div>
    </div>

<div id="get_approvals_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="get_approvals_modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <div class="modal-title">
          Personnel Requisition Approvals
		  </div>
        </div>
        <div class="modal-body" style="height: 350px;">
          <table id="request_approval_table" width="80%">
              <thead>
                  <tr>
                      <th>Endoresed By</th>
                      <th>Recommending Approval</th>
                      <th>Approved By</th>
                  </tr>
              </thead>
          </table>
          
    </div>
    <div class="modal-footer">
        <?php
                showButtonIfExecutive();
            ?>
    </div>
  </div>
</div>
</div>
</div>
<script type="text/javascript" src="js/ccu_script.js"></script>
    </body>

    </html>