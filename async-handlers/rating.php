<?php
/**
 * ASYNC Handler File for STAR RATING
 */

//load dependencies
require('../config.php');
require_once('../includes/functions.php');

$logged_in_user = check_login();
$user_id = $logged_in_user['user_id'];

//incoming data (from javascript fetch())
$post_id = filter_var($_REQUEST['postId'], FILTER_SANITIZE_NUMBER_INT);
$rating = filter_var($_REQUEST['rating'], FILTER_SANITIZE_NUMBER_INT);


//does that user like that post or not?
$result = $DB->prepare("SELECT * FROM ratings
                                WHERE user_id = ?
                                AND post_id = ?
                                LIMIT 1");
$result->execute( array( $user_id, $post_id ) );
if( $result->rowCount() >= 1 ){
	//the user previously rated this post. UPDATE the rating
	$query = "UPDATE ratings 
				SET rating = :rating
				WHERE user_id = :user_id
				AND post_id = :post_id";
}else{
	//the user didn't previously like it. ADD the like
	$query = "INSERT INTO ratings
				(user_id, post_id, rating, date)
				VALUES
				( :user_id, :post_id, :rating, now() )";
}

//run the resulting query
$result = $DB->prepare( $query );
$result->execute( array(
					'user_id' => $user_id,
					'post_id' => $post_id,
					'rating' => $rating
				) );

//update the like interface
star_interface( $post_id );
