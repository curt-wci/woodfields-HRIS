<?php
/**
** USE jsonPDO() to execute statement without binding parameters
** USE json_bind_multi() to execute statement with binding parameters

*/

include 'config.php';
session_start();

//check session storage if there is logged_user and user_type values
if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type']))
{

}
else
{
    die('NoCredentialsException: Not allowed to directly execute this script without a valid credentials. Please try again!');
}


if(isset($_GET['function']))
{
    $func = $_GET['function'];
    $func2 = $_GET['extra'];
        
    switch($func){
        case "getAllPersonnelRequest" : {getAllPersonnelRequest($conn); break;}
        case "getRequestDetails" : {getRequestDetails($conn,$func2); break;}  
        case "getApproval" : {getApproval($conn,$func2); break;}
        case "approvePersonnelRequest" : {approvePersonnelRequest($conn,$func2); break;}
        case "deleteRequest" : {deleteRequest($conn,$func2); break; }
        case "getAllPosition" : {getAllPosition($conn); break; }
        case "getAllDepartment" : {getAllDepartment($conn); break; }
        case "InsertNewPersonnelRequest" : {InsertNewPersonnelRequest($conn,$func2); break;}
        case "updatePersonnelRequest" : {updatePersonnelRequest($conn,$func2); break;}
        case "deleteRequestDetails" : {deleteRequestDetails($conn,$func2); break; }
        case "getRequestDetails2" : {getRequestDetails2($conn,$func2); break;}  
        case "getPersonnelRequestById" : {getPersonnelRequestById($conn,$func2); break;}
        case "EditPersonnelRequest" : {EditPersonnelRequest($conn,$func2); break; }
        case "getSessionStored" : {getSessionStored(); break;}
        case "getAllTrainingRequestByUserType" : {getAllTrainingRequestByUserType($conn); break;}
        case "getPersonnelRequest_Report" : {getPersonnelRequest_Report($conn); break;}
        case "getEmpTrainingRequest_Report" : {getEmpTrainingRequest_Report($conn); break;}
        case "get_hr_potion_details" : {get_hr_potion_details($func2,$conn); break;}
        default : {
            die("FunctionCallException: Function not found to execute. Please try again!");
        break;}
    }
}
else{
    die("FunctionCallException: Function not found to execute. Please try again!");
}
function getAllTrainingRequestByUserType($conn){
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
    
    $var_position = $_SESSION['user_type'];
    $stat = "% APPROVE BY ".$lastname.", ".$firstname."%";
    $static_approve = "APPROVE";
    $static_deny = "DENY";
    $static_cancel = "CANCEL";
    
    $sql_statement = "SELECT
     department.`s_deptdesc`,
     employeesx.`s_lastname`, employeesx.`s_frstname`,
     positionxx.`s_posidesc`,
     trainings.`s_trntitle`, trainings.`s_trainorg`, trainings.`d_strtdate`, trainings.`d_end_date`, trainings.`s_location`, trainings.`n_traincst`, trainings.`n_charging`, trainings.`s_justify`, trainings.`s_reqstats`, trainings.`s_tblstmps`
     FROM
     `employeesx` employeesx INNER JOIN `trainings` trainings ON employeesx.`n_contnmbr` = trainings.`n_reqempid`
     INNER JOIN `positionxx` positionxx ON trainings.`n_reqprjcd` = positionxx.`n_posinmbr`
     INNER JOIN `department` department ON trainings.`n_reqdptcd` = department.`n_deptnmbr`";
    
    switch($var_position){
        case "SUPERVISOR" :
            $sql_statement .= " WHERE trainings.`n_reqdptcd` = ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_tblstmps` NOT LIKE ?";
            $paramList = array();
            array_push($paramList,$deptno,$static_approve,$static_deny,$static_cancel,$stat);
            break;
            
        case "TOP_MNGT":
            $sql_statement = $sql_statement." WHERE trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_tblstmps` NOT LIKE ?";
            $paramList = array();
            array_push($paramList,$static_approve,$static_deny,$static_cancel,$stat);
            break;
            
        case "EMPLOYEE":
            $sql_statement = $sql_statement." WHERE trainings.`n_reqempid` = ? AND trainings.`n_reqdptcd` = ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ?";
            $paramList = array();
            array_push($paramList,$count_no,$deptno,$static_approve,$static_deny,$static_cancel);
            break;
        
        case "HR":
            $sql_statement = $sql_statement." WHERE trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_reqstats` <> ? AND trainings.`s_tblstmps` NOT LIKE ?";
            $paramList = array();
            array_push($paramList,$static_approve,$static_deny,$static_cancel,$stat);
            break;
            
        default:
            die("SwitchCaseException: Option was not found to execute. Please try again!");
            break;
    }
    echo '{"data": '. json_bind_multi($conn,$sql_statement,$paramList) .' }';
    
}
function getAllPersonnelRequest($conn){
    $qry = "SELECT * FROM personnel_request p
    JOIN positionxx po ON p.n_posnumbr = po.n_posinmbr 
    JOIN department d ON p.n_deptnmbr = d.n_deptnmbr WHERE request_status = ? ";
      
    $paramList = array();
    $paramList[] = 1;
    echo '{"data": '. json_bind_multi($conn,$qry,$paramList) .' }';
}
function getPersonnelRequestById($conn,$func2){
    $qry = "SELECT n_deptnmbr, n_posnumbr,d_mobiDate FROM personnel_request WHERE n_requestId = ?";
    $paramList = array($func2);
    echo '{"data": '. json_bind_multi($conn,$qry,$paramList) .' }';
}
function getRequestDetails($conn,$func2){
    $qry = "SELECT * FROM hris.personnel_request_details WHERE n_requestId = ? AND stamp NOT LIKE ?";
    $paramList = array($func2,"%DELETED%");
    echo '{"data": '. json_bind_multi($conn,$qry,$paramList) .' }';
}
function getRequestDetails2($conn,$func2){
    $qry = "SELECT employment_type, emp_dur_from, emp_dur_to,replaced_by,isBudget,budget_clearance,budget_clearance_date,justification,job_summary,qual_other_req FROM hris.personnel_request_details WHERE n_requestId = ? AND stamp NOT LIKE ?";
    $paramList = array($func2,"%DELETED%");
    echo '{"data": '. json_bind_multi($conn,$qry,$paramList) .' }';
}
function getApproval($conn,$func2){
    $qry = "SELECT n_requestId, isApproveAdmin, isApproveEVP, isApproveCEO FROM personnel_request WHERE n_requestId = ?";
    $paramList = array($func2."");
    echo '{"data": '. json_bind_multi($conn,$qry,$paramList) .' }';
}
function approvePersonnelRequest($conn,$func2){
    //initiate DateTime object
    $l_date = new DateTime();
    $pieces = explode(" ",$func2);
    $curr_stmp = "";
    $paramList = array();
    
    switch($pieces[1]){
        case '51':
            $curr_stmp =" APPROVE BY VP - ADMIN AND FINANCE ".$l_date->getTimestamp().";";
            $sql  = "UPDATE personnel_request SET isApproveAdmin = ? WHERE n_requestId = ?";
            array_push($paramList,$curr_stmp,$pieces[2]);
            $stmt = $conn->prepare($sql);
            DynamicBindVariables($stmt, $paramList);
             $result = $stmt->execute();
            break;
        case '136':
            $curr_stmp =" APPROVE BY EVP - COO ".$l_date->getTimestamp().";";
            $sql  = "UPDATE personnel_request SET isApproveEVP = ? WHERE n_requestId = ?";
            array_push($paramList,$curr_stmp,$pieces[2]);
            $stmt = $conn->prepare($sql);
            DynamicBindVariables($stmt, $paramList);
             $result = $stmt->execute();
            break;
        case '138':
            $curr_stmp =" APPROVE BY PRESIDENT and CEO ".$l_date->getTimestamp().";";
            $sql  = "UPDATE personnel_request SET isApproveCEO = ? WHERE n_requestId = ?";
            array_push($paramList,$curr_stmp,$pieces[2]);
            $stmt = $conn->prepare($sql);
            DynamicBindVariables($stmt, $paramList);
            $result = $stmt->execute();
            break;
    }
    
    if(isApprovalComplete($conn,$pieces[2])){
        $sql_statement  = "UPDATE personnel_request SET request_status = ? WHERE n_requestId = ?";
        $arr_ay = array(2,$pieces[2]);
        $stmt = $conn->prepare($sql_statement);
        DynamicBindVariables($stmt, $arr_ay);
        $result = $stmt->execute();
    }
    
}
function deleteRequest($conn,$func2){
    $qry = "UPDATE personnel_request SET request_status = ? WHERE n_requestId = ?";
    $paramList = array(4,$func2);
    $stmt = $conn->prepare($qry);
    DynamicBindVariables($stmt,$paramList);
    $result = $stmt->execute();
    echo $result;
}
function getAllPosition($conn){
    $var_position = $_SESSION['user_type'];
    $sql_statement = "SELECT * FROM positionxx";
    
    switch($var_position){
        case "SUPERVISOR" :
            $sql_statement .= " WHERE n_deptnmbr = ?";
            $paramList = array($_SESSION['deptCode']." ");
            echo '{"data": '. json_bind_multi($conn,$sql_statement,$paramList) .' }';
            break;
            
        case "TOP_MNGT":
            echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
            break;
        
        case "HR":
            echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
            break;
            
        default:
            die("SwitchCaseException: Option was not found to execute. Please try again!");
            break;
    }
}
function getAllDepartment($conn){
    $sql_statement = "SELECT * FROM hris.department";
    $var_position = $_SESSION['user_type'];
    
    switch($var_position){
        case "SUPERVISOR" :
            $sql_statement .= " WHERE n_deptnmbr = ?";
            $paramList = array($_SESSION['deptCode']." ");
            echo '{"data": '. json_bind_multi($conn,$sql_statement,$paramList) .' }';
            break;
            
        case "TOP_MNGT":
            echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
            break;
        
        case "HR":
            echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
            break;
            
        default:
            die("SwitchCaseException: Option was not found to execute. Please try again!");
            break;
    }
}
function InsertNewPersonnelRequest($conn,$func2){
    $ar = array();
    
    foreach($func2 as $item)
    {
        $ar[] = $item['value'];
    }
    $dt = new DateTime();
    $curr_date = date('Y-m-d');
    $stamp = "ADDED BY ".$_SESSION['logged_user']." ".$dt->getTimestamp().";";
    $qry = "INSERT INTO personnel_request (`request_date`,`n_deptnmbr`,`n_posnumbr`,`d_mobiDate`,`request_status`,`stamp`) VALUES(?,?,?,?,?,?)";
    $paramList = array($curr_date,intval($ar[1]),intval($ar[0]),$ar[2],1,$stamp);
        
    $stmt = $conn->prepare($qry);
    $res = DynamicBindVariables($stmt,$paramList);
    $result = $stmt->execute();
    
    $details_insert_id = $conn->insert_id;
    
    $qry = "INSERT INTO  personnel_request_details (`n_requestId`) VALUES(?)";
    unset($paramList);
    $paramList = array($details_insert_id);
    $stmt = $conn->prepare($qry);
    $res = DynamicBindVariables($stmt,$paramList);
    $result = $stmt->execute();
    echo $conn->insert_id;
}
function updatePersonnelRequest($conn,$func2){
    $values = array();
    $fields = array();
    $qry = "UPDATE personnel_request_details SET ";
    
    foreach($func2 as $item)
    {
        $fields[] = $item['name'];
        $values[] = $item['value'];
    }
    $filter_field = end($fields);
    $filter_value = end($values);
    
    array_pop($values);
    array_pop($fields);
    
    for($i = 0; $i< count($fields); $i++){
        if($values[$i]== ""){
            $values[$i] = null;
        }
        $qry.="`".$fields[$i]."` = ? , ";
    }
    $dt = new DateTime();
    $curr_date = date('Y-m-d');
    $stamp = "ADDED BY ".$_SESSION['logged_user']." ".$dt->getTimestamp();
    
    $qry = rtrim ($qry , " , ");
    $qry.=", `stamp` = ? WHERE ".$filter_field." = ?";
    array_push($values,$stamp);
    array_push($values,$filter_value);
    
    $stmt = $conn->prepare($qry);
    $res = DynamicBindVariables($stmt,$values);
    $result = $stmt->execute();
    echo $result;
}
function deleteRequestDetails($conn,$func2){
    $dt = new DateTime();
    $stamp = "DELETED BY ".$_SESSION['logged_user']." ".$dt->getTimestamp();
    $qry = "UPDATE personnel_request_details SET stamp = ? WHERE n_requestId = ?";
    $paramList = array($stamp,$func2);
    $stmt = $conn->prepare($qry);
    DynamicBindVariables($stmt,$paramList);
    $result = $stmt->execute();
    echo $result;
}
function EditPersonnelRequest($conn,$func2){
    $ar = array();
    
    foreach($func2 as $item)
    {
        $ar[] = $item['value'];
    }
    
    $qry = "UPDATE personnel_request SET n_posnumbr = ?, n_deptnmbr = ?, d_mobiDate = ? WHERE n_requestId = ?";
    $stmt = $conn->prepare($qry);
    DynamicBindVariables($stmt,$ar);
    $result = $stmt->execute();
    echo $result;
}
function getSessionStored(){
   echo '[{"user_type":"'.$_SESSION['user_type'].'"},{"logged_user": "'.$_SESSION['logged_user'].'"}]';
}
function isApprovalComplete($conn,$id){
    $arr = array($id);
    $sql_statement = "SELECT * FROM personnel_request WHERE n_requestId = ? AND isApproveAdmin IS NOT NULL AND isApproveEVP IS NOT NULL AND isApproveCEO IS NOT NULL";
    $stmt = $conn->prepare($sql_statement);
    DynamicBindVariables($stmt, $arr);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        return true;
    }
    else{
        return false;
    }
}
function getPersonnelRequest_Report($conn){
    $sql_statement = "SELECT * FROM personnel_request p 
    JOIN positionxx po ON p.n_posnumbr = po.n_posinmbr 
    JOIN department d ON p.n_deptnmbr = d.n_deptnmbr
    JOIN personnel_request_details pr ON p.n_requestId = pr.n_requestId
    ORDER BY request_status";
    
    echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
}
function getEmpTrainingRequest_Report($conn){
    $sql_statement = "SELECT
     department.`s_deptdesc`,
     employeesx.`s_lastname`, employeesx.`s_frstname`,
     positionxx.`s_posidesc`,
     trainings.`s_trntitle`, trainings.`s_trainorg`, trainings.`d_strtdate`, trainings.`d_end_date`, trainings.`s_location`, trainings.`n_traincst`, trainings.`n_charging`, trainings.`s_justify`, trainings.`s_reqstats`, trainings.`s_tblstmps`
     FROM
     `employeesx` employeesx INNER JOIN `trainings` trainings ON employeesx.`n_contnmbr` = trainings.`n_reqempid`
     INNER JOIN `positionxx` positionxx ON trainings.`n_reqprjcd` = positionxx.`n_posinmbr`
     INNER JOIN `department` department ON trainings.`n_reqdptcd` = department.`n_deptnmbr`";
    
    echo '{"data": '. jsonPDO($sql_statement,$conn) .' }';
}
function get_hr_potion_details($func2,$conn){
    $arr = array($func2);
    $sql_statement = 'SELECT `pr_hr_id`, `request_id`,
    (CASE WHEN `vacancy_status` = 1 THEN "SOURCING"
    WHEN `vacancy_status` = 2 THEN "INTERVIEWING"
    WHEN `vacancy_status` = 3 THEN "INITIAL"
    WHEN `vacancy_status` = 4 THEN "DEPT/UNIT"
    WHEN `vacancy_status` = 5 THEN "SCREENING"
    WHEN `vacancy_status` = 6 THEN "MEDICAL"
    WHEN `vacancy_status` = 7 THEN "ORIENTATION"
    ELSE null END) AS "vacancy_status", `vacancy_from`, `vacancy_to`, `remarks` FROM `pr_hr_portion` WHERE `request_id` = ?';
    echo '{"data": '. json_bind_multi($conn,$sql_statement,$arr) .' }';
}


