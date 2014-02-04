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

}());

function onLoad() {
	document.addEventListener("deviceready", AM.onDeviceReady, false);
}
