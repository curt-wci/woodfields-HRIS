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
         
         
         <!-- DateTime Picker Core -->
         <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
         <script src="js/bootstrap-datetimepicker.min.js"></script>
    </head>

    <body>
        <div style="margin-top: 10px; margin-left: 20px; margin-right: 20px">
            <h2 class="ccu_heading" style="text-align:center;" id="trainings_label">Personnel Requisition Report</h2>
            <hr>
            <table id="personnel_request_table">
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
			</thead>
			</table>

        </div>
    </body>
    
    <script>
        $('#personnel_request_table').DataTable({
            "bDestroy": true,
            "pageLength": 50,
            "responsive" : true,
            "ajax": { "url": "engine.php",
					  "data": { "function": "getPersonnelRequest_Report","extra": "" },
					  "type": "POST"
					},
			"columns": [
				{ "data" : "s_deptdesc" },
                { "data" : "s_posidesc" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     
                     return date_parser(mySource.request_date);
                 } },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     
                     return date_parser(mySource.d_mobiDate);
                 } },
                { "data" : "employment_type" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                 return date_parser(mySource.emp_dur_from)+" - "+date_parser(mySource.emp_dur_to);
                 } },
                { "data" : "replaced_by" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     var content = mySource.isBudget + "<br>"+
                         checkNull(mySource.budget_clearance)+"<br>"+
                         checkNull(mySource.budget_clearance_date)+"<br>";
                     
                     return content;
                 } },
                { "data" : "justification" },
                { "data" : "job_summary" },
                { "data" : "qual_other_req" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     //mySource.isApproveAdmin + "<br>" + mySource.isApproveEVP + "<br>" + mySource.isApproveCEO
                     var admin = checkNull(mySource.isApproveAdmin);
                     var coo = checkNull(mySource.isApproveEVP);
                     var ceo = checkNull(mySource.isApproveCEO);
                     
                     return "<center>"+admin + " " + coo + " " + ceo + "</center>";
                 } },
			]
		});
        
        
    function checkNull(data){
        if(data == "" || data === undefined || data == null || data == "null"){
            return "";
        }
        else
            return '<span class="glyphicon glyphicon-check"></span>';
    }
        
    function date_parser(date_string){
    if(date_string == "" || date_string === undefined || date_string == null)
        return "";
    else{
        var monthIndex = date_string.slice(6,7);
    var year = date_string.slice(0,4);
    var day = date_string.slice(8,10);
    
    var monthNames = [' ','January','February','March','April','May','June','July','August','September','October','November','December'];
    
    return monthNames[monthIndex]+" "+day+", "+year;
    }
    }
    </script>

    </html>