<h1>Please Log In</h1>
<?php
require_once "pdo.php";

$failure=false;
$loginstatus=0;
/*
if ( isset($_POST['who']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "Email and password are required";
        $loginstatus=1;
        error_log("Login failed as email and/or password not entered");
    }
    else if ( (strpos($_POST['who'], '@') !== false)==false ){
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
            ':em' => $_POST['who'], 
            ':pw' => $_POST['pass']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //var_dump($row);
         if ( $row === FALSE ) {
            $failure = "Incorrect password";
            $loginstatus=1;
            error_log("Login failed as password is incorrect for ".$_POST['who']);
        } else { 
            header("Location: autos.php?email=".urlencode($_POST['who']));
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
if ( isset($_POST['who']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "Email and password are required";
        $loginstatus=1;
        error_log("Login failed as email and/or password not entered");
    }
    else if ( (strpos($_POST['who'], '@') !== false)==false ){
        $failure = "Email must have an at-sign (@)";
        $loginstatus=1;
        error_log("Login failed as Email doesnt contain at-sign (@)");
    }
    else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            // Redirect the browser to autos.php
            header("Location: autos.php?email=".urlencode($_POST['who']));
            $failure = "Login success";
            $loginstatus=2;
            error_log("Login successful for ".$_POST['who']);
            return;
        } 
        else {
            $failure = "Incorrect password";
            $loginstatus=1;
            error_log("Login failed as password is incorrect for ".$_POST['who']." ".$check);
        }
    }
}



if ( $loginstatus==1 ){
    echo "<p style='color:red;'>". $failure ."</p>\n";
}
else if ( $loginstatus==2 ){
    echo "<p style='color:green;'>". $failure ."</p>\n";
}
else{
    echo "<p style='color:green;'></p>\n";
}


?>
<title>Ankur Bhaskar Desai - login a3530a3c</title>
<form method="post">
<p>Email:
<input type="text" size="40" name="who"></p>
<p>Password:
<input type="text" size="40" name="pass"></p>
<p><input type="submit" value="Log In"/>
<a href="<?php echo($_SERVER['PHP_SELF']);?>">Refresh</a>
<a href="index.php">Start Page</a></p>
</form>
