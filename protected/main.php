<?php
//import database configuration file
include 'config.php';
//starting of session for data storage purpose
session_start();

//check session storage if there is logged_user and user_type values
if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type']))
{
    
    //this is for personnel_request only!
    if($_SESSION['user_type'] == 'EMPLOYEE')
    {
        die('Sorry! You dont have an access to this module');
    }
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
while($l_statement->fetch()){}

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
        <link rel="stylesheet" href="../train_dev/css/ccu_stylesheet.css"> </head>
        <script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
        
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        
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

<body>
       <?php include 'add_personnel_request.php'; include 'add_request_details.php'; include 'edit_personnel_request.php' ?>
        <div class="ccu_sidenav">
            <div class="ccu_brand">woodfields consultants, inc.</div> <img src="images/profile.jpg" alt="" class="ccu_profile img-circle">
            <h3 class="ccu_title"><?php echo $lastname." ".$firstname ?></h3>
            <h4 class="ccu_title_second" style="margin-bottom: 0"><?php echo $jobDesc ?></h4>
            <h4 class="ccu_title_second"><?php echo $deptdesc ?></h4> <a href="./dashboard.php">Home</a> <a href="#">Training</a> <a href="#">Leave</a> <a href="logout.php">Logout</a> </div>
        <div class="ccu_main">
            <h2 class="ccu_heading" style="text-align:left; margin-top: 10px">Quick Links</h2> <a class="btn btn-primary">Request Leave</a> <a href="add_training.php" class="btn btn-primary">Request Training</a> <a class="btn btn-primary">Employment Status Report</a> <button class="btn btn-primary" id="add_new_personnel_request">Request Personnel</button>
            <hr>
            <h2 class="ccu_heading" style="text-align:left;">Notifications</h2> 
            
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

<script>
var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
var year = currentTime.getFullYear();
var curr_date = year + "-" + month + "-" + day;
$(".form_datetime").datetimepicker({
    format: "yyyy-mm-dd",
    todayBtn: true,
    startDate: curr_date + " 00:00",
    minuteStep: 20,
    autoclose: true,
    showMeridian: true
});

var gastos_table,tbMain, approved_table;
    
$(document).ready(function(){
    var rs = [];
    var id =null;
    tbMain = $('#main_table').DataTable({
			"bDestroy": true,
			"searching": false,
            "bPaginate": false,
			"ordering": false,
			"bLengthChange": true,
            "bInfo": false,
			"select": false,
			"pageLength": 100,
            "responsive" : true,
			"ajax": { "url": "engine.php",
					  "data": { "function": "getAllPersonnelRequest","extra": "" },
					  "type": "GET"
					},
			"columns": [
				{
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<button id='deleteBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-trash'></span></button>"
				},
				{
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<button id='editBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-pencil'></span></button>"
				},
				{ "data" : "s_deptdesc" },
				{ "data" : "s_posidesc" },
                { "data" : "request_date" },
				{ "data" : "d_mobiDate" },
                {
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<button id='reqDetailBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-th'></span></button>"
				},
                {
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<button id='approvalsBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-user'></span></button>"
				}
			]
		});

$.ajax({
    url: "engine.php",
    data: {function: "getAllPosition", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#job_title").append($('<option>', { 
                value: obj.data[i].n_posinmbr,
                text: obj.data[i].s_posidesc
            }));
        }
    }
});
    
$.ajax({
    url: "engine.php",
    data: {function: "getAllDepartment", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#req_dept").append($('<option>', { 
                value: obj.data[i].n_deptnmbr,
                text: obj.data[i].s_deptdesc
            }));
        }
    }
});
    
$.ajax({
    url: "engine.php",
    data: {function: "getAllPosition", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#n_posnumbr").append($('<option>', { 
                value: obj.data[i].n_posinmbr,
                text: obj.data[i].s_posidesc
            }));
        }
    }
});
    
$.ajax({
    url: "engine.php",
    data: {function: "getAllDepartment", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#n_deptnmbr").append($('<option>', { 
                value: obj.data[i].n_deptnmbr,
                text: obj.data[i].s_deptdesc
            }));
        }
    }
});


