<?php
session_start();

if(isset($_SESSION['logged_user']) && isset($_SESSION['user_type']))
{
    header("Location: dashboard.php");
}
else
{
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Woodfields Consultants Inc.</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div id="loginform" class="container">
  <div>
     <img src="images/logo.png" class="center-block" style="margin-top: 20px"/> </a>
  </div>
      <form class="form-signin" action="<?php $_SERVER['PHP_SELF']?>" method="POST">
        <h3 class="form-signin-heading">Human Resource Information System</h3>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="text" id="inputEmail" class="form-control" placeholder="Username" name="username" required autofocus><br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <input type="submit" class="btn btn-primary btn-block" value="Sign In" name="login_btn">
        <center><a href="#">Forgot Password</a></center>
      </form>
    </div>
    
    <?php
    
    if(isset($_POST['login_btn']))
    {
        //include the database connection
        include 'config.php';
        $l_username = htmlentities($_POST['username']);
        $l_password = htmlentities($_POST['password']);
        
        //prepare the SQL select statement
        $l_statement = $conn->prepare("SELECT n_contnmbr, s_acsstype, s_isactive FROM usr_access WHERE s_username = ? AND s_usrpaswd = ?");
        //bind the parameters
        $l_statement->bind_param("ss",$l_username,$l_password);
        //execute the prepared statement
        $l_statement->execute();
        
        $l_statement->store_result();
        
        //check whether the logging user has credentials to database
        if($l_statement->num_rows > 0)
        {
            $l_statement->bind_result($l_user,$l_acsstype,$l_acctstat);
            $l_statement->fetch();
       
            // Check whether the logging user has rights to enter the system
            if($l_acctstat == 'N')
            {
                show_error("Sorry access denied! Check for login status at HR, thanks!");
            }
            else
            {
                //assigning session logged user to determine the who is the user accessing the system and user type to determine employees' job position.
                
                $_SESSION['logged_user'] = $l_user;
                $_SESSION['user_type'] = $l_acsstype;
                //redirect the logged user to dashboard.php
                header("Location: dashboard.php");
            }
        }
        else
        {
            show_error("Access denied! No Credentials found!");
        }
            
    }
            
            function show_error($text)
            {
                echo "<h4 style='text-align:center; font-weight: 700; margin-top: 20px;'>".$text."</h4>";
            }
    ?>

</body>
</html>
