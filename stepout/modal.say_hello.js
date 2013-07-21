;(function($){

	/* event binding
	------------------------------------------------------------------- */
	$('#say_hello').live('click', function(e){
		sendHello($(this), e);
	});
	
	/* helper functions
	------------------------------------------------------------------- */
	/**
	 * Calls the Hello API to send a message to users
	 * @see https://www.wrike.com/open.htm?id=10494905
	 */
	function sendHello(){
		var data = {
			'from_user_id':Session.user.id
		};
		API.create('Hello', data, function(r){
			switch(r.meta.status){
				case 204:
					ntfn('success','You have successfully announced your arrival to the Stepout Community!');
				break;
				case 500:
					ntfn('error','StepOut hiccuped! We were unable to announce your arrival at this moment.');
					logError('SayHelloMessageFailed', window.location.href, { user: Session.user.id, response: r});
				break;
				default:
					ntfn('error','StepOut hiccuped! We were unable to process your request.');
					logError('SayHelloFailed', window.location.href, { user: Session.user.id, response: r});
				break;
			}
			$('.closebox').click();
		});
	}

})(jQuery);