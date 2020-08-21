<?php
require_once "pdo.php";
session_start();

//CHECK FOR PASSED GET PARAMETER
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

$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);

?>
<html>
<head>
<title>Ankur Bhaskar Desai - Resume Registry a3530a3c</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<h1>Profile information</h1>
<p>First Name: <?php echo $f ?></p>
<p>Last Name: <?php echo $l ?></p>
<p>Email: <?php echo $e ?></p>
<p>Headline:<br> <?php echo $h ?></p>
<p>Summary:<br> <?php echo $s ?></p>
<p>Education:</p>
<ul>
    <?php
        $stmt1 = $pdo->prepare("SELECT year, name FROM Education 
                                JOIN Institution ON Education.institution_id = Institution.institution_id 
                                WHERE profile_id = :xyz ORDER BY rank");
        $stmt1->execute(array(":xyz" => $_GET['profile_id']));
        $rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        foreach ( $rows1 as $row1 ) {
            echo "<li>";
            echo htmlentities($row1['year']);
            echo(" - ");
            echo htmlentities($row1['name']);
            echo("</li>\n");
        }

    ?>
</ul>
<p>Positions:</p>
<ul>
    <?php

        $stmt1 = $pdo->prepare("SELECT * FROM position where profile_id = :xyz ORDER BY rank");
        $stmt1->execute(array(":xyz" => $_GET['profile_id']));
        $rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);//IMP - use fetchAll instead of fetch to get entire values
        foreach ( $rows as $row ) {
            echo "<li>";
            echo htmlentities($row['year']);
            echo(" - ");
            echo htmlentities($row['description']);
            echo("</li>\n");
        }

    ?>
</ul>
<a href="index.php">Done</a>
</div>
</body>
</html>