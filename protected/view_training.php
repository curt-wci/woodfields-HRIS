<?php
//check if tr_name is present on the URL
    if(isset($_GET['tr_name'])) 
    {
        //import database configuration file
        include ("config.php");
        //starting of session for data storage purpose
        session_start();
        //check session storage if there is logged_user and user_type values
        if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type'])){}
        else
            header("Location: index.php");
    }
//if not, produce an error
    else
        die("External request are invalid. Please use the front-end");

//prepare an SQL statement
    $l_statement = $conn->prepare("SELECT
     employeesx.`s_lastname`, employeesx.`s_frstname`, employeesx.`n_contnmbr`,
     department.`n_deptnmbr`, department.`s_deptdesc`,
     positionxx.`n_posinmbr`, positionxx.`s_posidesc`
     FROM
     `employeesx` employeesx INNER JOIN `empjobhist` empjobhist ON employeesx.`n_contnmbr` = empjobhist.`n_contnmbr`
     INNER JOIN `department` department ON empjobhist.`n_deptnmbr` = department.`n_deptnmbr`
     INNER JOIN `positionxx` positionxx ON empjobhist.`n_posinmbr` = positionxx.`n_posinmbr` WHERE hris.employeesx.n_contnmbr = ? ORDER BY empjobhist.`d_hiredate` DESC LIMIT 0,1");
//bind parameters logged_user
    $l_statement->bind_param('s',$_SESSION['logged_user']) or die ($l_statement->error);
//execute statement
    $l_statement->execute();
//bind results to output
    $l_statement->bind_result($lastname,$firstname,$count_no,$deptno,$deptdesc,$jobCode,$jobDesc);

     while($l_statement->fetch()){}

//prepare an SQL statement 
    $l_statement = $conn->prepare("SELECT
     department.`s_deptdesc`,
     employeesx.`s_lastname`, employeesx.`s_frstname`,
     positionxx.`s_posidesc`,
     trainings.`s_trntitle`, trainings.`s_trainorg`, trainings.`d_strtdate`, trainings.`d_end_date`, trainings.`s_location`, trainings.`n_traincst`, trainings.`n_charging`, trainings.`s_justify`, trainings.`s_reqstats`, trainings.`s_tblstmps`
     FROM
     `employeesx` employeesx INNER JOIN `trainings` trainings ON employeesx.`n_contnmbr` = trainings.`n_reqempid`
     INNER JOIN `positionxx` positionxx ON trainings.`n_reqprjcd` = positionxx.`n_posinmbr`
     INNER JOIN `department` department ON trainings.`n_reqdptcd` = department.`n_deptnmbr`
WHERE
    trainings.`s_trntitle` = ? LIMIT 0,1");
//bind parameters
    $l_statement->bind_param('s',$_GET['tr_name']) or die ($l_statement->error);
//execute statement
    $l_statement->execute();
//bind results
    $l_statement->bind_result($tr_deptDesc,$tr_lname,$tr_fname,$tr_jobDesc,$tr_title,$tr_org,$tr_strt_dt,$tr_end_dt,$tr_loc,$tr_cost,$tr_charge,$tr_justify,$tr_status,$tr_tblstmp);
//loop to display
     while($l_statement->fetch())
    {
       
    }
//check if approve_form button was fired
if(isset($_POST['approve_form']))
{
    //initiate DateTime object
    $l_date = new DateTime();
    $curr_stmp = $tr_tblstmp." APPROVE BY ".$lastname.", ".$firstname." ".$l_date->getTimestamp().";";
    //statement builder
    $stmt = "UPDATE `trainings` SET `s_tblstmps` = ? WHERE `trainings`.`s_trntitle` = ?";
    //prepare the statement
    $l_statement = $conn->prepare($stmt);
    //bind parameters
    $l_statement->bind_param("ss",$curr_stmp,$tr_title);
    //execute statement
    $l_statement->execute();
    
    if($_SESSION['logged_user'] == "138")
    {
       header("Location:emp_adjust.php?tr_name=".$tr_title);
        
    }
    else{
        header("Refresh: 2; url=dashboard.php");
    }
    
    
}
else if(isset($_POST['deny_form']))
{
    $l_date = new DateTime();
    $l_deny = "DENY";
    $curr_stmp = $tr_tblstmp." DENIED BY ".$lastname.", ".$firstname." ".$l_date->getTimestamp().";";
    
    $stmt = "UPDATE `trainings` SET `s_tblstmps` = ?, `s_reqstats` = ? WHERE `trainings`.`s_trntitle` = ?";
    
    $l_statement = $conn->prepare($stmt);
    $l_statement->bind_param("sss",$curr_stmp,$l_deny,$tr_title);
    $l_statement->execute();
    header("Refresh: 2; url=dashboard.php");
}
else if(isset($_POST['cancel_form']))
{
    $l_date = new DateTime();
    $l_deny = "CANCEL";
    $curr_stmp = $tr_tblstmp." CANCELLED BY ".$lastname.", ".$firstname." ".$l_date->getTimestamp().";";
    
    $stmt = "UPDATE `trainings` SET `s_tblstmps` = ?, `s_reqstats` = ? WHERE `trainings`.`s_trntitle` = ?";
    
    $l_statement = $conn->prepare($stmt);
    $l_statement->bind_param("sss",$curr_stmp,$l_deny,$tr_title);
    $l_statement->execute();
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
            <?php
            if($tr_status == 'PENDING')
            {
                echo '<div class="alert alert-info">
                    <strong>Info!</strong> Request still pending and waiting for approval.
                    </div>';
            }
            else if($tr_status == "APPROVE")
            {
                echo '<div class="alert alert-success">
                    <strong>Success!</strong> Request Approved!
                    </div>';
            }
            else if($tr_status == "CANCEL")
            {
                echo '<div class="alert alert-warning">
                    <strong>Warning!</strong> Request has been cancelled!
                    </div>';
            }
            else
            {
                echo '<div class="alert alert-danger">
                    <strong>Alert!</strong> Request is denied!
                    </div>';
            }
            ?>
                <h2 class="ccu_heading">Training Information (Detailed View)</h2>
                <hr>
                <h4 style="font-weight: 600">Training Request by:</h4>
                <table class="table table-striped">
                <tr>
                <thead>
                    <tr>
                        <th style="width:30%">Employee Name</th>
                        <th style="width:30%">Position</th>
                        <th style="width:30%">Department/Section</th>
                    </tr>
                </thead>
                <tr>
                    <td><?php echo $tr_lname." ".$tr_fname?></td>
                    <td><?php echo $tr_jobDesc?></td>
                    <td><?php echo $tr_deptDesc?></td>
                </tr>
                </tr>
                </table>
                <form method="POST" action="<?php $_SERVER['PHP_SELF']?>">
                    <div class="form-group">
                        <div class="col-md-12" style="margin-bottom:10px">
                            <div class="col-md-6">
                                <label for="training_title">Training Title:</label>
                                <input type="text" class="form-control" id="training_title" value="<?php echo $tr_title ?>" readonly required> </div>
                            <div class="col-md-6">
                                <label for="training_org">Training Organization:</label>
                                <input type="text" class="form-control" id="training_org" name="training_org" value="<?php echo $tr_org ?>" readonly required> </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="training_sched">Training Schedule:</label>
                        <div class="col-md-12" style="margin-bottom:10px">
                            <div class="col-md-6">
                                <div class="input-append date form_datetime">
                                    <input size="16" type="text" value="<?php echo $tr_strt_dt ?>" readonly class="form-control" placeholder="From" name="training_date-from" required> <span class="add-on"><i class="icon-th"></i></span> </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-append date form_datetime">
                                    <input size="16" type="text" value="<?php echo $tr_end_dt ?>" readonly class="form-control" placeholder="Until" name="training_date-until" required> <span class="add-on"><i class="icon-th"></i></span> </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="training_venue">Training Venue/Location:</label>
                        <input type="text" id="training_venue" class="form-control" value="<?php echo $tr_loc ?>" readonly required> </div>
                    <div class="form-group">
                        <label for="training_cost">Training Cost:</label>
                        <input type="text" id="training_cost" class="form-control" value="<?php echo $tr_cost ?>" readonly required> </div>
                    <div class="form-group">
                        <label for="training_charging">Charging:</label>
                        <input type="text" id="training_charging" class="form-control" value="<?php echo $tr_charge ?>" readonly required> </div>
                    <div class="form-group">
                        <label for="training_justification">
                            <h4 style="font-weight: 600">JUSTIFICATION FOR THE REQUEST:</h4></label>
                        <textarea name="train_justify" id="training_justification" rows="5" class="form-control" style="resize: none" readonly required><?php echo $tr_justify?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="training_justification">
                            <h4 style="font-weight: 600">Request Progress</h4></label>
                        <textarea name="train_justify" id="training_justification" rows="5" class="form-control" style="resize: none" readonly required><?php echo $tr_tblstmp?></textarea>
                    </div>
                    <hr>
                    <?php
                    if($_SESSION['user_type'] != 'EMPLOYEE')
                    {
                    echo '<input type="submit" class="btn btn-primary btn-block" name="approve_form" value="Approve Request">
                    <input type="submit" class="btn btn-danger btn-block" name="deny_form" value="Deny Request">';
                    }
                    echo '<input type="submit" class="btn btn-default btn-block" name="cancel_form" value="Cancel Request">';
                    ?>
                    </form>
                    <br>
  
        </div>
        <!-- jQuery Version 1.11.1 -->
        <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-datetimepicker.min.js"></script>
    </body>

    </html>