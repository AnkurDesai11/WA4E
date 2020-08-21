<?php
require_once "pdo.php";
session_start();

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['message'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
  
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['message'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
     && isset($_POST['headline']) && isset($_POST['summary'])) {

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        //$message = "Make is required";
        $_SESSION['message']="All fields are required";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as not all values entered");
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    else if( (strpos($_POST['email'], '@') !== false)==false ){
        //$message = "Email must have an at-sign (@)";
        $_SESSION['message']="Email must have an at-sign (@)";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as Email must have an at-sign (@)");
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    
    else {
        
        $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn,
            last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :profile_id');
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':profile_id' => $_REQUEST['profile_id'])
        );
        $_SESSION['success']="Record Updated";
        //$_SESSION['status']=2;
        error_log("Record Updated");
        
        header("Location: index.php");
        return;
    }   
}

$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);


?>
<html>
<head>
<title>Ankur Bhaskar Desai - Automobile Tracker a3530a3c</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>
<div class="container">
<h3>Edit User</h3>
<?php
    // Flash pattern
    if ( isset($_SESSION['message']) ) {
        echo '<p style="color:red">'.$_SESSION['message']."</p>\n";
        unset($_SESSION['message']);
    }
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60" value="<?= $f ?>"></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?= $l ?>"></p>
<p>Email:
<input type="text" name="email" size="30" value="<?= $e ?>"></p>
<p>Headline:<br>
<input type="text" name="headline" size="80" value="<?= $h ?>"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80" style="width: 547px; height: 169px;"  resize: both;><?php echo $s ?></textarea>
</p><p>
<input type="submit" value="Save">
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
</p>
</form>
</div>
</body>

