<?php
require_once "pdo.php";
session_start();
$logflag=-1;
if ( ! isset($_SESSION["name"]) ) { 
    $logflag=0;
    error_log("Not logged in");
}
else{
    $logflag=1;
}
?>
<html><head>
<title>Ankur Bhaskar Desai - Resume Registry a3530a3c</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<h2>Welcome to Ankur Bhaskar Desai's Resume Registry</h2>
<?php
    
    if( isset($_SESSION['message'])) {
        echo("<p style='color:red'>".$_SESSION['message']."</p>\n");
        unset($_SESSION['message']);
    }
    if( isset($_SESSION['success'])){ 
        echo("<p style='color:green'>".$_SESSION['success']."</p>\n");
        unset($_SESSION['success']);
    }

    if($logflag==0){
        echo "<a href='login.php'>Please log in</a>";
        //echo '<p>Attempt to <a href="add.php">add data</a> without logging in</p>';
    }
    if($logflag==1){
        echo("<p><a href='logout.php'>Logout</a></p>");
    }
    //if($logflag==1){
    $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id, user_id FROM profile");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( !$rows ) {
            echo "<p>No rows found</p>";
    }
    else {
        echo('<table border="1">'."\n");
        echo "<thead><tr>";
        echo "<th>Name</th>";
        echo "<th>Headline</th>";
        if($logflag==1){
            echo "<th>Action</th>";
        }
        echo "</tr></thead>";
        echo "<tbody>";
        foreach ( $rows as $row )  {
            echo "<tr><td>";
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td>");
            if($logflag==1){
                echo("<td>");
                if($_SESSION['user_id']==$row['user_id']){
                    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                }
                echo("</td>");
            }
            echo("</tr>\n");
        }
        echo("</tbody></table>");
    }
    if($logflag==1){
    echo("<p><a href='add.php'>Add New Entry</a></p>");
    }
    
    //}
    if($logflag==-1){
        echo("<p>Internal Error</p>");
        error_log("Internal Error -> logflag=-1");
    }
?>
</div>

</body></html>