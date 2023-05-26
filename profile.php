<?php 
require('config.php'); 
require_once('includes/functions.php');
require('includes/header.php');

//whose profile is this?
if(isset($_GET['user_id'])){
	$user_id = filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT);
}elseif($logged_in_user){
	$user_id = $logged_in_user['user_id'];
}else{
	exit('Invalid User Account');
}

?>
<main class="content">
	<div class="flex three four-600 five-900">
		<?php 
		//get the user info
		$result = $DB->prepare('SELECT * FROM  users
			WHERE user_id = ?
			LIMIT 1'); 
		$result->execute(array($user_id));

		if( $result->rowCount() >= 1 ){			
			$row = $result->fetch();
			extract($row);		
			?>
			<section class="full author-profile">
				<?php show_profile_pic($profile_pic, 100, $username ); ?>
				<h2><?php echo $username ?></h2>
				<p><?php echo $bio; ?></p>
				<!-- <div class="flex" id="follow-info">
					<?php //follows_interface( $user_id ); ?>
				</div> -->
				<hr>
			</section>
			<?php
			
	//get this user's posts (left join so uncategorized posts are included)
	$query = 'SELECT *,  categories.name
				FROM posts
					LEFT JOIN  categories
					ON  categories.category_id = posts.category_id
				WHERE posts.user_id = ? ';
	//if viewing someone else's profile, hide the drafts
	if(  !$logged_in_user  OR $user_id != $logged_in_user['user_id']){
		$query .= ' AND posts.is_published = 1';
	}

	$query .= ' ORDER BY is_published ASC, date	DESC		
				LIMIT 20';	
			$result = $DB->prepare($query); 

			$result->execute(array($user_id));

			if( $result->rowCount() >= 1 ){			
				?>

				<?php
				while( $row = $result->fetch() ){
					extract($row);
					//handle the draft content
				if($is_published == 0){
					$class='draft';
					$title = 'Draft Post';
					$name = 'Uncategorized';
					$url = "edit-post.php?post_id=$post_id";
				}else{
					$class = 'public';
					$url = "single.php?post_id=$post_id";
				}	 
					
					?>
					<article class="post <?php echo $class ?>">
						<div class="card">
							<a href="<?php echo $url; ?>">
								<?php show_post_image( $image, 'medium' ) ?>

							</a>
							<footer>
							<h3><?php echo $title; ?></h3>	

							<span class="category"><?php echo $name; ?></span>
							<span class="date"><?php echo time_ago( $date ); ?></span>
							<span class="comment-count"><?php echo count_comments( $post_id ); ?></span>
						</footer>
						</div>		
					</article>
				<?php } //end while loop?>
				
			<?php }else{ ?>

				<div class="full feedback info">
					<p>This user hasn't posted any public images</p>
				</div>

				<?php 
		}//end if posts found 
	}else{
		echo 'Sorry, that user account doesn\'t exist';
	}?>
</div>
</main>
<?php 
require('includes/sidebar.php'); ?>
<?php
require('includes/footer.php');
?>	