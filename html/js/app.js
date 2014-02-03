/*global $, window*/

var AM = window.AM || {};

(function () {

	'use strict';

	AM.eventHandlerForClosingOverlays = (function () {
        $(function () {
			$('body').on('click', '.button', function () {
				var btn = $(this),
					pageRef = btn.data('target-ref'),
					funcRef = btn.data('target-func');
				if (typeof (pageRef) !== 'undefined') {
					AM.showPage( pageRef );
					return false;
				}
				if (typeof(funcRef) !== 'undefined') {
					AM.runFunc( funcRef, btn );
					return false;
				}
			});
        });
    }());


	AM.showPage = function( pageRef ) {
		$('.page').hide();
		$('#page-' + pageRef).show();
	}

	AM.runFunc = function( funcRef, el ) {
		AM[funcRef]();
	}
	
	AM.questionCreate = function(el) {
		alert('yeah baby');
	}

	
	function onLoad() {
        document.addEventListener("deviceready", onDeviceReady, false);
    }

    // device APIs are available
    //
    function onDeviceReady() {
        // Register the event listener
        document.addEventListener("backbutton", onBackKeyDown, false);
    }

    // Handle the back button
    //
    function onBackKeyDown() {
		alert('back off pal');
	}
	
	
}());