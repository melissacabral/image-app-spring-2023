<aside class="sidebar flex grow one two-500 three-800">
	<?php 
	$result = $DB->prepare('SELECT * 
							FROM users
							WHERE is_admin = 0
							LIMIT 5');
	$result->execute();
	if( $result->rowCount() ){ ?>
	<section class="users">
		<h2>Newest Users</h2>
		<?php while( $row = $result->fetch() ){ 
			user_info( $row['user_id'], $row['username'], $row['profile_pic']  ); 
		 } //end while ?>

	</section>
	<?php } //end users ?>

	<?php 
	$result = $DB->prepare('SELECT c.*, COUNT(*) AS count 
							FROM posts AS p, categories AS c
							WHERE p.category_id = c.category_id
							AND p.is_published = 1
							GROUP BY category_id
							ORDER BY RAND()
							LIMIT 20');
	$result->execute();
	if( $result->rowCount() ){ ?>
	<section class="categories">
		<h2>Categories</h2>
		<?php while ( $row = $result->fetch() ) { ?>
		<a href='#' class='pseudo button'><?php echo $row['name']; ?> 
			(<?php echo $row['count'] ?>)
		</a> 
		<?php } ?>

	</section>
	<?php } ?>
	<section class="meta">
		<h2>Fine Print</h2>
		<div>
			<a href="#" class="pseudo button">Terms of Service</a>
			<a href="#" class="pseudo button">About Finsta</a>
			<a href="#" class="pseudo button">Contact</a>
		</div>
	</section>
</aside>