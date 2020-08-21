<?php
require_once "pdo.php";
session_start();
$failure=false;
$loginstatus=0;
/*
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "Email and password are required";
        $loginstatus=1;
        error_log("Login failed as email and/or password not entered");
    }
    else if ( (strpos($_POST['email'], '@') !== false)==false ){
        $failure = "Email must have an at-sign (@)";
        $loginstatus=1;
        error_log("Login failed as Email doesnt contain '@'");
    }
    else {
        
        $sql = "SELECT email FROM users 
        WHERE email = :em AND password = :pw";

        //echo "<p>$sql</p>\n";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':em' => $_POST['email'], 
            ':pw' => $_POST['pass']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //var_dump($row);
         if ( $row === FALSE ) {
            $failure = "Incorrect password";
            $loginstatus=1;
            error_log("Login failed as password is incorrect for ".$_POST['email']);
        } else { 
            header("Location: autos.php?email=".urlencode($_POST['email']));
            $failure = "Login success";
            $loginstatus=2;
            error_log("Login successful");
        }
    }
}
*/
//how login should be (maybe with implementing hash)


$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123
$failure = false;  // If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset($_SESSION['email']);
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        //$failure = "Email and password are required";
        //$loginstatus=1;
        $_SESSION['error']="Email and password are required";
        error_log("Login failed as email and/or password not entered");
        header("Location: login.php");
        return;
    }
    else if ( (strpos($_POST['email'], '@') !== false)==false ){
        //$failure = "Email must have an at-sign (@)";
        //$loginstatus=1;
        $_SESSION['error']="Email must have an at-sign (@)";
        error_log("Login failed as Email doesnt contain at-sign (@)");
        header("Location: login.php");
        return;
    }
    else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            // Redirect the browser to view.php
            $_SESSION['email']=$_POST['email'];
            $_SESSION['success']="Login success";
            //?email=".urlencode($_POST['email']));
            //$failure = "Login success";
            //$loginstatus=2;
            error_log("Login successful for ".$_POST['email']);
            header("Location: view.php");
            return;
        } 
        else {
            $_SESSION['error']="Incorrect password";
            //$failure = "Incorrect password";
            //$loginstatus=1;
            error_log("Login failed as password is incorrect for ".$_POST['email']." ".$check);
            header("Location: login.php");
            return;
        }
    }
}


/*
if ( $loginstatus==1 ){
    echo "<p style='color:red;'>". $failure ."</p>\n";
}
else if ( $loginstatus==2 ){
    echo "<p style='color:green;'>". $failure ."</p>\n";
}
else{
    echo "<p style='color:green;'></p>\n";
}
*/

?>
<title>Ankur Bhaskar Desai - login a3530a3c</title>



<h1>Please Log In</h1>
<?php
    if( isset($_SESSION['error']) ){
        echo("<p style='color:red'>".$_SESSION['error']."</p>\n");
        unset($_SESSION['error']);
    }
    if( isset($_SESSION['success']) ){
        echo("<p style='color:green'>".$_SESSION['success']."</p>\n");
        unset($_SESSION['success']);
    }
?>
<form method="post">
<p>Email:
<input type="text" size="40" name="email"></p>
<p>Password:
<input type="text" size="40" name="pass"></p>
<p><input type="submit" value="Log In"/> 
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
<!--<a href="<?php //echo($_SERVER['PHP_SELF']);?>">Refresh</a>
<a href="index.php">Start Page</a></p>-->
</form>
