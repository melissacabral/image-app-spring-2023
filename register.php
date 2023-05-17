<?php 
require 'config.php'; 
require_once 'includes/functions.php';
require 'includes/parse-register.php';
//doctype and visible header
require 'includes/header.php' ;
?>
<main class="content">
	<div class="important-form">
	<h1>Create an Account</h1>

	<?php show_feedback($feedback, $feedback_class, $errors); ?>

	<form method="post" action="register.php">
		<label>Username:</label>
		<input type="text" name="username">

		<label>Email Address:</label>
		<input type="email" name="email">

		<label>Password:</label>
		<input type="password" name="password">

		<label>
			<input type="checkbox" name="policy" value="1">
			<span class="checkable">
				I agree to the <a href="#" target="_blank">terms of use and privacy policy</a>
			</span>
		</label>

		<input type="submit" value="Sign Up">
		<input type="hidden" name="did_register" value="1">
	</form>
	</div>
</main>

<?php include('includes/footer.php'); ?>