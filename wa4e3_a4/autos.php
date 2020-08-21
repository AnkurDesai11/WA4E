<?php
require_once "pdo.php";

$message=false;
$status=0;


// Demand a GET parameter
if ( ! isset($_GET['email']) || strlen($_GET['email']) < 1  ) {
    die('Name parameter missing');
}

$user=$_GET['email'];

echo("<h1>Tracking Autos for ");  
    echo $user;
echo("</h1>");

//if ( isset($_POST['logout']) ) {
    // Redirect the browser to index.php
   // header("Location: index.php");
   // exit;
//}

if ( isset($_POST['make']) && isset($_POST['year']) 
     && isset($_POST['mileage'])) {

    if ( strlen($_POST['make']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $message = "Make is required";
        $status=1;
        error_log("Entry failed as not all values entered");
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    else if( !ctype_digit(strval($_POST['year'])) || !ctype_digit(strval($_POST['mileage'])) ){
        $message = "Mileage and year must be numeric";
        $status=1;
        error_log("Entry failed as year and/or mileage not integers");
    }

    else {
        $sql = "INSERT INTO autos (make, year, mileage) 
                VALUES (:make, :year, :mileage)";
        //echo("<pre>\n".$sql."\n</pre>\n");
        $status=2;
        $message = "Record inserted";
        error_log("Record inserted");
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':make' => $_POST['make'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']));
    }   
}

if ( $status==1 ){
    echo "<p style='color:red;'>". $message ."</p>\n";
}
else if ( $status==2 ){
    echo "<p style='color:green;'>". $message ."</p>\n";
}
else{
    echo "<p style='color:green;'></p>\n";
}

?>
<html>
<head></head><body>
<!--<h1>Tracking Autos for  
    <?php //echo $user ?>
</h1>-->
<form method="post">
<p>Make:
<input type="text" name="make" size="40" ></p>
<p>Year:
<input type="text" name="year"></p>
<p>Mileage:
<input type="text" name="mileage"></p>
<p><button type="submit">Add</button>   <input type="button" onclick="window.location.href='index.php';" value="logout" />
</p>
<!--<p><input type="submit" value="logout"/></p>-->

<h2>Automobiles</h2>
<ul>
<?php
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ( $rows as $row ) {
    echo "<li>";
    echo htmlentities($row['year']);
    echo(" ");
    echo htmlentities($row['make']);
    echo(" / ");
    echo htmlentities($row['mileage']);
    echo("</li>\n");
}
?>
</ul>
</body>
