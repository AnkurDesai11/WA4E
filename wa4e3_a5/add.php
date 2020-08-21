<?php
require_once "pdo.php";
session_start();
//$message=false;
//$status=0;

if ( ! isset($_SESSION["email"]) ) { 
    die('ACCESS DENIED');
    error_log("ACCESS DENIED-Not logged in");
}

if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) 
     && isset($_POST['mileage'])) {

    if ( strlen($_POST['make']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1 || strlen($_POST['model']) < 1) {
        //$message = "Make is required";
        $_SESSION['message']="All fields are required";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as not all values entered");
        header("Location: add.php");
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    else if( !ctype_digit(strval($_POST['year'])) || !ctype_digit(strval($_POST['mileage'])) ){
        //$message = "Mileage and year must be numeric";
        $_SESSION['message']="Mileage and year must be numeric";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as year and/or mileage not integers");
        header("Location: add.php");
        return;
    }

    else {
        $sql = "INSERT INTO autos (make, model, year, mileage) 
                VALUES (:make, :model, :year, :mileage)";
        //echo("<pre>\n".$sql."\n</pre>\n");
        //$status=2;
        //$message = "Record inserted";
        $_SESSION['success']="Record Added";
        //$_SESSION['status']=2;
        error_log("Record inserted");
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']));
        
        header("Location: index.php");
        return;
    }   
}

?>


<html><head>
<title>Ankur Bhaskar Desai - Automobile Tracker a3530a3c</title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Tracking Autos for <?php echo($_SESSION['email']) ?></h1>
<?php
    if( isset($_SESSION['message']) && $_SESSION['status']==1){
        echo("<p style='color:red'>".$_SESSION['message']."</p>\n");
        unset($_SESSION['message']);
    }
    if( isset($_SESSION['success']) && $_SESSION['status']==2){
        echo("<p style='color:green'>".$_SESSION['message']."</p>\n");
        unset($_SESSION['success']);
    }
?> 
<form method="post">
<p>Make:
<input type="text" name="make" size="60"></p>
<p>Model:
<input type="text" name="model" size="60"></p>
<p>Year:
<input type="text" name="year"></p>
<p>Mileage:
<input type="text" name="mileage"></p>
<input type="submit" value="Add"> 
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
</form>

</div>


</body></html>