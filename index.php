<?php 
require_once( 'config.php' ); 
require_once( 'includes/functions.php' );
require( 'includes/header.php' );
?>
<main class="content">
	<div class="posts-container flex one two-600 three-900">
		<?php 
		//get all the published posts from the DB
		//1. write it (prepare the statement)
		$result = $DB->prepare('SELECT posts.*, categories.*, users.username, users.profile_pic
			FROM posts, users, categories
			WHERE posts.user_id = users.user_id
			AND posts.category_id = categories.category_id
			AND posts.is_published = 1
			ORDER BY posts.date DESC
			LIMIT 20'); 
		//2. run it (execute)
		$result->execute();

		//3. check it (were any posts found?)
		if( $result->rowCount() ){
		//4. loop it
			while( $row = $result->fetch() ){
				//print_r($row);
				
?>
	<article class="post">
		<div class="card">
			<div class="post-image-header">
				<a href="single.php?post_id=<?php echo $row['post_id']; ?>">
					<?php show_post_image( $row['image'], 'medium', $row['title']  ); ?>
				</a>

				<?php edit_post_button( $row['post_id'], $row['user_id'] ); ?>
			</div>
			<footer>
				<div class="post-header">
			<?php user_info( $row['user_id'], $row['username'], $row['profile_pic']  ); ?>
				</div> <!-- post header -->

				<h3 class="post-title clamp"><?php echo $row['title']; ?></h3>
				<p class="post-excerpt clamp"><?php echo $row['body']; ?></p>
				<div class="flex post-info">							
					<span class="date"><?php echo time_ago($row['date']); ?></span>	
					<span class="comment-count">
						<?php echo count_comments( $row['post_id'] ); ?>
					</span>
					<span class="category"><?php echo $row['name']; ?></span>		
				</div>
			</footer>
		</div><!-- .card -->
	</article> <!-- .post -->
		<?php 
			} //end while
		}else{
			echo '<h2>No posts to show</h2>';
		} 
		?>

	</div><!-- .posts-container -->
</main>		
<?php 
require('includes/sidebar.php');
require('includes/footer.php');
?>