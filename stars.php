<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	
</head>
<body>


<?php 
$rating = 3.5;

//show 5 stars
for ($i = 1 ; $i <= 5 ; $i++) { 
	
	if( $i <= $rating ){
		//full
		$class = "fa-solid fa-star";
	}elseif( $rating < $i AND $rating > ($i - 1) ){
		//half
		$class="fa-solid fa-star-half-stroke";
	}else{
		//empty
		$class = "fa-regular fa-star";
	}
	echo "<i class='$class'></i>";
}


 ?>

</body>
</html>