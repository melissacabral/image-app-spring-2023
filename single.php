<?php 
/*
Template for a single post
*/
require_once( 'config.php' ); 
require_once( 'includes/functions.php' );

//get the ID of THIS post
//URL will be single.php?post_id=X
//sanitize and validate it
$post_id = 0;
if(isset($_GET['post_id'])){
	$post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT);

	//make sure it isn't negative
	if($post_id < 0){
		$post_id = 0;
	}
}

require( 'includes/header.php' );
require( 'includes/parse-comment.php' );
?>
<!--testing comment-->
<main class="content">
	<?php //Get all the info about THIS post
	$result = $DB->prepare('SELECT posts.*, categories.*, users.username, users.profile_pic
			FROM posts, users, categories
			WHERE posts.user_id = users.user_id
			AND posts.category_id = categories.category_id
			AND posts.is_published = 1
			AND posts.post_id = ?
			ORDER BY posts.date DESC
			LIMIT 1');
	//run it with the placeholder data
	$result->execute( array( $post_id ) );

	//check it
	if( $result->rowCount() ){
		//loop it
		while( $row = $result->fetch() ){
	?>
	<article class="post">
		<div class="card flex one two-700">
			<div class="post-image-header two-third-700">
				<?php show_post_image( $row['image'], 'large', $row['title']  ); ?>
			</div>
			<footer class="third-700">
				<div class="flex">
			<?php user_info( $row['user_id'], $row['username'], $row['profile_pic']  ); ?>

			<div class="likes">  
				<?php like_interface( $row['post_id'] ); ?>
			</div>
		</div>
				
				<h3><?php echo $row['title']; ?></h3>
				<p><?php echo $row['body']; ?></p>

				<div class="flex post-info">							
					<span class="date"><?php echo time_ago($row['date']); ?></span>	
					<span class="comment-count">
						<?php echo count_comments( $row['post_id'] ); ?>
					</span>
					<span class="category"><?php echo $row['name']; ?></span>		
				</div>
			</footer>
		</div>
	</article>
	<?php
			$allow_comments = $row['allow_comments'];
		} //end while 
		//load the comments on this post
		require('includes/comments.php');	
		//comment form if allowed
		if( $allow_comments AND $logged_in_user ){
			require('includes/comment-form.php');	
		}
	}else{
		echo '<h2>Post not found</h2>';
	} 
	?>
</main>		
<?php 
require('includes/sidebar.php');
require('includes/footer.php');
?>