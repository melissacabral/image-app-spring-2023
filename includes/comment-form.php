<section class="comment-form">
	<h2>Leave a comment</h2>

	<?php show_feedback( $feedback, $feedback_class ); ?>
	
	<form action="single.php?post_id=<?php echo $post_id; ?>" method="post">
		<label for="thebody">Your Comment</label>
		<textarea name="body" id="thebody"></textarea>

		<input type="submit" value="Comment">
		<input type="hidden" name="did_comment" value="1">
	</form>
</section>