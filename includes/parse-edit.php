<?php 
$errors = array();
$feedback = '';
$feedback_class = '';

//which post are we trying to edit?
//URL Will look like edit-post.php?post_id=X
$post_id = 0;
if(isset($_GET['post_id'])){
	$post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT);

	//make sure it isn't negative
	if($post_id < 0){
		$post_id = 0;
	}
}


//parse the form if they hit submit
if( isset( $_POST['did_edit'] ) ){
	//sanitize everything
	$title = trim(strip_tags($_POST['title']));
	$body = trim(strip_tags($_POST['body']));
	$category_id = filter_var( $_POST['category_id'], FILTER_SANITIZE_NUMBER_INT );
	//sanitize booleans
	if( isset($_POST['allow_comments']) ){
		$allow_comments = 1;
	}else{
		$allow_comments = 0;
	}

	if( isset($_POST['is_published']) ){
		$is_published = 1;
	}else{
		$is_published = 0;
	}
	
	//validate	
	$valid = true;
	//title blank or longer than 100
	if( $title == '' OR strlen($title) > 100 ){
		$valid = false;
		$errors['title'] = 'Title must be between 1 - 100 characters long.';
	}
	//body longer than 200	
	if( strlen($body) > 200 ){
		$valid = false;
		$errors['body'] = 'Caption must be less than 200 characters long.';
	}	
	//category must be positive int
	if( $category_id < 1 ){
		$valid = false;
		$errors['category_id'] = 'Choose a category from the dropdown.';
	}
	
	//if valid, update the post in the DB
	if($valid){
		//update the post
		$result = $DB->prepare('UPDATE posts
								SET title 			= :title,
									body 			= :body,
									category_id 	= :cat,
									allow_comments 	= :allow_comm,
									is_published	= :is_pub
								WHERE post_id 		= :post_id
								AND user_id 		= :user_id
								LIMIT 1 ');
		$result->execute( array(
								'title' 	=> $title,
								'body' 		=> $body,
								'cat'		=> $category_id,
								'allow_comm'=> $allow_comments,
								'is_pub' 	=> $is_published,
								'post_id'	=> $post_id,
								'user_id'	=> $logged_in_user['user_id']
						) );
		if( $result->rowCount() ){
			//success
			$feedback = 'Changes Saved.';
			$feedback_class = 'success';
		}else{
			//error - no changes happened
			$feedback = 'No changes were made to your post';
			$feedback_class = 'info';
		}
	}else{
		$feedback = 'Your post could not be saved. Fix the following:';
		$feedback_class = 'error';
	}
		
}//end if did edit


//Pre-fill the form values/security check 
//is the viewer of the page the author of this post? (if so, grab all the info to fill the form)
$result = $DB->prepare('SELECT * FROM posts
						WHERE post_id = :post_id
						AND user_id = :user_id
						LIMIT 1');
$result->execute( array(
					'post_id' => $post_id,
					'user_id' => $logged_in_user['user_id']
				) );
if( $result->rowCount() ){
	$row = $result->fetch();
	//convert array into vars like $title, $body, etc
	extract($row);
}else{
	//security! invalid user or post
	exit('You are not allowed to edit this post');
}