//---- button clicked in datatable ------
$('#main_table tbody').on( 'click', 'button', function () {
			rs = tbMain.row( $(this).parents('tr') ).data();
            switch(this.id){
                case 'deleteBtn_Main':
                    swal({
                        title: "Are you sure you want to delete this request?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Okay, I understand",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                         function(isConfirm){
                        if (isConfirm) {                           
                            $.ajax({
                                type : 'GET',
                                url : 'engine.php',
                                data : {'function' : "deleteRequest", 'extra' : rs['n_requestId']},
                                success : function(data){
                                        swal("Deleted!", "Request has been deleted", "success");
                                        $("#main_table").DataTable().ajax.reload();
                                }
                            });
                        } else {
                            swal("Cancelled", "Your imaginary file is safe :)", "error");
                        }
                    });
                    break;
                case 'editBtn_Main' :
                        $('#edit_personnel_request_modal').appendTo("body").modal('show');
                        editPersonnelRequestForm(rs['n_requestId']);
                    
                    break;
                case 'approvalsBtn_Main':
                    $('#get_approvals_modal').modal('show');
                    getApprovals(rs['n_requestId']);
                    break;
                case 'reqDetailBtn_Main':
                    $('#get_request_details_modal').modal('show');
                    getRequestDetailsFunction(rs['n_requestId']);
                    break;
            }
			
});


$('#get_request_details_modal').on('shown.bs.modal',function(){
        $(this).find('.modal-dialog').css({width:'80%', height:'auto', 'max-height':'100%'});
});
$('#get_approvals_modal').on('shown.bs.modal',function(){
        $(this).find('.modal-dialog').css({width:'80%', height:'auto', 'max-height':'100%'});
});
    
$('#approve_request').click(function(e){
    e.preventDefault();
    var sess_token, sess_user;
    $.ajax({
    url: "engine.php",
    data: {function: "getSessionStored", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
         sess_token = obj[0].user_type.toString();
         sess_user = obj[1].logged_user.toString();
        
            if(sess_token === "TOP_MNGT"){
        swal({
            title: "Are you sure?",
            text: "You want to perform the action?",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Okay",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    type : 'GET',
                    url : 'engine.php',
                    data : {"function" : "approvePersonnelRequest", "extra" : sess_token+" " +sess_user + " "+rs['n_requestId']},
                    success : function(data){
                        swal("Success", "Personnel request has been approved! Wait for other signatories", "success");
                        $("#request_approval_table").DataTable().ajax.reload();
                    }
                });
                
            } else {
                swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
});
    }
    else{
        swal("Error", "You dont have an access to this module yet", "error");
    }
        
    }
});


});
$('#add_new_personnel_request').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_modal').appendTo("body").modal('show');
});
$('#request_details_add_btn').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_details_modal').appendTo("body").modal('show');
    sessionStorage.setItem("request_id",rs['n_requestId']);
    clear_data();
});
$('#request_details_delete_btn').click(function(e){
    e.preventDefault();
    swal({
        title: "Are you sure you want to delete this request?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Okay, I understand",
        closeOnConfirm: false,
        closeOnCancel: false
    },
         function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    type : 'GET',
                    url : 'engine.php',
                    data : {'function' : "deleteRequestDetails", 'extra' : rs['n_requestId']},
                    success : function(data){
                        swal("Deleted!", "Request details has been deleted", "success");
                        $("#get_request_details_modal").modal("hide");
                    }
                });
            }
            else{
                swal("Cancelled","Operation has been cancelled. Data is safe!","success");
            }
    });
    
});  
$('#request_details_edit_btn').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_details_modal').appendTo("body").modal('show');
    sessionStorage.setItem("request_id",rs['n_requestId']);
    editPersonnelRequest(rs['n_requestId']);
});
    
    

    
}); // End of document.ready
    
    
function getRequestDetailsFunction(id){
    request_details_table = $('#request_details_table').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "GET",
            "error": function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "employment_type"},
            {"data"	: "emp_dur_from"},
            {"data" : "emp_dur_to"},
            {"data" : "replaced_by"},
            {"data" : "isBudget"},
            {"data" : "budget_clearance"},
            {"data" : "budget_clearance_date"}
        ]
    });
    $('#request_details_table2').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "GET",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "justification"}
        ]
    });
    $('#request_details_table3').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "GET",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "job_summary"}
        ]
    });
    $('#request_details_table4').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "GET",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "qual_other_req"}
        ]
    });
}
    
function getApprovals(id){
    approved_table = $('#request_approval_table').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getApproval', 'extra' : id},
			"type": "GET"
			},
        "columns": [
            {"data" : "isApproveAdmin"},
            {"data"	: "isApproveEVP"},
            {"data" : "isApproveCEO"}
        ]
    });
}


</script>

</body>
</html>