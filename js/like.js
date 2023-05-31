//listen for a click on the heart-button
document.body.addEventListener( 'click', function(e){
	if(e.target.className == 'heart-button'){
		//console.log(e.target.dataset.postid, userId);
		likeUnlike(e.target);
	}
} );
//fetch has to be called from an async function. 
//el is the heart element that was clicked
async function likeUnlike( el ){
	
	const postId = el.dataset.postid;
	//the container that will be updated after liking
	const container = el.closest('.likes');

	//data that will be sent to the PHP handler
	let formData = new FormData();
	formData.append('postId', postId);	

	let response = await fetch( 'async-handlers/like-unlike.php', {
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