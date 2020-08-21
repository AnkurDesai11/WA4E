
<?php
require_once "pdo.php";
session_start();
//$message=false;
//$status=0;

if ( ! isset($_SESSION["name"]) ) { 
    die('ACCESS DENIED');
    error_log("ACCESS DENIED-Not logged in");
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
     && isset($_POST['headline']) && isset($_POST['summary'])) {

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['message']="All fields are required";
        error_log("Entry failed as not all values entered");
        header("Location: add.php");
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    else if( (strpos($_POST['email'], '@') !== false)==false ){
        $_SESSION['message']="Email must have an at-sign (@)";
        error_log("Entry failed as Email must have an at-sign (@)");
        header("Location: add.php");
        return;
    }

    else {
        
        $stmt = $pdo->prepare('INSERT INTO Profile
                (user_id, first_name, last_name, email, headline, summary)
                VALUES ( :uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
        );
        $_SESSION['success']="Record Added";
        
        error_log("Record inserted");
        
        header("Location: index.php");
        return;
    }   
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
<h1>Adding Profile for <?php echo $_SESSION['name'] ?></h1>
<?php
    
    if( isset($_SESSION['message'])){
        echo("<p style='color:red'>".$_SESSION['message']."</p>\n");
        unset($_SESSION['message']);
    }
    if( isset($_SESSION['success'])){ 
        echo("<p style='color:green'>".$_SESSION['success']."</p>\n");
        unset($_SESSION['success']);
    }
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"></p>
<p>Last Name:
<input type="text" name="last_name" size="60"></p>
<p>Email:
<input type="text" name="email" size="30"></p>
<p>Headline:<br>
<input type="text" name="headline" size="80"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80" style="width: 547px; height: 169px;"></textarea>
</p><p>
<input type="submit" value="Add">
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
</p>
</form>
</div>
</body>