<?php 
require_once 'config.php'; 
require_once 'includes/functions.php';
require 'includes/header.php';
//exit the page if not logged in
if( ! $logged_in_user ){
	exit('This page is for logged in users only');
}
?>
<main class="content">
	<?php require 'includes/parse-upload.php'; ?>
	<h2>Add New Post</h2>
	
	<?php show_feedback($feedback, $feedback_class, $errors); ?>

	<form action="new-post.php" method="post" enctype="multipart/form-data">
		<label>Upload a .jpg, .gif or .png image</label>
		<input type="file" name="uploadedfile" required accept="image/*">

		<input type="submit" value="Next: Add Post Details &rarr;">
		<input type="hidden" name="did_upload" value="1">
	</form>
</main>		
<?php 
require 'includes/sidebar.php';
require 'includes/footer.php';
?>