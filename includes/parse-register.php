<?php 
$errors = array();
$feedback = '';
$feedback_class = '';

//if the user submitted the register form
if( isset($_POST['did_register']) ){
	//sanitize everything
	$username = trim(strip_tags($_POST['username']));
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$password =  trim(strip_tags($_POST['password']));
	//"sanitize" a boolean
	if( isset($_POST['policy']) ){
		$policy = 1;
	}else{
		$policy = 0;
	}
	//validate
	$valid = true;

	//password tests
	if( strlen($password) < 8 ){
		$valid = false;
		$errors['password'] = 'Your password must be at least 8 characters.';
	}

	//username tests
	if( strlen($username) < 5 OR strlen($username) > 30 ){
		$valid = false;
		$errors['username'] = 'Your username must be between 5 - 10 characters long.';
	}else{
		//check if name is already taken. look this username up in the DB
		$result = $DB->prepare('SELECT username FROM users 
								WHERE username = ?
								LIMIT 1');
		$result->execute( array( $username ) );
		//check it
		if( $result->rowCount() ){
			//name is taken
			$valid = false;
			$errors['username'] = 'That username is already taken. Try another.';
		}
	} //end username tests

	//email tests
	if( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
		$valid = false;
		$errors['email'] = 'Provide a valid email address.';
	}else{
		//look up this email in the DB
		$result = $DB->prepare('SELECT email FROM users 
								WHERE email = ? 
								LIMIT 1');
		$result->execute( array( $email ) );
		if($result->rowCount()){
			$valid = false;
			$errors['email'] = 'Your email is already registered. Try logging in.';
		}
	} //end email tests

	//policy check
	if( ! $policy ){
		$valid = false;
		$errors['policy'] = 'You must agree to our terms before signing up';
	}

	//if valid, add the user to the DB
	if( $valid ){
		//salt and hash the password before storage
		$hashed_pass = password_hash($password, PASSWORD_DEFAULT);
		$result = $DB->prepare('INSERT INTO users
						(username, profile_pic, password, email, bio, is_admin, join_date)
						VALUES
						(:username, :pic, :pass, :email, :bio, 0, NOW())');
		$result->execute(array(
						'username' => $username,
						'pic' => '',
						'pass' => $hashed_pass, 
						'email' => $email,
						'bio' => ''
						));
		if($result->rowCount()){
			$feedback = 'Welcome! you are now a member of Finsta. Go log in!';
			$feedback_class = 'success';
		}else{
			$feedback = 'Something went wrong when adding your account. Try again';
			$feedback_class = 'error';
		}
	}else{
		//invalid form submission
		$feedback = 'Your registration is incomplete. Fix the following:';
		$feedback_class = 'error';
	}
	//handle feedback
}//end parser