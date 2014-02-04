/*global $*/

var AM = window.AM || {};

(function () {

	'use strict';

	AM.history = [];
	AM.history.push('home');

	AM.eventHandlerForButtons = (function () {
        $(function () {
			$('body').on('click', '.button', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (pageRef !== undefined) {
					AM.showPage(pageRef);
					return false;
				}
				if (funcRef !== undefined) {
					AM.runFunc(funcRef, btn);
					return false;
				}
			});
        });
    }());

	AM.eventHandlerForJsLinks = (function () {
        $(function () {
			$('body').on('click', '.jslink', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (pageRef !== 'undefined') {
					AM.showPage(pageRef);
					return false;
				}
				if (funcRef !== 'undefined') {
					AM.runFunc(funcRef, btn);
					return false;
				}
			});
        });
    }());


	AM.showPage = function (pageRef) {
		AM.history.push(pageRef);
		console.log(AM.history);
		$('.page').hide();
		$('#page-' + pageRef).show();
	};

	AM.runFunc = function (funcRef, el) {
		AM[funcRef]();
	};

	AM.questionCreate = function (el) {
		alert('yeah baby');
	};


}());
