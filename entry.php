<?php  
//entry.php  

// session_start
session_start();

if (!isset($_SESSION['user_name'])) {
	header('location: login.php');
}

?>
<h1>Chào mừng - <?php echo $_SESSION['user_name'] ?></h1>
<p><a href="logout.php">Đăng xuất</a></p>