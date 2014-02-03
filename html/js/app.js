/*globals $*/

$(function () {

	'use strict';

	$('.button').click(function () {
		var btn = $(this),
			ref = btn.data('target-ref');
		$('.page').hide();
		$('#page-' + ref).show();
		return false;
	});

});