<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Finsta - Image Sharing</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picnic">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" type="image/x-icon" sizes="32x32" href="favicon.ico">
</head>
<body>
	<div class="site">
		<header class="header">
			<nav>
				<a href="index.php" class="brand">
					<img class="logo" src="images/logo-color.png" />
					<span>Finsta</span>
				</a>

				<!-- responsive-->
				<input id="menu-button" type="checkbox" class="show">
				<label for="menu-button" class="burger pseudo button">&#9776;</label>

				<div class="menu">
					<a href="register.php">Sign Up</a>
				                       
				    <form action="search.php" method="get" class="searchform">
				        <input type="search" name="phrase" placeholder="Search">
				        <input type="submit" value="search">
				    </form>

				</div>  				
			</nav>
		</header>