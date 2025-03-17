'<?php
session_start();
?>

<html>
<p>Create an outfit</p>
</html>

<?php
if (!isset($_SESSION["username"])) {
	header("LOACTION:../index.php");
}
?>
