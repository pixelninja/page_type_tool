Symphony.Language.add({
	'Request failed. Try again.': false,
	'Complete!': false
});

jQuery(function($){
	var _ = Symphony.Language.get;
	var fieldset = $('.add_pagetype'),
		status = $('<span />').attr('class', 'status'),
		gif = $('<img />'),
		form = $('form');
		
	if (!fieldset.length) return;
	
	fieldset.append(status)
	fieldset.find('button').click(function(e){
		var status = fieldset.find('span.status');
		status.text('');
		
		var page_type = fieldset.find('input').val(),
			self = $(this),
			page = fieldset.find('select').val();
		
		if (page_type == false || page == null) {
			alert('Please fill out both fields');
			return false;
		}
		var data = {addtype: {page_type: page_type, page: page}, 'action[add_pagetype]': 'run'};
				
		self.attr('disabled', 'disabled');
		status.prepend(gif.attr('src', Symphony.WEBSITE + '/extensions/sitemap_xml/assets/ajax-loader.gif'));
		
		$.ajax({
			url: window.location.href,
			data: data,
			success: function(html){
				self.attr('disabled', null);
				status.find('img').remove;
				status.text(Symphony.Language.get('Complete!'));
			},
			error: function(){
				self.attr('disabled', null);
				status.find('img').remove;
				status.text(Symphony.Language.get('Request failed. Try again.'));
			}
		});
		
		return false;	
	});
});