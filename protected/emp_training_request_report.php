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
            <h2 class="ccu_heading" style="text-align:center;" id="trainings_label">Training Nomination Report</h2>
            <hr>
            <table id="training_request_report_table">
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
			</thead>
			</table>

        </div>
    </body>
    
    <script>
        $('#training_request_report_table').DataTable({
            "bDestroy": true,
            "pageLength": 50,
            "responsive" : true,
            "ajax": {
			"url": "engine.php",
			"data": {'function': 'getEmpTrainingRequest_Report', 'extra' : ''},
			"type": "GET",
            "error" : function(){
                swal("Invalid Data","No data found on employee training request.","error");
                $('#training_request_table').hide();
                $('#trainings_label').hide();
            }
			},
        "columns": [
            {"data" : "s_trntitle"},
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return mySource.s_lastname+" "+mySource.s_frstname;
             } },
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return mySource.s_posidesc+"<br>"+mySource.s_deptdesc;
             } },
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return date_parser(mySource.d_strtdate)+"<br>"+date_parser(mySource.d_end_date);
             } },
            {"data" : "n_traincst"},
            {"data" : "s_trainorg"},
            {"data" : "s_location"},
            {"data" : "n_charging"},
            {"data" : "s_reqstats"}
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