$(document).ready(function()
{
	$("#images.owl-carousel").owlCarousel({ 
		items : 1,
		singleItem:true
	});
	$("#announcements .owl-carousel").owlCarousel({ 
		items : 3 
	});
  
	// Custom Navigation Events
	$(".next").click(function(){
		$("#announcements .owl-carousel").trigger('owl.next');
	})
	$(".prev").click(function(){
		$("#announcements .owl-carousel").trigger('owl.prev');
	})
	if(window.location.href.indexOf("'/ticket/view'")>0)
	{
		loadSearchingFilters();
	}
	$('.collapse').collapse('hide');

	
	// Prevent dropdown to be closed when we click on an accordion link
	$('.dropdown-accordion').on('click', 'a[data-toggle="collapse"]', function (event) {
	  event.preventDefault();
	  event.stopPropagation();
	  $($(this).data('parent')).find('.collapse.in').collapse('hide');
	  $($(this).attr('href')).collapse('show');
	})
	
});

$(window).load(function() {
	// When the page has loaded
	$("#preloader").hide();
});

function loginMyBB(username,password)
{
	$.post( "/forum/member.php", {  action: 'do_login', quick_login: 1, quick_username: ''+username+'', quick_password: ''+password+'', submit:'Login' });
}


function validURL(url){
    var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

    if(RegExp.test(url)){
        return true;
    }else{
        return false;
    }
} 
