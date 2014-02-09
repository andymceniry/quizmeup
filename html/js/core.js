/*global $*/

var AM = window.AM || {};

(function () {

	'use strict';

	AM.onDeviceReady = function () {
		document.addEventListener("backbutton", AM.onBackKeyDown, false);
	};

	AM.onBackKeyDown = function () {
		var currentPage = AM.history.pop(),
			previousPage = '';
		if (currentPage !== "home") {
			previousPage = AM.history.pop();
			AM.showPage(previousPage);
			return false;
		}
		navigator.app.exitApp();
	};

	AM.moveToBottom = function (pageRef) {
		var page = $('#page-' + pageRef),
			el = $('.movetobottom', page),
			elH = el.height() + 30;
		el.css('position','absolute').css('bottom','10px');
		page.css('padding-bottom',elH+'px');
	};
	
}());

function onLoad() {
	document.addEventListener("deviceready", AM.onDeviceReady, false);
	AM.init();
}
