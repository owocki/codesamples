$(function() {
	/**
	* !- config - allows systems admins to set longpolling features live / dead.
	* (note this must be equal to the vals in Library/LongPoll.php )
	 */
	
	/**
	 * high perf impact - 'other user is typing'
	 */
	document.CHAT_USERSTATUSINDICATORS_ENABLED = 1; 

	/**
	 * low perf impact - 'push chat messages to other client'\
	 */
	document.CHAT_PUSHMESSAGES_ENABLED = 1;

	/**
	 * medium  perf impact - push 'buddy online_status changes to each person on each of their buddy lists'
	 */
	document.CHAT_PUSHBUDDYLISTREFRESHES_ENABLED = 1; 
	
	/**
	 * this is a timeout value.  if the user has been inactive for more then POLL_USERACTIVITYTIMEOUT s, we stop making long poll requests.
	 */
	document.POLL_USERACTIVITYTIMEOUT = 60 * 3; 

	// ! - general helper fns
	
	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: 
	 *  @return returns true if the long poller should be initialized on this page
	 *  
	 *  
	 */
	var shouldInitializeLongPoller = function(){
		return isAuth() && ( $("#page_register").length < 1 ) && true;
	}

	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: 
	 *  @return the # seconds since the last user action
	 *  
	 *  
	 */
	var timeSinceLastUserAction = function(){
		if(typeof document.lastUserActivityTime == 'undefined')
			return -1;

	     return Math.round(new Date().getTime() / 1000) - document.lastUserActivityTime ;
	};	
	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: updates lastUserActivityTime to the latest timestamp
	 *  @return 
	 *  
	 *  
	 */
	var updateLastUserActivityTime = function(){
	    document.lastUserActivityTime = Math.round(new Date().getTime() / 1000);
	};
	$('html').mousemove(updateLastUserActivityTime);
	$('html').live('keydown',updateLastUserActivityTime);
	updateLastUserActivityTime();


	// ! - class def

	function LongPoller(){
		this.enabled = true;
		this.windowFocused = true;
		this.requesting = false;
   		this.lastRequestValid = true;
   		this.lastRequestTime = false;
   		this.lastRequestUnauthenticated = false;
   		this.RETRY_INTERVAL = 500;
   		this.MIN_TIME_BETWEEN_REQUESTS = 300;
	}
	
	// ! - class vars
	
	LongPoller.prototype.windowFocused = false;
	LongPoller.prototype.lastRequestValid = false;
	LongPoller.prototype.lastRequestTime = false;
	LongPoller.prototype.lastRequestUnauthenticated = false;
	LongPoller.prototype.enabled = false;
	LongPoller.prototype.requesting = false;
	LongPoller.prototype.RETRY_INTERVAL = false;
	LongPoller.prototype.MIN_TIME_BETWEEN_REQUESTS = false;

	// ! - class fns
	
	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: initializes the class
	 *  @return 
	 *  
	 *  
	 */
	LongPoller.prototype.init = function(){
		document.longPoller.watchPoller();
	};
	
	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: watches the polling script, sends a request if shouldSendAnotherReauest
	 *  @return 
	 *  
	 *  
	 */
	LongPoller.prototype.watchPoller = function(){
    		
    		var shouldSendAnotherRequest = this.enabled && !this.lastRequestUnauthenticated && this.windowFocused && !this.requesting && ( timeSinceLastUserAction() < document.POLL_USERACTIVITYTIMEOUT );
    		var TIME_UNTIL_NEXT_REQUEST = this.RETRY_INTERVAL;
    		if( this.lastRequestTime && this.lastRequestTime < 0.5 ){
    			TIME_UNTIL_NEXT_REQUEST = 1000;
    		}

    		if(shouldSendAnotherRequest){
	   			setTimeout(function(){
	   				document.longPoller.poll();
	   			}, (TIME_UNTIL_NEXT_REQUEST - 100 ) );
	   		} 
   			
   			setTimeout(function(){
   				document.longPoller.watchPoller();
   			},TIME_UNTIL_NEXT_REQUEST  );
	};
	
	/**
	 *  @param  uid - a user UID to push a poll value to, key, the key / values to push to the user
	 *  Preconditions: 
	 *  Postconditions: 
	 *  @return 
	 *  
	 *  
	 */
	LongPoller.prototype.push = function(uid,key,val){
    	
    	if(!this.enabled)
    		return null;
    		
    	//set up request
    	var pushURL = urlHelper('LongPoll.php','',{
    		type : 'push',
    		uid : uid,
    		key : key,
			val : val,
			api_auth_id: API.config.params.auth_id,
			api_auth_token: API.config.params.auth_token
		}).replace('.php/','.php').replace('//', '//poll.').replace('//poll.www.', '//poll.');

    	var callback = function(response){};

    	$.ajax( {
    		global: false,
    		url: pushURL,
    		success: callback
    		}
    	);	
    }
	
	/**
	 *  @param  
	 *  Preconditions: 
	 *  Postconditions: 
	 *  @return 
	 *  
	 *  polls the server for a new response, and recursively calls itself until the page is closed or user goes to new page
	 */
	LongPoller.prototype.poll = function(){
    	
    	if(!this.enabled)
    		return null;
    		
    	//set up request
    	var pollURL = urlHelper('LongPoll.php','',{
    		type : 'poll',
			time: Math.round(new Date().getTime() / 1000),
			api_auth_id: API.config.params.auth_id,
			api_auth_token: API.config.params.auth_token
		}).replace('.php/','.php').replace('//', '//poll.').replace('//poll.www.', '//poll.');
    	this.requesting = true;
    	
    	//set up the callback function 
    	var callback = function(response){
    		
    		if(typeof response =='string')
    			response = $.parseJSON(response);
    		
    		//validate response
    		if( typeof response != 'undefined' && response.status == "OK" ){
    			
    			//pass responses into callback function
	    		var payloads = response.payloads;
	    		
	    		$.each(payloads,function(i,thisPayload){
	    			
		    		document.longPoller.generalCallback(thisPayload);

	    		});
	    		
    		}
    		
    		// recursively begin polling again
    		document.longPoller.requesting = false;
    		document.longPoller.lastRequestTime = response.meta.time;
    		document.longPoller.lastRequestValid = typeof response != 'undefined' && response.status == "OK" || response.status == "NONE" ;
    		document.longPoller.lastRequestUnauthenticated = typeof response != 'undefined' && response.meta.authenticated == false;
    		
    		if( !document.longPoller.lastRequestValid ){
	    	
	    		logError('invalidLongPollResponse',response);
	    		
	    	}
    	};

    	//make the request
    	$.ajax({
    		global: false,
    		url: pollURL,
    		success: callback
    	});
    	
    };


	/**
	 *  @param  someData - a json array, with 'key' and 'args';
	 *  Preconditions: 
	 *  Postconditions: performs thte appropriate action on the callback key
	 *  @return 
	 *  
	 *  
	 */
	LongPoller.prototype.generalCallback = function(someData){
			

			if(!someData || typeof someData == 'undefined' )
				return;

			// someData will have both a key an args
			var key = someData.key;
			var args = someData.args;
			
			switch(key){
				
				case 'refreshchatmessages':
					
					// Tell Chat Disdpatcher that messages need a refresh
					
					if( typeof document.chatDispatcher == 'undefined' )
						return false;
					
					if(document.CHAT_PUSHMESSAGES_ENABLED)
						document.chatDispatcher.processPulledMessage(args);
					
				break;
			
				case 'refreshbuddies':
					
					if( typeof document.chatDispatcher == 'undefined' )
						return false;

					if( typeof args == 'undefined')
						return false;

					// Tell Chat Disdpatcher that some of the buddy lists need a refresh
					if(document.CHAT_PUSHBUDDYLISTREFRESHES_ENABLED)
						document.chatDispatcher.changeBuddyStatus( args.type , args.uid, args.online_status, args.User );
					
				break;
				case 'newannouncement':
					var key = 'announcement.' + args.name + '.shown';
					if( !amplify.store(key) || typeof amplify.store(key) === undefined ) {
						var sticky = args.life == 0 ? true : false;

						// This specifies how many messages can be pooled at a time.
						$.jGrowl.defaults.pool = 5;

						$.jGrowl(args.text,{header:args.header,sticky:sticky, life:args.life});

						//Keep it in localstorage for 12 hours
						amplify.store(key, true, { expires: 60000*60*12 });

						soPanel.track(soPanel.REVENUE_TESTING, 'Announcement Received', args);
						$(".jGrowl-message").die().live('click',function(e){
							soPanel.track(soPanel.REVENUE_TESTING, 'Announcement clicked', args);
						}).live('mouseenter',function(e){
							soPanel.track(soPanel.REVENUE_TESTING, 'Announcement hovered', args);
						});
					}


				break;
				case 'messageupdate':
					
					if( typeof document.chatDispatcher == 'undefined' )
						return false;
					
					switch(args.type){
						case 'sending':
						case 'typing':
						case 'nottyping':
						case 'readmessage':
						case 'multireadmessage':
								
						if(document.CHAT_USERSTATUSINDICATORS_ENABLED)
							document.chatDispatcher.refreshMessages(args);
							
						break;
						default:
							
							//unrecognized message type
							
						break;
					}
					
				break;
				case 'newcentrestage':

				if(typeof document.pullCentreStage != 'undefined')
					document.pullCentreStage(args.User);

				break;
				case 'newnotification':
					if(typeof API == 'object'){
						var notification = API.construct('Notification', args.notification);
						notification.trigger('read');
					}
				break;

				case 'newcentrestagephoto':
					
					//tell centrestage that we have a new photo for a user on CS
					amplify.publish('newcentrestagephoto');

				break;

				case 'centrestagephotodeleted':
					
					//tell centrestage that we have a photo on CS that has been delted
					amplify.publish('centrestagephotodeleted');

				break;

				default:
					
				break;
			
			}
			
		};
		
		
		if(shouldInitializeLongPoller()){
			document.longPoller = new LongPoller();
			document.longPoller.init();
			
		} else {

			document.CHAT_USERSTATUSINDICATORS_ENABLED = 0; 
			document.CHAT_PUSHMESSAGES_ENABLED = 0; 
			document.CHAT_PUSHBUDDYLISTREFRESHES_ENABLED = 0; 


		}
			
});