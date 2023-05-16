<?php 
//get all the approved comments on THIS post, newest first
$result = $DB->prepare('SELECT comments.*, users.username, users.profile_pic
						FROM comments, users
						WHERE comments.user_id = users.user_id
						AND is_approved = 1
						AND post_id = ?
						ORDER BY date DESC
						LIMIT 30');
$result->execute( array( $post_id ) );
//are there comments?
$total = $result->rowCount();
if( $total ){
?>
<section class="comments">
	<h2><?php echo $total; ?> Comments on this post</h2>

	<?php while( $row = $result->fetch() ){ ?>
	<div class="card">
		<footer>
			<?php user_info( $row['user_id'], $row['username'], $row['profile_pic'] ); ?>

			<p><?php echo $row['body']; ?></p>
			<span class="date"><?php echo time_ago( $row['date'] ); ?></span>
		</footer>
	</div>
	<?php } //end while ?>
</section>
<?php } //end if comments exist ?>