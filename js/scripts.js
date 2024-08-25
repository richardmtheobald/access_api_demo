var user = {};

function ajaxCall(ajaxUrl,ajaxData,callBack,thing){
	try {
		var b = $.parseJSON(ajaxData);
		// ajaxData is a JSON object; it needs to not be one as our API isn't smart enough to convert it
		ajaxData = $.param(ajaxData);
	} catch (exception) {
		// ajaxData isn't a JSON object; assume it is correct
	}
	$.ajax({
		url: ajaxUrl,
		type: 'post',
		data: ajaxData
	}).done(function(data, textStatus, jqXHR){
		try {
			var b = $.parseJSON(data);
		} catch (exception) { // API returned non-JSON data
			createLightbox('<h2>Error:</h2><p>'+data.replace("<br>","\n")+'</p>');
		}
		if ((typeof b !== 'undefined') && (typeof b.error !== 'undefined')) { // API returned a JSON error
			createLightbox('<h2>Error:</h2><p>'+(b.error.code != 0 ? b.error.code +': ' : '')+b.error.msg+'</p>');
		} else {
			// ajaxCall was successful
			if(typeof callBack !== 'undefined'){
				if(typeof thing !== 'undefined'){
					callBack(b,thing);
				} else {
					callBack(b);
				}
			}			
		}
	}).fail(function( jqXHR, textStatus, errorThrown){
		createLightbox('<h2>Error:</h2><p>'+errorThrown.replace("<br>","\n")+'</p>');
	});
}

function createLightbox(content){
	if(typeof content == 'undefined'){
		console.log('no content');
		return false;
	}
	//lightbox background
	var lightbox = $('<div/>', {'class':'lightbox'});
	$(lightbox).on({
		'click':function(event){
			$(this).remove();
			event.stopPropagation();
		}
	});
	
	var lightboxClose = $('<img/>',{'src':'/images/closeIcon.png','class':'lightbox-close'})
	$(lightboxClose).on({
		'click':function(){
			$(this).closest('.lightbox').trigger('click');
		}
	})
	$(lightbox).append(lightboxClose);
	
	//inner white div of lightbox
	var lightboxContent = $('<div/>',{'class':'lightbox-content'}); 
	$(lightboxContent).on({
		'click': function(event){
			event.stopPropagation();
		}
	});
	$(lightboxContent).html(content);
	$(lightboxContent).append(lightboxClose);
	$(lightbox).append(lightboxContent);
	$('body').append(lightbox);
	return lightboxContent;
}

function getReport(reportName = loginReport){
	ajaxCall('api/reports.php',{"report":reportName},function(jsonObj){
		if(typeof jsonObj != 'undefined' && jsonObj.rows != 'undefined'){
			// we build a table with a variable number of columns and rows
			let table = $('<table/>');
			let tbody = $('<tbody/>');
			let thead = $('<tr/>');
			let title = $('<h2/>').append(jsonObj.title);
			$.each(jsonObj.headers,function(key,value){
				thead.append($('<th/>').append(value));
			});
			$(table).append($('<thead/>').append($(thead)));
			$(jsonObj.rows).each(function(k,v){
				let tr = $('<tr/>');
				$.each(jsonObj.headers,function(key,value){
					tr.append($('<td/>').append(v[value]));
				});
				tbody.append(tr);
			});
			$(table).append(tbody);
			let lightbox = createLightbox('<h2>'+jsonObj.title+'</h2>');
			$(lightbox).append(table);
		}
	});
}

function resetForms() {
	$('input').val('');
}

function hideForms() {
	$('.form').not('.hidden').addClass('hidden');
}

function updateUserState(reset = 0) {
	if(reset == 1 || user.authToken == 0 || user.userId == 0 || user.hash == 0){
		user.userId = 0;
		user.authToken = 0;
		user.hash = 0;
		user.firstName = "";
		user.postalCode = "";
		user.lastName = "";
		user.username = "";
		user.emailAddress = "";
		$('#weather.form').find('.userFirstName').html('User');
		$('#weather.form').find('.currentTemp').html('0');
		$('#weather.form').find('.weatherReport').html('Unknown');
		hideForms();
		$('#logIn.form').removeClass('hidden');
	} else {
		ajaxCall('api/user.php',{ "authToken": user.authToken, "userId": user.userId, "hash": user.hash }, function(jsonObj){
			if(typeof jsonObj !== 'undefined' && typeof jsonObj.id != 'undefined' && jsonObj.id != 0){
				user.firstName = jsonObj.firstName;
				user.postalCode = jsonObj.postalCode;
				user.lastName = jsonObj.lastName;
				user.username = jsonObj.username;
				user.emailAddress = jsonObj.emailAddress;
				ajaxCall('api/weatherCheck.php',{ "authToken": user.authToken, "userId": user.userId, "postalCode": user.postalCode }, function(jsonObj){
					if(typeof jsonObj !== 'undefined' && typeof jsonObj.id != 'undefined' && jsonObj.id != 0){
						$('#weather.form').find('.userFirstName').html(user.firstName);
						$('#weather.form').find('.currentTemp').html(jsonObj.tempInF);
						$('#weather.form').find('.weatherReport').html(jsonObj.weather);
					}
				});
			} else {
				updateUserState(1); // an error occurred; log out
			}
		});
	}
}

