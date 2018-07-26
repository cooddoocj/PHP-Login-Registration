<?php
//login.php

// kết nối database
$db = new PDO("mysql:host=localhost;charset=utf8;dbname=test", "root", "pass");

// session_start
session_start();

// kiểm tra phiên đăng nhập
if (isset($_SESSION['user_name'])) {
	header('location: entry.php');
}


/*register*/
if (isset($_POST['register'])) {

	// thu thập mảng dữ liệu
	extract($_POST);

	// kiểm tra username
	if (!preg_match('/^[A-Za-z][A-Za-z0-9]{4,14}$/', $user_name)) {
		$errors[] = 'User không hợp lệ!';
	}

	// kiểm tra pass
	if (!preg_match('/^(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9!@#$%&*]{8,32}$/', $user_pass)) {
		$errors[] = 'Pass không hợp lệ!';
	}

	// kiểm tra username đã tồn tại chưa
	$query = "SELECT user_name FROM users WHERE user_name = :user_name";
	$stmt = $db->prepare($query);
	$stmt->execute(array(':user_name' => $user_name));
	if ($stmt->rowCount() > 0) {
		$errors[] = 'User đã tồn tại';
	}


	// hợp lệ điều kiện insert dữ liệu
	if (!isset($errors)) {

		$user_pass = password_hash($user_pass, PASSWORD_DEFAULT);

		$query = "INSERT INTO users (user_name, user_pass) VALUES (:user_name, :user_pass)";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
		':user_name' => $user_name,
		':user_pass' => $user_pass
		));

		$errors[] = 'Thành công';

	}


}


/*login*/
if (isset($_POST['login'])) {

	extract($_POST);

	if ($user_name == '') {
		$errors[] = 'Chưa nhập username';
	}

	if ($user_pass == '') {
		$errors[] = 'Chưa nhập password';
	}


	if (!isset($errors)) {

		$query = "SELECT * FROM users WHERE user_name = :user_name";
		$stmt = $db->prepare($query);
		$stmt->execute(array(':user_name' => $user_name));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		// kiểm tra xem user tồn tại không
		if ($stmt->rowCount() > 0) {

			// kiểm tra xem pass đúng không
			if (password_verify($user_pass, $user['user_pass'])) {

				$_SESSION['user_name'] = $user['user_name'];
				header('location: entry.php');

			}
			else
			{
				$errors[] = 'Password sai';
			}

		}
		else
		{
			$errors[] = 'User không tồn tại';
		}


	}


}

?>
<?php

if (isset($errors)) {
	foreach ($errors as $error) {
		echo "<p>$error</p>";
	}
}

?>
<?php if (isset($_GET['action']) == 'register') : ?>
<form method="post">
	<label>Username</label>
	<input type="text" name="user_name">
	<br>
	<label>Password</label>
	<input type="text" name="user_pass">
	<br>
	<input type="submit" name="register" value="Register">
	<br>
	<p>User dài 5 đến 15 ký tự, gồm a-z A-Z và 0-9, bắt đầu bằng chữ cái.</p>
	<p>Pass dài 8 đến 32 ký tự, có ít nhất một chữ cái, một chữ số và có thể sử dụng ký tự đặc biệt !@#$&*.</p>
	<p><a href="login.php">Login</a></p>
</form>

<?php else : ?>

<form method="post">
	<label>Username</label>
	<input type="text" name="user_name" value="<?php if (isset($errors)) { echo $user_name; } ?>">
	<br>
	<label>Password</label>
	<input type="text" name="user_pass">
	<br>
	<input type="submit" name="login" value="Login">
	<br>
	<p><a href="login.php?action=register">Register</a></p>
</form>

<?php endif; ?>

