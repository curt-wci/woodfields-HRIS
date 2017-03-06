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

//check if the submit button is fired up
    if(isset($_POST['submit_request']))
    { 
        //get the data from filled form. htmlentities() is used to convert all the special characters into HTML values TL;DR
         $l_train_title = htmlentities($_POST['training_title']);
         $l_train_org = htmlentities($_POST['training_org']);
         $l_train_date_from = $_POST['training_date-from'];
         $l_train_date_until = $_POST['training_date-until'];
         $l_train_venue = htmlentities($_POST['training_location']);
         $l_train_cost = htmlentities($_POST['training_cost']);
         $l_train_charging = htmlentities($_POST['training_charging']);
         $l_train_justify = htmlentities($_POST['train_justify']);
        
        //aside form htmlentities() another form of string serialization is mysqli_real_escape_string which escapes all special chars in string
        $l_train_title = mysqli_real_escape_string($conn,$l_train_title);
        $l_train_org = mysqli_real_escape_string($conn,$l_train_org);
        $l_train_venue = mysqli_real_escape_string($conn,$l_train_venue);
        $l_train_cost = mysqli_real_escape_string($conn,$l_train_cost);
        $l_train_charging = mysqli_real_escape_string($conn,$l_train_charging);
        $l_train_justify = mysqli_real_escape_string($conn,$l_train_justify);
        
        //initialize date
        $l_date = new DateTime();
        //get the logged user data from session
        $l_reqEmp = $_SESSION['logged_user'];
        $l_empJob = $jobCode;
        $l_empDept = $deptno;
        $l_status = "PENDING";
        $l_stamp = "ADDED BY ".$lastname.", ".$firstname." ".$l_date->getTimestamp().";";
        
        //prepare an SQL statement
        $l_statement = $conn->prepare("INSERT INTO `trainings` (`s_trntitle`, `s_trainorg`, `d_strtdate`, `d_end_date`, `s_location`, `n_traincst`, `n_charging`, `s_justify`, `n_reqempid`, `n_reqprjcd`, `n_reqdptcd`, `s_reqstats`, `s_tblstmps`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        //bind all the parameters to be inserted
        $l_statement->bind_param("sssssiisiiiss",$l_train_title,$l_train_org,$l_train_date_from,$l_train_date_until,$l_train_venue,$l_train_cost,$l_train_charging,$l_train_justify,$l_reqEmp,$l_empJob,$l_empDept,$l_status,$l_stamp);
        //execute the statement
        $l_statement->execute() or die ($l_statement->error);
        //data inserted redirect to dashboard
        echo "<script>alert('Data Inserted! Pending for Supervisor approval')</script>";
        header("Refresh: 2; url=dashboard.php");
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
        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="dist/sweetalert.css">
        <link rel="stylesheet" href="css/ccu_stylesheet.css"> </head>

    <body>
        <div class="ccu_sidenav">
            <div class="ccu_brand">woodfields consultants, inc.</div> <img src="images/profile.jpg" alt="" class="ccu_profile img-circle">
            <h3 class="ccu_title"><?php echo $lastname." ".$firstname ?></h3>
            <h4 class="ccu_title_second" style="margin-bottom: 0"><?php echo $jobDesc ?></h4>
            <h4 class="ccu_title_second"><?php echo $deptdesc ?></h4> <a href="./dashboard.php">Home</a> <a href="#">Training</a> <a href="#">Leave</a> <a href="logout.php">Logout</a> </div>
        <div class="ccu_main">
            <h2 class="ccu_heading">training nomination form (TNF)</h2>
            <hr>
            <form method="POST" action="<?php $_SERVER['PHP_SELF']?>">
                <div class="form-group">
                    <div class="col-md-12" style="margin-bottom:10px">
                        <div class="col-md-6">
                            <label for="training_title">Training Title:</label>
                            <input type="text" class="form-control" id="training_title" name="training_title" required> </div>
                        <div class="col-md-6">
                            <label for="training_org">Training Organization:</label>
                            <input type="text" class="form-control" id="training_org" name="training_org" required> </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="training_sched">Training Schedule:</label>
                    <div class="col-md-12" style="margin-bottom:10px">
                        <div class="col-md-6">
                            <div class="input-append date form_datetime">
                                <input size="16" type="text" value="" readonly class="form-control" placeholder="From" name="training_date-from" required> <span class="add-on"><i class="icon-th"></i></span> </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-append date form_datetime">
                                <input size="16" type="text" value="" readonly class="form-control" placeholder="Until" name="training_date-until" required> <span class="add-on"><i class="icon-th"></i></span> </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="training_venue">Training Venue/Location:</label>
                    <input type="text" id="training_venue" class="form-control" name="training_location" required> </div>
                <div class="form-group">
                    <label for="training_cost">Training Cost:</label>
                    <input type="text" id="training_cost" class="form-control" name="training_cost" required> </div>
                <div class="form-group">
                    <label for="training_charging">Charging:</label>
                    <input type="text" id="training_charging" class="form-control" name="training_charging" required> </div>
                <div class="form-group">
                    <label for="training_justification">
                        <h4 style="font-weight: 600">JUSTIFICATION FOR THE REQUEST:</h4></label>
                    <textarea name="train_justify" id="training_justification" rows="5" class="form-control" style="resize: none" required></textarea>
                </div>
                <hr>
                <input type="submit" class="btn btn-block btn-primary" name="submit_request" value="Send Request"> </form>
        </div>
        <!-- jQuery Version 1.11.1 -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <!-- Bootstrap date/time picker core-->
        <script src="js/bootstrap-datetimepicker.min.js"></script>
        <script>
            var currentTime = new Date();
            var month = currentTime.getMonth() + 1;
            var day = currentTime.getDate();
            var year = currentTime.getFullYear();
            //get the current date today
            var curr_date = year + "-" + month + "-" + day;
            $(".form_datetime").datetimepicker({
                format: "yyyy-mm-dd"
                , todayBtn: true
                , startDate: curr_date + " 00:00"
                , minuteStep: 20
                , autoclose: true
                , showMeridian: true
            , });
        </script>
    </body>

    </html>