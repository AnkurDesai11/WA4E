
<?php
require_once "pdo.php";
session_start();
//$message=false;


if ( ! isset($_SESSION["name"]) ) { 
    die('ACCESS DENIED');
    error_log("ACCESS DENIED-Not logged in");
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
     && isset($_POST['headline']) && isset($_POST['summary'])) {

    $errorstatus=0;

    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['message']="All fields are required";
        error_log("Entry failed as not all values entered");
        $_SESSION['f'] = htmlentities($_POST['first_name']);
        $_SESSION['l'] = htmlentities($_POST['last_name']);
        $_SESSION['e'] = htmlentities($_POST['email']);
        $_SESSION['h'] = htmlentities($_POST['headline']);
        $_SESSION['s'] = htmlentities($_POST['summary']);
        $errorstatus=1;
        header("Location: add.php");
        return;
    }
    
    //strval($_POST['year']) !== strval(intval($_POST['year']))
    if( (strpos($_POST['email'], '@') !== false)==false ){
        $_SESSION['message']="Email must have an at-sign (@)";
        error_log("Entry failed as Email must have an at-sign (@)");
        $_SESSION['f'] = htmlentities($_POST['first_name']);
        $_SESSION['l'] = htmlentities($_POST['last_name']);
        $_SESSION['e'] = htmlentities($_POST['email']);
        $_SESSION['h'] = htmlentities($_POST['headline']);
        $_SESSION['s'] = htmlentities($_POST['summary']);
        $errorstatus=1;
        header("Location: add.php");
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
                header("Location: add.php");
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
                header("Location: add.php");
                return;
            }
            --$positionnos;
        }
    }

    if( $_POST['eduflag'] != 0 ) {
        $positionnos = (int)$_POST['eduflag'];
        $i=0;
        while ( $positionnos!=0 ){
            ++$i;
            if ( ! isset($_POST['yeare'.$i]) ) continue;
            if ( ! isset($_POST['name'.$i]) ) continue;
            $yeare = $_POST['yeare'.$i];
            $name = $_POST['name'.$i];
            if (strlen($yeare) < 1 || strlen($name) < 1){
                $_SESSION['message']="All fields are required";
                error_log("Entry failed as not all values entered");
                $_SESSION['f'] = htmlentities($_POST['first_name']);
                $_SESSION['l'] = htmlentities($_POST['last_name']);
                $_SESSION['e'] = htmlentities($_POST['email']);
                $_SESSION['h'] = htmlentities($_POST['headline']);
                $_SESSION['s'] = htmlentities($_POST['summary']);
                $errorstatus=1;
                header("Location: add.php");
                return;
            }
            if (! is_numeric($yeare)){
                $_SESSION['message']="Year must be numeric";
                error_log("Entry failed as Position year not numeric");
                $_SESSION['f'] = htmlentities($_POST['first_name']);
                $_SESSION['l'] = htmlentities($_POST['last_name']);
                $_SESSION['e'] = htmlentities($_POST['email']);
                $_SESSION['h'] = htmlentities($_POST['headline']);
                $_SESSION['s'] = htmlentities($_POST['summary']);
                $errorstatus=1;
                header("Location: add.php");
                return;
            }
            --$positionnos;
        }
    }

    if ( $errorstatus==0 ){
        
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
        
        $_SESSION['success']="Record added";
        error_log("Record inserted");

        $_SESSION['f'] = '';
        $_SESSION['l'] = '';
        $_SESSION['e'] = '';
        $_SESSION['h'] = '';
        $_SESSION['s'] = '';

        $profile_id = $pdo->lastInsertId();
        $positions = (int)$_POST['flagtest'];
        $i=0;
        $rank=1;      
        while ( $positions!=0 ) {
            ++$i;
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
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
        $i=0;
        $rank=1;
        $positions = (int)$_POST['eduflag'];
        while ( $positions!=0 ) {
            ++$i;
            if ( ! isset($_POST['yeare'.$i]) ) continue;
            if ( ! isset($_POST['name'.$i]) ) continue;
            $yeare = $_POST['yeare'.$i];
            $name = $_POST['name'.$i];
            $stmt1 = $pdo->prepare("SELECT institution_id FROM institution where name = :abc");
            $stmt1->execute(array(":abc" => $name));
            $institution = $stmt1->fetch(PDO::FETCH_ASSOC);
            $institution = reset($institution);
            if(!$institution){
                $stmt2 = $pdo->prepare("INSERT INTO institution (name) VALUE (:name)");
                $stmt2->execute(array(":name" => $name));
                $institution = $pdo->lastInsertId();
            }
            $stmt = $pdo->prepare('INSERT INTO education
                    (profile_id, institution_id, rank, year)
                    VALUES ( :pid, :iid, :rank, :yeare)');
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':iid' => $institution,
                ':rank' => $rank,
                ':yeare' => $yeare)
            );
            ++$rank;
            --$positions;
        }

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
<input type="text" name="first_name" size="60" value="<?= $_SESSION['f'] ?>"></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?= $_SESSION['l'] ?>"></p>
<p>Email:
<input type="text" name="email" size="30" value="<?= $_SESSION['e'] ?>"></p>
<p>Headline:<br>
<input type="text" name="headline" size="80" value="<?= $_SESSION['h'] ?>"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80" style="width: 547px; height: 169px;"><?php echo $_SESSION['s'] ?></textarea></p>
<p>Education: <input type='button' id='addEdu' value='+'>
<div id="education_fields">
</div>
</p>
<p>
<p>Position: <input type='button' id='addPos' value='+'>
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" value="Add">
<input type="button" onclick="window.location.href='index.php';" value="Cancel" />
<input type="hidden" name='flagtest' id='flagtest' value='0'/>
<input type="hidden" name='eduflag' id='eduflag' value='0'/>
</p>
</form>
<script>
countPos=0;
countEdu=0;
    $(document).ready(function(){
        $(addPos).click(function(event){
            event.preventDefault();
            if(countPos >= 9){
                alert("Maximum of nine position entries allowed");
                return;
            }
            countPos++;
            document.getElementById('flagtest').value=countPos;
            window.console && console.log("Adding position "+countPos);
            $("#position_fields").append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                <input type="button" value="-" \
                    onClick="countPos--; document.getElementById(\'flagtest\').value=countPos; $(\'#position'+countPos+'\').remove();return false;"></p> \
                <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                </div>');
            }
        );
        $(addEdu).click(function(event){
            event.preventDefault();
            if(countEdu >= 9){
                alert("Maximum of nine education entries allowed");
                return;
            }
            countEdu++;
            document.getElementById('eduflag').value=countEdu;
            window.console && console.log("Adding position "+countEdu);
            $("#education_fields").append(
                '<div id="education'+countEdu+'"> \
                <p>Year: <input type="text" name="yeare'+countEdu+'" value="" /> \
                <input type="button" value="-" \
                    onClick="countEdu--; document.getElementById(\'eduflag\').value=countEdu; $(\'#education'+countEdu+'\').remove();return false;"></p> \
                <p>School: <input type="text" name="name'+countEdu+'" class="school" size="80"></p>\
                </div>');

            $('.school').autocomplete({source: "school.php"});
            }
        );
        $('.school').autocomplete({source: "school.php"});
    });
</script>
</div>
</body>