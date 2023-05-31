//listen for a click on the star icons
document.body.addEventListener( 'click', function(e){
	if(e.target.classList.contains('star-icon') ){
		//console.log(e.target.dataset.rating);
		rateIt(e.target);
	}
} );
//fetch has to be called from an async function. 
//el is the heart element that was clicked
async function rateIt( el ){
	const postId = el.dataset.postid;
	const rating = el.dataset.rating;
	const container = el.closest('.rating');

	//data that will be sent to the PHP handler
	let formData = new FormData();
	formData.append('postId', postId);	
	formData.append('rating', rating);

	let response = await fetch( 'async-handlers/rating.php', {
		method	: 'POST', 
		body 	: formData
	} );
	if(response.ok){
		//success - update the container with the result
		let result = await response.text();
		container.innerHTML = result;
	}else{
		//error
		console.log(response.status);
	}

} //end likeUnlike function