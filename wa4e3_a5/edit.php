<?php
require_once "pdo.php";
session_start();

// Guardian: Make sure that autos_id is present
if ( ! isset($_GET['autos_id']) ) {
    $_SESSION['message'] = "Missing autos_id";
    header('Location: index.php');
    return;
}
  
$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['message'] = 'Bad value for autos_id';
    header( 'Location: index.php' ) ;
    return;
}

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) 
     && isset($_POST['mileage'])) {
    //$givenID=$_GET['autos_id'];
    if ( strlen($_POST['make']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1 || strlen($_POST['model']) < 1) {
        //$message = "Make is required";
        $_SESSION['message']="All fields are required";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as not all values entered");
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    else if( !ctype_digit(strval($_POST['year'])) || !ctype_digit(strval($_POST['mileage'])) ){
        //$message = "Mileage and year must be numeric";
        $_SESSION['message']="Mileage and year must be numeric";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as year and/or mileage not integers");
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;
    }

    else{
        $sql = "UPDATE autos SET make = :make,
            model = :model, year = :year, mileage = :mileage
            WHERE autos_id = :autos_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage'],
            ':autos_id' => $_POST['autos_id']));
        $_SESSION['success'] = 'Record updated';
        //$_SESSION['status']=2;
        header( 'Location: index.php' ) ;
        return;
    }
}

$ma = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$y = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);
$autos_id = $row['autos_id'];

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
<p>Make:
<input type="text" name="make" value="<?= $ma ?>"></p>
<p>Model:
<input type="text" name="model" value="<?= $mo ?>"></p>
<p>Year:
<input type="text" name="year" value="<?= $y ?>"></p>
<p>Mileage:
<input type="text" name="mileage" value="<?= $mi ?>"></p>
<input type="hidden" name="autos_id" value="<?= $autos_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</form>
</div>
</body>
</html>