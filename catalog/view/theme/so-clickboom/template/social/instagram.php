<div class="instagram-gallery row">

</div>
<script type="text/javascript">
	var token = "<?php echo $_GET['instagram_token']; ?>", // learn how to obtain it below
    userid = "<?php echo $_GET['instagram_user']; ?>", // User ID - get it in source HTML of your Instagram profile or look at the next example :)
    num_photos = 9; // how much photos do you want to get
 
	$.ajax({
		url: 'https://api.instagram.com/v1/users/' + userid + '/media/recent', // or /users/self/media/recent for Sandbox
		dataType: 'jsonp',
		type: 'GET',
		data: {access_token: token, count: num_photos},
		success: function(data){
			
			for( x in data.data ){
				$('.instagram-gallery').append('<div class="instagram-item col-sm-4"> <a   href="' + data.data[x].link + '"><img src="'+data.data[x].images.thumbnail.url+'"></a></div>'); // data.data[x].images.low_resolution.url - URL of image, 306х306
				$('.instagram-gallery a').attr('target', '_blank');
				// data.data[x].images.thumbnail.url - URL of image 150х150
				// data.data[x].images.standard_resolution.url - URL of image 612х612
				// data.data[x].link - Instagram post URL 
			}
		},
		error: function(data){
			console.log(data); // send the error notifications to console
		}
	});

</script>