$(function(){
	updateUserState(1); // reset User State to initial settings
	
	$('.logIn').on({
		"click": function(){
			let requirementsSatisfied = true;
			$('.invalidInput').removeClass('invalidInput');
			$('#logIn').find('.req').each(function(k,v){
				if($(this).val() == ''){
					requirementsSatisfied = false;
					$(this).addClass('invalidInput');
				}
			});
			if(requirementsSatisfied){
				// all requirements are satisfied; submit to API
				ajaxCall('api/login.php',$('#logIn').find('input').serialize(),function(jsonObj){
					if(typeof jsonObj !== 'undefined' && typeof jsonObj.userId != 'undefined' && jsonObj.userId != 0){
						resetForms();
						hideForms();
						user.userId = jsonObj.userId;
						user.authToken = jsonObj.authToken;
						user.hash = jsonObj.hash;
						updateUserState();
						$('#weather').removeClass('hidden');
					}
				});
			}
		}
	});
	
	$('.signOut').on({
		"click": function(){
			updateUserState(1);
		}
	});
	
	$('.cancelEditUser').on({
		"click": function(){
			hideForms();
			if(user.hash == 0 || user.authToken == 0){ // returns them to the login page if they are not logged in; otherwise to the main weather page
				$('#logIn.form').removeClass('hidden');
			} else {
				$('#weather.form').removeClass('hidden');
			}
		}
	});
	
	$('.editUser').on({
		"click": function(){
			hideForms();
			if(user.hash == 0 || user.authToken == 0){ 
				// there is no user to edit; they must be trying to create a new user
				// the password boxes may be hidden as we do not allow users to update their password
				$('#editUser').find('[for="editUserPassword"]').closest('div').removeClass('hidden');
				$('#editUserPassword').closest('div').removeClass('hidden');
				$('#editUserPassword').addClass('req');
				$('#editUser').find('[for="editUserConfirmPassword"]').closest('div').removeClass('hidden');
				$('#editUserConfirmPassword').closest('div').removeClass('hidden');
				$('#editUserConfirmPassword').addClass('req');
				$('#editUser.form').removeClass('hidden');
			} else {
				// load the user's data via an ajax call and then show the form
				// hide the password boxes as we do not allow users to update their password
				$('#editUser').find('[for="editUserPassword"]').closest('div').addClass('hidden');
				$('#editUserPassword').closest('div').addClass('hidden');
				$('#editUserPassword').removeClass('req');
				$('#editUser').find('[for="editUserConfirmPassword"]').closest('div').addClass('hidden');
				$('#editUserConfirmPassword').closest('div').addClass('hidden');
				$('#editUserConfirmPassword').removeClass('req');
				
				updateUserState();
				$.each(user,function(k,v){
					$('#editUser').find('[name="'+k+'"]').val(v);
				});
				
				$('#editUser.form').removeClass('hidden');
			}
		}
	});
	
	$('.saveUser').on({
		"click": function(){
			let requirementsSatisfied = true;
			$('.invalidInput').removeClass('invalidInput');
			$('#editUser').find('.req').each(function(k,v){
				if($(this).val() == ''){
					requirementsSatisfied = false;
					$(this).addClass('invalidInput');
				}
			});
			if($('#editUserPassword').val() != '' && $('#editUserPassword').val() != $('#editUserConfirmPassword').val()){
				requirementsSatisfied = false;
				$('#editUserConfirmPassword').addClass('invalidInput');
				createLightbox('<h2>Error</h2><p>Passwords do not match.</p>');
			}
			if(requirementsSatisfied){
				// all requirements are satisfied; submit to API
				ajaxCall('api/user.php',$('#editUser').find('input').serialize()+'&authToken='+user.authToken,function(jsonObj){
					if(typeof jsonObj !== 'undefined' && typeof jsonObj.id != 'undefined' && jsonObj.id != 0){
						resetForms();
						hideForms();
						if(user.userId == 0){
							createLightbox('<h2>Success!</h2><p>User was created successfully!</p>');
							$('#logIn').removeClass('hidden');
						} else {
							createLightbox('<h2>Success!</h2><p>User was updated successfully!</p>');
							updateUserState();
							$('#weather').removeClass('hidden');
						}
					}
				});
			}
		}
	});
	
	$('.backToMain').on({
		"click": function(){
			hideForms();
			if(user.hash == 0 || user.authToken == 0){ // returns them to the login page if they are not logged in; otherwise to the main weather page
				$('#logIn.form').removeClass('hidden');
			} else {
				$('#weather.form').removeClass('hidden');
			}
		}
	});
	
	$('.showReports').on({
		"click": function(){
			hideForms();
			// being logged in is not required to view reports
			$('#reports.form').removeClass('hidden');
		}
	});
});