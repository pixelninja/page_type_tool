Symphony.Language.add({
	'Request failed. Try again.': false,
	'Complete!': false
});

jQuery(function($){
	var _ = Symphony.Language.get,
		fieldset = $('.add_pagetype'),
		status = $('<span />').attr('class', 'status'),
		gif = $('<img />'),
		form = $('form');
		
	if (!fieldset.length) return;
	
	fieldset.append(status);
	
	fieldset.find('button').click(function(e){
		var status = fieldset.find('span.status');
		status.text('');
		
		var page_type = fieldset.find('input').val(),
			self = $(this),
			page = fieldset.find('select').val();
		
		if (page_type == false || page == null) {
			status.removeClass('valid').addClass('invalid').text('Please fill out both fields');
			return false;
		}
		var data = {addtype: {page_type: page_type, page: page}, 'action[add_pagetype]': 'run'};
				
		self.attr('disabled', 'disabled');
		status.removeClass('valid').removeClass('invalid').text('');
		status.prepend(gif.attr('src', Symphony.WEBSITE + '/extensions/page_type_tool/assets/ajax-loader.gif'));
		
		$.ajax({
			url: window.location.href,
			data: data,
			success: function(html){
				self.attr('disabled', null);
				status.find('img').remove;
				status.addClass('valid').text(_('Complete!'));
			},
			error: function(){
				self.attr('disabled', null);
				status.find('img').remove;
				status.addClass('invalid').text(_('Request failed. Try again.'));
			}
		});
		
		return false;	
	});
});