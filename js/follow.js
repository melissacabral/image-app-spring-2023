//Follow interaction
document.body.addEventListener( 'click', function(e){
	if(e.target.classList.contains('follow-button')){
		follow(e.target);
	}
} );

async function follow( el ){
	let to = el.dataset.to;
	console.log(to);

	let data = new FormData();
	data.append( 'to', to );

	let response = await fetch( 'async-handlers/follow.php', {
		method : 'POST',
		body: data
	});
	if( response.ok ){
		let output = await response.text();
		//update the container div
		document.getElementById('follow-info').innerHTML = output;
	}else{
		console.log(response.status);
	}
}