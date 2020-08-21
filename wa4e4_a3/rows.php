<?php
require_once "pdo.php";
session_start();
$stmt1 = $pdo->prepare("SELECT * FROM position where profile_id = :xyz ORDER BY rank");
$stmt1->execute(array(":xyz" => 12));
$rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);//IMP - use fetchAll instead of fetch to get entire values
$rows_encode = json_encode($rows);
?>
<script>
    var position_rows = <?php echo $rows; ?>;
    document.write(position_rows.0.['rank']);
</script>
