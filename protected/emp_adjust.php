<?php
    if(isset($_GET['tr_name']))
    {
        include ("config.php");
        session_start();
        
        if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type']))
        {
        }
        else
        {
            header("Location: index.php");
        }
        
        if($_SESSION['user_type'] != "TOP_MNGT")
        {
            header("Location: index.php");
        }
    }
    else
    {
        die("External request are invalid. Please use the front-end");
    }

    $l_statement = $conn->prepare("SELECT
     employeesx.`s_lastname`, employeesx.`s_frstname`, employeesx.`n_contnmbr`,
     department.`n_deptnmbr`, department.`s_deptdesc`,
     positionxx.`n_posinmbr`, positionxx.`s_posidesc`
     FROM
     `employeesx` employeesx INNER JOIN `empjobhist` empjobhist ON employeesx.`n_contnmbr` = empjobhist.`n_contnmbr`
     INNER JOIN `department` department ON empjobhist.`n_deptnmbr` = department.`n_deptnmbr`
     INNER JOIN `positionxx` positionxx ON empjobhist.`n_posinmbr` = positionxx.`n_posinmbr` WHERE hris.employeesx.n_contnmbr = ? ORDER BY empjobhist.`d_hiredate` DESC LIMIT 0,1");
    $l_statement->bind_param('s',$_SESSION['logged_user']) or die ($l_statement->error);
    $l_statement->execute();

    $l_statement->bind_result($lastname,$firstname,$count_no,$deptno,$deptdesc,$jobCode,$jobDesc);

     while($l_statement->fetch())
    {

    }

    $l_statement = $conn->prepare("SELECT
     employeesx.`s_lastname`, employeesx.`s_frstname`,
     trainings.`s_trntitle`, trainings.`s_trainorg`, trainings.`d_strtdate`, trainings.`d_end_date`, trainings.`s_location`, trainings.`n_traincst`, trainings.`n_charging`, trainings.`s_justify`, trainings.`s_tblstmps`, trainings.`s_reqstats`,
     positionxx.`s_posidesc`,
     department.`s_deptdesc`,
     empjobhist.`d_hirethru`, empjobhist.`d_hiredate`, empjobhist.`n_deptnmbr`, empjobhist.`n_posinmbr`, empjobhist.`n_contnmbr`, empjobhist.`n_compnyid`, empjobhist. `n_basicpay`
     FROM
     `employeesx` employeesx INNER JOIN `trainings` trainings ON employeesx.`n_contnmbr` = trainings.`n_reqempid`
     INNER JOIN `positionxx` positionxx ON trainings.`n_reqprjcd` = positionxx.`n_posinmbr`
     INNER JOIN `department` department ON trainings.`n_reqdptcd` = department.`n_deptnmbr`
     INNER JOIN `empjobhist` empjobhist ON employeesx.`n_contnmbr` = empjobhist.`n_contnmbr`
     WHERE
    trainings.`s_trntitle` = ?
    ORDER BY
	empjobhist.`d_hirethru` DESC
    LIMIT 0,1");
    $l_statement->bind_param('s',$_GET['tr_name']) or die ($l_statement->error);
    $l_statement->execute();
    
    $l_statement->bind_result($tr_lname,$tr_fname,$tr_title,$tr_org,$tr_strt_dt,$tr_end_dt,$tr_loc,$cost,$tr_charge,$tr_justify,$tr_tblstmp,$tr_status,$tr_jobDesc,$tr_deptDesc,$tr_hirethru,$tr_hiredate,$tr_deptNo,$tr_jobNo,$count_no,$compid,$pay);

     while($l_statement->fetch())
    {
        
    }
    $cost = intval($cost);

    

function emp_service_adjust($cost,$time)
{
    $render_year;
    if($cost <= 999)
    {
       $render_year = 6;
    }
    if($cost >= 1000 && $cost <= 10000)
    {
       //employee render time is 6 months 
       $render_year = 6;
    }
    else if($cost >= 10001 && $cost <= 25000)
    {
        //employee render time is 12 months 
        $render_year = 12;
    }
    else if($cost >= 25001 && $cost <= 50000)
    {
        //employee render time is 1.5 years 
        $render_year = 18;
    }
    else if($cost >= 50001 && $cost <= 100000)
    {
        //employee render time is 2 years 
        $render_year = 24;
    }
    else if($cost >= 100001 && $cost <= 150000)
    {
        //employee render time is 3 years
        $render_year = 36;
    }
    else if($cost >= 150001 && $cost <= 200000)
    {
        //employee render time is 4 years
        $render_year = 48;
    }
    else if($cost >= 200001 && $cost <= 250000)
    {
        //employee render time is 5 years
        $render_year = 60;
    }
    else if($cost >= 250001 && $cost <= 300000)
    {
        //employee render time is 6 years
        $render_year = 72;
    }
    else if($cost >= 300001 && $cost <= 350000)
    {
        //employee render time is 7 years
        $render_year = 84;
    }
    else if($cost >= 350001 && $cost <= 400000)
    {
        //employee render time is 8 years
       $render_year = 96;
    }
    else if($cost >= 400001 && $cost <= 450000)
    {
        //employee render time is 9 years
        $render_year = 108;
    }
    else if($cost >= 450001 && $cost <= 500000)
    {
        //employee render time is 10 years
        $render_year = 120;
    }
    
    $message = $render_year." month(s) to render";
    $newdate = strtotime("+".$render_year."month", strtotime($time));
    $newdate = date('Y-m-d',$newdate);
    return $newdate."<br>".$message;

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
            
                <h2 class="ccu_heading">Employees' Service Adjustment</h2>
                <hr>
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
                <hr>
                <table class="table">
                    <tr>
                        <td class="ccu_panel_title">Training Title</td>
                        <td><?php echo $tr_title ?></td>
                        <td class="ccu_panel_title">Training Organization</td>
                        <td><?php echo $tr_org ?></td>
                    </tr>
                    <tr>
                        <td class="ccu_panel_title">Training Schedule</td>
                        <td><?php echo $tr_strt_dt.' until '.$tr_end_dt ?></td>
                        <td class="ccu_panel_title">Training Venue</td>
                        <td><?php echo $tr_loc?></td>

                    </tr>
                    <tr>
                        <td class="ccu_panel_title">Training Cost <br>(in pesos)</td>
                        <td><?php echo intval($cost) ?></td>
                        <td class="ccu_panel_title">Charging</td>
                        <td><?php echo intval($tr_charge) ?></td>
                    </tr>
                    <tr>
                        <td class="ccu_panel_title">Employee Hire Date</td>
                        <td><?php echo date("M d, Y",strtotime($tr_hiredate))?></td>
                        <td class="ccu_panel_title">Employee Hire Thru</td>
                        <td><?php echo $adj_date = emp_service_adjust($cost,$tr_hirethru); ?></td>
                    </tr>
                </table>
                
                <form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
                    <input type="submit" class="btn btn-block btn-primary" value="Finalize Request" name="finalize">
                    <input type="submit" class="btn btn-danger btn-block" value="Deny Request" name="deny">
                </form>
                
<?php
    if(isset($_POST['finalize']))
    {
        if($_SESSION['user_type']== 'TOP_MNGT' && $_SESSION['logged_user'] == '138'){
            $getDt = explode(" ",$adj_date);
            
            $getDt = substr($getDt[0],0,-5);
            
            //print_r($getDt);
            
            $qry = "INSERT INTO `empjobhist` VALUES (?,?,?,?,?,?,?,?)";
            $null_val = null;
            $l_statement = $conn->prepare($qry);
            $l_statement->bind_param("bsssssss",$null_val,$count_no,$tr_hiredate,$tr_deptNo, $compid, $tr_jobNo, $pay, $getDt);
            
            if($l_statement->execute())
            {
                $tr = $_GET['tr_name'];
                $const_approve = "APPROVE";
                $l_statement = $conn->prepare(" UPDATE `trainings` SET `s_reqstats`=? WHERE `s_trntitle` = ? ");
                $l_statement->bind_param('ss',$const_approve,$tr);
                
                if($l_statement->execute())
                {
                    echo "<script>alert('Insert success! Employee's year of service has been changed!)</script>";
                    header("refresh: 2; url=dashboard.php");
                }
            }
            else
            {
                echo "<script>alert('Internal Error: Call IT for help!)</script>";
            }
        }

    }
    else if(isset($_POST['deny']))
    {
        
    }
    else
    {
        //do nothing
    }
?>
  
        </div>
        <!-- jQuery Version 1.11.1 -->
        <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-datetimepicker.min.js"></script>
    </body>

    </html>