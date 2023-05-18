<?php
//pre-define vars
$feedback = '';
$feedback_class = '';
$errors = array();


//if they're trying to log out, destroy the session and cookies
if( isset( $_GET['action'] ) AND $_GET['action'] == 'logout' ){
	//expire the cookies
	setcookie( 'access_token', 0, time() - 99999 );
	setcookie( 'user_id', 0, time() - 99999 );

	//destroy the session
	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}
	// Finally, destroy the session.
	session_destroy();
	//redirect to force a page reload so the cookies don't "stick"
	header('Location:login.php');
}//end logout


//process the data if submitted
if( isset($_POST['did_login']) ){
    //sanitize everything
    $username = trim(strip_tags($_POST['username']));
    $password = trim(strip_tags($_POST['password']));
    
    //validate
    $valid = true;

    //password tests
    if( strlen($password) < 8 ){
        $valid = false;
    }

    //username tests
    if( strlen($username) < 5 OR strlen($username) > 30 ){
        $valid = false;
    }
    //if valid, check the DB
    if( $valid ){
        //look up the username in the DB
        $result = $DB->prepare('SELECT user_id, password
                                FROM users
                                WHERE username = ?
                                LIMIT 1');
        $result->execute( array($username) );
        //if we found a username, verify the password
        if( $result->rowCount() ){
            $row = $result->fetch();
            //check against the hashed password from the DB
            if(password_verify( $password, $row['password'] )){
                //success! log them in
                $feedback = 'You are now logged in';
                $feedback_class = 'success';
                //generate the access token (60 chars = 30 * 2)
                $access_token = bin2hex(random_bytes(30));
                //store it in the DB
                $result = $DB->prepare('UPDATE users
                						SET access_token = :token
                						WHERE user_id = :id ');
                $result->execute( array(
                	'token' => $access_token,
                	'id' 	=> $row['user_id']
                ) );
                //store it as a cookie and session
                $exp = time() + 60 * 60 * 24 * 7;
                setcookie( 'access_token', $access_token, $exp );
                //hash the user ID for cookie storage
                $hashed_id = password_hash($row['user_id'], PASSWORD_DEFAULT);
                setcookie('user_id', $hashed_id, $exp);

                //store sessions to match the cookies
                $_SESSION['access_token'] = $access_token;
                $_SESSION['user_id'] = $hashed_id;
            }else{
                $feedback = 'Wrong password.';
                $feedback_class = 'error';
            }
        }else{
            //username not found
            $feedback = 'That user does not exist.';
            $feedback_class = 'error';
        }
    }else{
        //invalid form
        $feedback = 'Incorrect Username or Password.';
        $feedback_class = 'error';
    }
} //end form parser