function json_bind_multi($conn,$stmt,$paramList)
{
    $arr = "[]";
    $out = "[]";
    $statement = $conn->prepare($stmt);
    DynamicBindVariables($statement,$paramList);
    $statement->execute();
    $result = $statement->get_result();
    
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc())
        {
            $ar[] = $row;
        }
        $out =  json_encode($ar);
    }
    else{
        return;
    }
    return $out;
}

function jsonPDO($stmt, $conn)
{
    $out;
    $ar = array();
    
    $stmt = $conn->prepare($stmt);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc())
        {
            $ar[] = $row;
        }
        return json_encode($ar);
    }
    else{
        $out = json_encode("No result");
    }
    
    return $out;
}

function DynamicBindVariables($stmt, $params)
{
    $array_data = array();
    
    if ($params != null)
    {
        // Generate the Type String (eg: 'issisd')
        $types = '';
        foreach($params as $param)
        {
            if(is_int($param)) {
                // Integer
                $types .= 'i';
            } elseif (is_float($param)) {
                // Double
                $types .= 'd';
            } elseif (is_string($param)) {
                // String
                $types .= 's';
            } else {
                // Blob and Unknown
                $types .= 'b';
            }
        }
        // Add the Type String as the first Parameter
        $bind_names[] = $types;
  
        // Loop thru the given Parameters
        for ($i=0; $i<count($params);$i++)
        {
            // Create a variable Name
            $bind_name = 'bind' . $i;
            // Add the Parameter to the variable Variable
            $$bind_name = $params[$i];
            // Associate the Variable as an Element in the Array
            $bind_names[] = &$$bind_name;
        }
         
        // Call the Function bind_param with dynamic Parameters
        call_user_func_array(array($stmt,'bind_param'), $bind_names);
    }
    return $stmt;
}

/** FOR DEBUG
if(false === $result){
        die("Failed: ".$stmt->error);
    }
    
    
*/

?>