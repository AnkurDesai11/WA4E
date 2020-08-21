<?php
    require_once "pdo.php";
    session_start();
    if ( ! isset($_SESSION["email"]) ) { 
        die('Not logged in');
        error_log("Not logged in");
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
<h1>Tracking Autos for 
<?php echo($_SESSION['email']) ?>
</h1>
<?php
    
    if( isset($_SESSION['success']) ){
        echo("<p style='color:green'>".$_SESSION['success']."</p>\n");
        unset($_SESSION['success']);
    }
     
    /*
    else { 
        ?> <p>This is where a cool application would be.</p> 
        <p>Please <a href="logout.php">Log Out</a> when you are done.</p> 
        <?php 
    }
    */ 
?>
<h2>Automobiles</h2>
<ul>
<p>
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
</p></ul>
<p>
<a href="add.php">Add New</a> |
<a href="logout.php">Logout</a>
</p>
</div>


</body></html>