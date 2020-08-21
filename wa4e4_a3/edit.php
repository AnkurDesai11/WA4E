<?php
require_once "pdo.php";
session_start();

$initialpos=0;

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
$stmt1 = $pdo->prepare("SELECT * FROM position where profile_id = :xyz ORDER BY rank");
$stmt1->execute(array(":xyz" => $_GET['profile_id']));
$rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);//IMP - use fetchAll instead of fetch to get entire values

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
     && isset($_POST['headline']) && isset($_POST['summary'])) {

    $errorstatus=0;

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        //$message = "Make is required";
        $_SESSION['message']="All fields are required";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as not all values entered");
        $errorstatus=1;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    if( (strpos($_POST['email'], '@') !== false)==false ){
        //$message = "Email must have an at-sign (@)";
        $_SESSION['message']="Email must have an at-sign (@)";
        //$status=1;
        $_SESSION['status']=1;
        error_log("Entry failed as Email must have an at-sign (@)");
        $errorstatus=1;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    if( $_POST['flagtest'] != 0 ) {
        $positionnos = (int)$_POST['flagtest'];
        $i=0;
        while ( $positionnos!=0 ){
            ++$i;
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            if (strlen($year) < 1 || strlen($desc) < 1){
                $_SESSION['message']="All fields are required";
                error_log("Entry failed as not all values entered");
                $_SESSION['f'] = htmlentities($_POST['first_name']);
                $_SESSION['l'] = htmlentities($_POST['last_name']);
                $_SESSION['e'] = htmlentities($_POST['email']);
                $_SESSION['h'] = htmlentities($_POST['headline']);
                $_SESSION['s'] = htmlentities($_POST['summary']);
                $errorstatus=1;
                header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
                return;
            }
            if (! is_numeric($year)){
                $_SESSION['message']="Year must be numeric";
                error_log("Entry failed as Position year not numeric");
                $_SESSION['f'] = htmlentities($_POST['first_name']);
                $_SESSION['l'] = htmlentities($_POST['last_name']);
                $_SESSION['e'] = htmlentities($_POST['email']);
                $_SESSION['h'] = htmlentities($_POST['headline']);
                $_SESSION['s'] = htmlentities($_POST['summary']);
                $errorstatus=1;
                header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
                return;
            }
            --$positionnos;
        }
    }

    if ( $errorstatus==0 ) {
        
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
        error_log("Record Updated");

        $profile_id = $_REQUEST['profile_id'];
        $positions = (int)$_POST['flagtest'];
        $positions_old = sizeof($rows);
        $j=0;
        $rank=1;
        if( $positions == $positions_old){  
            while ( $positions!=0 ) {
                ++$j;
                if ( ! isset($_POST['year'.$j]) ) continue;
                if ( ! isset($_POST['desc'.$j]) ) continue;
                $year = $_POST['year'.$j];
                $desc = $_POST['desc'.$j];
                $stmt = $pdo->prepare('UPDATE position SET 
                    year=:year, description=:desc
                    WHERE profile_id = :pid AND rank=:rank');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                ++$rank;
                --$positions;
            }
        }
        else if( $positions >= $positions_old){  
            $positions = $positions-$positions_old;
            while ( $positions_old!=0 ) {
                ++$j;
                if ( ! isset($_POST['year'.$j]) ) continue;
                if ( ! isset($_POST['desc'.$j]) ) continue;
                $year = $_POST['year'.$j];
                $desc = $_POST['desc'.$j];
                $stmt = $pdo->prepare('UPDATE position SET 
                    year=:year, description=:desc
                    WHERE profile_id = :pid AND rank=:rank');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                ++$rank;
                --$positions_old;    
            }
            while ( $positions!=0 ) {
                ++$j;
                if ( ! isset($_POST['year'.$j]) ) continue;
                if ( ! isset($_POST['desc'.$j]) ) continue;
                $year = $_POST['year'.$j];
                $desc = $_POST['desc'.$j];
                $stmt = $pdo->prepare('INSERT INTO position
                    (profile_id, rank, year, description)
                    VALUES ( :pid, :rank, :year, :desc)');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                ++$rank;
                --$positions;
            }
        }
        
        else{
            while ( $positions!=0 ) {
                ++$j;
                if ( ! isset($_POST['year'.$j]) ) continue;
                if ( ! isset($_POST['desc'.$j]) ) continue;
                $year = $_POST['year'.$j];
                $desc = $_POST['desc'.$j];
                $stmt = $pdo->prepare('UPDATE position SET 
                    year=:year, description=:desc
                    WHERE profile_id = :pid AND rank=:rank');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                ++$rank;
                --$positions;
            }
            $sql = "DELETE FROM position WHERE profile_id = :pid AND rank >= :rank";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':pid' => $_REQUEST['profile_id'], ':rank' => $rank));
        }

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

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

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
</p>
<p>Position: <input type='button' id='addPos' value='+'>
<div id="position_fields">
<?php
    foreach ( $rows as $row ){
        $initialpos++;
        echo('<div id="position'.$initialpos.'">');
        echo('<p>Year: <input type="text" name="year'.$initialpos.'" value="'.$row['year'].'" /> ');
        echo('<input type="button" value="-" onClick="countPos--; document.getElementById(\'flagtest\').value=countPos; $(\'#position'.$initialpos.'\').remove();return false;"></p>');
        echo('<textarea name="desc'.$initialpos.'" rows="8" cols="80">'.$row['description'].'</textarea>');
        echo('</div>'); 
    }
?>
</div>
</p>
<p>
<input type="submit" value="Save">
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
<input type="hidden" name='flagtest' id='flagtest' value=<?php echo $initialpos?> />
</p>
</form>
<script>
countPos=<?php echo $initialpos?>;
posId=countPos+10;
    $(document).ready(function(){
        $(addPos).click(function(event){
            event.preventDefault();
            if(countPos >= 9){
                alert("Maximum of nine position entries allowed");
                return;
            }
            countPos++;
            document.getElementById('flagtest').value=countPos;
            window.console && console.log("Adding position "+posId);
            $("#position_fields").append(
                '<div id="position'+posId+'"> \
                <p>Year: <input type="text" name="year'+posId+'" value="" /> \
                <input type="button" value="-" \
                    onClick="countPos--; document.getElementById(\'flagtest\').value=countPos; $(\'#position'+posId+'\').remove();return false;"></p> \
                <textarea name="desc'+posId+'" rows="8" cols="80"></textarea>\
                </div>');
            }
        );
    });
</script>
</div>
</body>

