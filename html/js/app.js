$(function(){
	


$(window).resize(function() {
    updateDisplayInfo();
});

function updateDisplayInfo() {
    var HTML = '';
    HTML += '<p>$(window).width() = ' + $(window).width() + 'px &nbsp; &nbsp; &nbsp; $(window).height() = ' + $(window).height() + 'px</p>';
    HTML += '<p>$(window).innerWidth() = ' + $(window).innerWidth() + 'px &nbsp; &nbsp; &nbsp; $(window).innerHeight() = ' + $(window).innerHeight() + 'px</p>';
    HTML += '<p>$(window).outerWidth() = ' + $(window).outerWidth() + 'px &nbsp; &nbsp; &nbsp; $(window).outerHeight() = ' + $(window).outerHeight() + 'px</p>';
    $('#infobar').html(HTML);
}
updateDisplayInfo();

});