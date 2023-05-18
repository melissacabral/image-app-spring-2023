<?php
$feedback = '';
$feedback_class = '';

//if the form was submitted
if( isset( $_POST['did_comment'] ) ){
	//sanitize every field
	$body = trim(strip_tags( $_POST['body'] ));
	//validate
	$valid = true;
	//body max length is based off of VARCHAR(200) in DB structure
	if( $body == '' OR strlen($body) > 200  ){
		$valid = false;
	}
	//if valid, add the comment to the DB
	if( $valid ){
		$result = $DB->prepare('INSERT INTO comments
								( user_id, date, body, post_id, is_approved )
								VALUES 
								( :user, NOW(), :body, :post, 1 ) ');
		
		$result->execute( array(
			'user' => $logged_in_user['user_id'],
			'body' => $body,
			'post' => $post_id,
		) );

		//check if it worked
		if( $result->rowCount() ){
			//success
			$feedback = 'Thank you for your comment';
			$feedback_class = 'success';
		}else{
			//Insert error
			$feedback = 'Sorry, your comment could not be saved.';
			$feedback_class = 'error';
		}
	}else{
		//not valid
		$feedback = 'Invalid comment. Try again.';
		$feedback_class = 'error';
	}//end if valid
}//end parser