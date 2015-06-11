//this function is called for the custom combobox in the submit ticke page
function rolesCombobox()
{
	(function($) 
	{
		$.widget( "custom.combobox", 
		{
			_create: function() 
			{
				
				this.wrapper = $( "<span>" )
					.addClass( "custom-combobox" )
					.insertAfter( this.element );
				this.element.hide();
				this._createAutocomplete();
			},
		 
			_createAutocomplete: function() 
			{
				var selected = this.element.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
		 
				this.input = $( "<input>" )
					.appendTo( this.wrapper )
					.val( value )
					.attr("name","role[]")
					.attr( "title", "" )
					.addClass( "custom-combobox-input ui-widget-content ui-state-default ui-corner-left form-control" )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: $.proxy( this, "_source" )
					})
					.tooltip({
						tooltipClass: "ui-state-highlight"
					});
		 
				this._on( this.input, 
				{
					autocompleteselect: function( event, ui ) 
					{
						ui.item.option.selected = true;
						this._trigger( "select", event, {
						  item: ui.item.option
						});
					},
		 
					autocompletechange: "_removeIfInvalid"
				});
			},
		 
			_source: function( request, response ) {
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( this.element.children( "option" ).map(function() 
				{
					var text = $( this ).text();
					if ( this.value && ( !request.term || matcher.test(text) ) )
						return {
							label: text,
							value: text,
							option: this
					};
				}));
				$(".ui-autocomplete").removeClass("ui-widget");
			},
		 
			_removeIfInvalid: function( event, ui ) 
			{
		 
				// Selected an item, nothing to do
				if ( ui.item ) {
					return;
				}
		 
				// Search for a match (case-insensitive)
				var value = this.input.val(),
				valueLowerCase = value.toLowerCase(),
				valid = false;
				this.element.children( "option" ).each(function() 
				{
					if ( $( this ).text().toLowerCase() === valueLowerCase ) 
					{
						this.selected = valid = true;
						return false;
					}
				});
		 
				// Found a match, nothing to do
				if ( valid ) {
					return;
				}
		 
				// Remove invalid value
				this.input
					.tooltip( "open" );
				this.element.val( "" );
				this._delay(function() 
				{
					this.input.tooltip( "close" ).attr( "title", "" );
				}, 2500 );
				this.input.autocomplete( "instance" ).term = "";
			},
		 
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			}
		});
	})(jQuery);
	
}

function customCombobox(id)
{
	$(function() 
	{
		$("[id^=role_" + id + "]").combobox();
		$( "#toggle" ).click(function() {
			$("[id^=role_" + id + "]").toggle();
		});
	});
}

function addAnotherTicket(array)
{
	(function($) 
	{
		BoxCounter = 0;
		loadRoles(BoxCounter, array)
		$("#first_div").remove();
		$('a#add_ticket').click(function (event) 
		{
			event.preventDefault();
			BoxCounter += 1;
			loadRoles(BoxCounter, array);
		});
		
	})(jQuery);
}

function loadRoles(BoxCounter, array) 
{
	var roleoptions = '';
	$(array).each(function(i, e) {
		if (e == 'Website Administrator')
		{
			roleoptions += '<option selected="selected" value="' + e + '">' + e + '</option>';
		}
		else
		{
			roleoptions += '<option value="' + e + '">' + e + '</option>';
		}
		
	});
	
	$('#custom').append('\
		<div id="custom_' + BoxCounter + '">\
			<div style="border-top:1px solid #eee;padding-top:20px;padding-bottom:10px;">\
				<div class="row">\
					<div class="col-lg-12">\
						<div id="first_div" class="text-right form-group">\
							<a class="navbar-right" href="javascript:void()"><span id="link_' + BoxCounter + '" class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>\
						</div>\
						<div class="form-group">\
							<textarea id="desc_' + BoxCounter + '" required="required" name="desc[]" placeholder="Please provide a detailed description of your question or of the problem you are facing and we will get back to you as soon as possible." class="form-control" rows="5"></textarea>\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-6">\
						<div class="form-group">\
							<select id="priority_' + BoxCounter + '" name="priority[]" class="form-control">\
								<option value="0">Low</option>\
								<option value="1">Medium</option>\
								<option value="2">High</option>\
								<option value="3">Immediate</option>\
							</select>\
						</div>\
					</div>\
					<div class="col-lg-6">\
						<div class="form-group">\
							<select id="role_' + BoxCounter + '" name="role[]" class="form-control">\
								' + roleoptions + '\
							</select>\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>');
	
	customCombobox(BoxCounter);	
}

function deleteChild()
{
	$(document).on( "click", function(event)
	{
		var id = event.target.id;
		var arr = id.split('_');
		//arr[1] is the number
		if(id.indexOf("link") > -1)
		{
			$("#custom_" + arr[1]).remove();
			BoxCounter -= 1;
		}
	});
}

function addAnotherHour(array)
{
	BoxCounter = 0;
	loadHours(BoxCounter, array);
	
	$("#first_div").remove();
	$('a#add_hour').click(function (event) 
	{
		BoxCounter += 1;
		loadHours(BoxCounter, array);
	});
}

function loadHours(BoxCounter, array) 
{
	$('#custom').append('\
		<div id="custom_' + BoxCounter + '">\
			<div style="border-top:1px solid #eee;padding-top:20px;padding-bottom:10px;">\
				<div class="row">\
					<div class="col-lg-12">\
						<div id="first_div" class="text-right form-group">\
							<a class="navbar-right" href="javascript:void()"><span id="link_' + BoxCounter + '" class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>\
						</div>\
						<div class="form-group">\
							<textarea id="desc_' + BoxCounter + '" required="required" name="desc[]" placeholder="Please provide a detailed description of your question or of the problem you are facing and we will get back to you as soon as possible." class="form-control" rows="5"></textarea>\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-6 col-xs-6">\
						<div class="form-group">\
							<input id="logindate_' + BoxCounter + '"  value="' + getCurrentDate() + '" type="text" name="loginDate[]" class="form-control pickdate" placeholder="Start Date *" required="required" />\
						</div>\
					</div>\
					<div class="col-lg-6 col-xs-6">\
						<div class="form-group">\
							<input id="logoutdate_' + BoxCounter + '"  value="' + getCurrentDate() + '" type="text" name="logoutDate[]" class=" form-control pickdate"  placeholder="End Date *" required="required" />\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-12" style="height: 0px; bottom: 15px;">\
						<div class="form-group text-center">\
							<b>To</b>\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-6 col-xs-6">\
						<div class="form-group">\
							<input id="logintime_' + BoxCounter + '"  value="' + getCurrentTime(0) + '" type="text" name="loginTime[]" class="form-control picktime" placeholder="Log in time *" required="required" />\
						</div>\
					</div>\
					<div class="col-lg-6 col-xs-6">\
						<div class="form-group">\
							<input id="logouttime_' + BoxCounter + '"  value="' + getCurrentTime(30) + '" type="text" name="logoutTime[]" class="form-control picktime" placeholder="Log out time *" required="required" />\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-12">\
						<div class="form-group text-center timediff" id="timediff_' + BoxCounter + '">\
							<b>(Time)</b>\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-12">\
						<div class="form-group">\
							<select class="form-control sharesites" id="site_' + BoxCounter + '" name="site[]" multiple="multiple">\
								' + array + '\
							</select>\
						</div>\
					</div>\
				</div>\
			</div>\
		</div>');
		
		multiSelectResearchSites();
		
		$(".multiSelect").addClass("form-control");
		//$(".multiSelectOptions").addClass("form-control");
		//$(".multiSelectOptions").children().addClass("form-control");
		
		callTime();
		initialTime(BoxCounter);
		timeDifference();
}

function initiateFormProperties()
{
	multiSelectResearchSites();
	
	$(".multiSelect").addClass("form-control");
	//$(".multiSelectOptions").addClass("form-control");
	//$(".multiSelectOptions").children().addClass("form-control");
	
	callTime();
	getCurrentTime();
	initialTime(0);
	timeDifference();
	//initialTime(BoxCounter);
	//timeDifference();
}

function callTime() 
{
	$('.picktime').each(function(i) {
		$(this).timepicker();
	});

	$('.pickdate').each(function(i) {
		$(this).datepicker()
	});
}

function getCurrentDate()
{
	var d = new Date();

	var month = d.getMonth()+1;
	var day = d.getDate();

	var output = ((''+month).length<2 ? '0' : '') + month + '/' +
				((''+day).length<2 ? '0' : '') + day + '/' +
				d.getFullYear();

	return output;
}

function getCurrentTime(t)
{
	var dt = new Date();
	var min = dt.getMinutes();
	min+=  t;
	var remainder = (30 - min) % 30;
	min += remainder;
	var hours = dt.getHours();
	if(min >=60)
	{
		min = 30;
		hours+=1;
	}
    var hours = (hours+24)%24;
    var mid='am';
    if(hours==0)
	{ 
		//At 00 hours we need to show 12 am
		hours=12;
    }
    else if(hours>12)
    {
		hours=hours%12;
		mid='pm';
    }
    return hours + ":" + min + mid;
}

function initialTime(i)
{		
	var startTime = $('#logintime_'+i+'').val();
	var endTime = $('#logouttime_'+i+'').val();
	var startDate = $('#logindate_'+i+'').val();
	var endDate = $('#logoutdate_'+i+'').val();
	
	startTime = startTime.replace("pm", " PM").replace("am", " AM");							
	endTime = endTime.replace("pm", " PM").replace("am", " AM");
	
	startDate = $('#logindate_'+i+'').val();
	endDate = $('#logoutdate_'+i+'').val();
	
	var start_actual_time = new Date(startDate + " " + startTime);
	var end_actual_time = new Date(endDate + " " + endTime);
	var diff = end_actual_time - start_actual_time;

	var diffSeconds = diff / 1000;
	var HH = Math.floor(diffSeconds / 3600);
	var MM = Math.floor(diffSeconds % 3600) / 60;
	var formatted = ((HH < 10) ? (HH) : HH) + "hr " + ((MM < 10) ? (MM) : MM) + "min";
	formatted = formatted.replace("0hr 0min","");
	
	if (formatted.length > 1) {
		if (!(formatted.indexOf("-") > -1)) {
			$('#timediff_'+i+'').html("(<b>" + formatted +  "</b>)");
			
		} else {
			$('#timediff_'+i+'').empty();
		}
	} else {
		$('#timediff_'+i+'').empty();
	}
}
		
function timeDifference()
{
	$('.timediff').each(function(i) 
	{
		var startTime = $('#logintime_'+i+'').val();
		var endTime = $('#logouttime_'+i+'').val();
		var startDate = $('#logindate_'+i+'').val();
		var endDate = $('#logoutdate_'+i+'').val();
		$('#logintime_'+i+'').on( "change", function() 
		{
			startTime = this.value.replace("pm", " PM").replace("am", " AM");							
			endTime = endTime.replace("pm", " PM").replace("am", " AM");
			
			startDate = $('#logindate_'+i+'').val();
			endDate = $('#logoutdate_'+i+'').val();
			var start_actual_time = new Date(startDate + " " + startTime);
			var end_actual_time = new Date(endDate + " " + endTime);
			var diff = end_actual_time - start_actual_time;

			var diffSeconds = diff / 1000;
			var HH = Math.floor(diffSeconds / 3600);
			var MM = Math.floor(diffSeconds % 3600) / 60;
			var formatted = ((HH < 10) ? (HH) : HH) + "hr " + ((MM < 10) ? (MM) : MM) + "min";
			formatted = formatted.replace("0hr 0min","");
			if (formatted.length > 1) 
			{
				if (!(formatted.indexOf("-") > -1)) 
				{
					$('#timediff_'+i+'').html("(<b>" + formatted +  "</b>)");
				} 
				else 
				{
					$('#timediff_'+i+'').empty();
				}
			} 
			else 
			{
				$('#timediff_'+i+'').empty();
			}
		});
		$('#logouttime_'+i+'').on( "change", function() 
		{
			endTime = this.value.replace("pm", " PM").replace("am", " AM");							
			startTime = startTime.replace("pm", " PM").replace("am", " AM");
			
			startDate = $('#logindate_'+i+'').val();
			endDate = $('#logoutdate_'+i+'').val();
			var start_actual_time = new Date(startDate + " " + startTime);
			var end_actual_time = new Date(endDate + " " + endTime);
			var diff = end_actual_time - start_actual_time;

			var diffSeconds = diff / 1000;
			var HH = Math.floor(diffSeconds / 3600);
			var MM = Math.floor(diffSeconds % 3600) / 60;
			var formatted = ((HH < 10) ? (HH) : HH) + "hr " + ((MM < 10) ? (MM) : MM) + "min";
			formatted = formatted.replace("0hr 0min","");
			if (formatted.length > 1) 
			{
				if (!(formatted.indexOf("-") > -1)) 
				{
					$('#timediff_'+i+'').html("(<b>" + formatted +  "</b>)");
				} 
				else
				{
					$('#timediff_'+i+'').empty();
				}
			} 
			else
			{
				$('#timediff_'+i+'').empty();
			}
		});
	});
}

function multiSelectResearchSites()
{
	$(".sharesites").multiSelect({
		selectAll: false,
		noneSelected: 'All Research Sites',
		oneOrMoreSelected: '% Research Sites'
	});
}

// populate the fields on click
function tracking()
{
	$('.main_tr').click(function() 
	{
		if (!$(this).hasClass('head'))
		{
			var id = $(this).children().eq(0).find('.tic_id').text();
			var email = $(this).children().eq(0).find('.tic_email').text();
			var prior =  $(this).children().eq(0).find('.tic_prior').text();
			var status = $(this).children().eq(0).find('.tic_status').text();
			var desc = $(this).children().eq(0).find('.tic_desc').text();
			var time = $(this).children().eq(0).find('.tic_time').text();

			// Fill the vars to the appr inputs
			$('#id').val(id);
			$('#email').val(email);
			$('#desc').val(desc);
			
			$('#prior option:contains(' + prior + ')').prop('selected', true);
			$('#status option:contains(' + status + ')').prop('selected', true);
			
			$('#editModalLabel').html('Ticket By: '+email);
			$("#viewtickettracking").empty();
			var d = '';
			$.post( "/ticket/view", {ticket_id: id} ).done(function(data) {
				$("#viewtickettracking").append(data);
				
				$(".in_tr").click(function()
				{
					if (!$(this).hasClass('head'))
					{
						var desc = $(this).children().eq(0).find('.ticket_desc').text();
						var nextTRID = $(this).closest('tr').next().attr('id');
						if($(this).attr("class").indexOf("clicked") < 0)
						{
							$(this).addClass("clicked");
							$("#"+nextTRID).css("display","");
						}
						else
						{
							$(this).removeClass("clicked");
							$("#"+nextTRID).css("display","none");
						}
					}
				});
				
			});
			
			// show the modal (will happen AFTER all the vars are filled out)
			$('#editModal').modal('show');
		}
	});
}

function button_to_checkbox(selector)
{
	$(selector).change(function (e) 
	{
		var checkedDays = $(selector + " :checkbox").map(function () {
			return +$(this).is(':checked');
		}).get(); 
		
		console.log(checkedDays);
	});
}


function filter()
{
	var o = true;
	var queryClicked ="";
	var p = "";
	var s = "";
	$('a#filter').click(function (event) 
	{
		//event.preventDefault();
		if(o)
		{
			$("#fil").css("display","inherit");
			callTime();
			
			var fullDate = new Date();
			var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
			var currentDate = "01/" + twoDigitMonth + "/" + fullDate.getFullYear();
			
			$("[name=from]").val(currentDate);
			button_to_checkbox("#priority_filter");
			button_to_checkbox("#status_filter");
			o = false;
	
			$("[class^=priority]").each(function(i){
				$(this).click(function(){
					if($(this).attr("class").indexOf("clicked") >=0)
					{
						//off
						$(this).removeClass("clicked");
						p = p.replace('?','');
						p = p.replace('p[]='+i+'&','');
					}
					else
					{
						//on
						$(this).addClass("clicked");
						p+= "p[]="+i+"&";
					}
					queryClicked = p+s;
					
				});
			});
			
			$("[class^=status]").each(function(i){	
				$(this).click(function(){
					if($(this).attr("class").indexOf("clicked") >=0)
					{
						//off
						$(this).removeClass("clicked");
						s = s.replace('?','');
						s = s.replace('s[]='+i+'&','');
					}
					else
					{
						$(this).addClass("clicked");
						s+= "s[]="+i+"&";
					}
					queryClicked = p+s;
					
				});
			});		
			
		}
		else
		{
			$("#fil").css("display","none");
			o = true;
			queryClicked="";
		}		
	});
	
	$('.searchbar').keypress(function(e) {
		if(e.which == 13) 
		{
			doSearch(queryClicked);
		}
	});
	
	$('#search1').click(function() {
		doSearch(queryClicked);
	});
	
	loadSearchingFilters();
}


function doSearch(queryClicked)
{
	var query = '';
	var sQuery = '';
	if ($('.searchbar').val().length > 0)
	{
		sQuery = 'q='+$('.searchbar').val();
		query = '?';
	} 
	
	if (queryClicked.length > 0) 
	{
		if ($('.searchbar').val().length <= 0)
		{
			//delete the last &
			queryClicked = queryClicked.slice(0,-1);
		}
		query = '?';
	}
	query += queryClicked + sQuery;
	window.location.href = '/ticket/view' + query;
	
	//$("tr.low").css('display','none');
	
	//queryClicked = queryClicked.replace('?',"");
	//queryClicked = queryClicked + '&' ;
}


function loadSearchingFilters()
{
	if(window.location.search.indexOf('p[]') >=0 || window.location.search.indexOf('s[]') >=0)
	{
		$("a#filter").trigger("click");
		//delete the '?'
		var filtering =  window.location.search.replace('?','');
		
		var array = filtering.split('&');
		var i;
		for (i = 0; i < array.length; i++) 
		{
			if(array[i].indexOf('p')>=0)
			{
				$(".priority_"+array[i].charAt(4)).trigger("click");
			}
			else if(array[i].indexOf('s')>=0)
			{
				$(".status_"+array[i].charAt(4)).trigger("click");
			}
			else
			{
				//q='';
			}
		}
	}
	else
	{
		if(window.location.search.indexOf('date') >=0)
		{
			$("a#filter").trigger("click");
		}
	}
	
}

function cancelsubmit()
{
	$('#cancel').click(function() {
		history.back();
		return false;
	});
}

function mediaClick(result)
{
	//result[tableRowClicked][media_id][scr/title/description];
	$(".media_tr").click(function(){
		var media_id = $(this).attr("id");
		var tableRowClicked = $(this).attr("name");
		var src = result[tableRowClicked][media_id][0];
		var title = result[tableRowClicked][media_id][1];
		var description = result[tableRowClicked][media_id][2];
		var htmlFrameToAppend = '';
		
		if (tableRowClicked.indexOf("video") >= 0)
		{
			var arr = tableRowClicked.split('_');
			tableRowClicked = arr[1];
			htmlFrameToAppend ='\
			<div class="row">\
				<div class="embed-responsive embed-responsive-16by9">\
					<iframe class="embed-responsive-item" src="'+src+'" allowfullscreen="">\
					</iframe>\
				</div>\
			</div>';
		}
		else
		{
			htmlFrameToAppend ='\
				<div class="imageBox image-editor">\
					<div class="row">\
						<div id="image-cropper_'+media_id+'">\
							<div class="hidden imgfile">' + src + '</div>\
							<div class="col-lg-12 cropit-image-preview" align="center"></div>\
							<div class="slider-wrapper">\
								<div class="image-size-label">\
									Resize image\
								</div>\
								<span class="icon icon-image small-image"></span>\
								<input type="range" class="cropit-image-zoom-input" min="0" max="1" step="0.01">\
								<span class="icon icon-image large-image"></span>\
							</div>\
						</div>\
					</div>\
				</div>';
		}
		
		if(title == "No Title")
			title = "";
		if(description == "No Description")
			description = "";
		$(".media_tr").each(function(){
			$(this).removeClass("active");
			$("#showMedia").empty();
		});
		
		$(this).addClass("active");
		
		$("#showMedia").append('\
					'+htmlFrameToAppend+'\
			<div align="center">\
				<div class="row">\
					<div class="col-lg-12">\
						<div class="form-group">\
							<input type="text" class="form-control" name="title" placeholder="Image Title" value="'+title+'"/>\
						</div>\
					</div>\
				</div>\
				<div class="row">\
					<div class="col-lg-12">\
						<div class="form-group">\
							<textarea name="desc" class="form-control" placeholder="Image Description" rows="4">'+description+'</textarea>\
						</div>\
					</div>\
				</div>\
				<input type="hidden" id="imageID" value="2">\
			</div>\
		');
		
		$("div[class*='image-editor']").each(function(i){
			var srcimg = 'http://njace-cc.montclair.edu/img/raw/' + $(this).find('.imgfile').html();
			$(this).cropit({
				imageState: {
					src: srcimg
				}
			});
		});
		//$('#image-cropper_' + media_id).cropit();
		
	});
}

function crop(id)
{
	var imgdata = '';
	var src = '';
	$('.cropper-image > img').each(function()
	{
		src = $(this).attr('src');
		$(this).cropper({
			aspectRatio: 4 / 3,
			autoCropArea: 0.65,
			responsive: true,
			strict: true,
			guides: true,
			highlight: true,
			dragCrop: true,
			movable: true,
			resizable: true,
			rotatable: true,
			zoomable: true,
			touchDragZoom: true,
			mouseWheelZoom: true,
			crop: function(data) {
				imgdata = data;
				if(Math.round(imgdata.width) > 622){
					$("#widthCol").removeClass("panel-danger");
					$("#widthCol").addClass("panel-success");
				}
				else{
					$("#widthCol").removeClass("panel-success");
					$("#widthCol").addClass("panel-danger");
				}
				if(Math.round(imgdata.height) > 415){
					$("#heightCol").removeClass("panel-danger");
					$("#heightCol").addClass("panel-success");
				}
				else{
					$("#heightCol").removeClass("panel-success");
					$("#heightCol").addClass("panel-danger");
				}
				if((Math.round(imgdata.width) > 622)&&(Math.round(imgdata.height) > 415)){
					$('.savecrop').removeAttr("disabled");
				}
				else{
					$('.savecrop').attr("disabled", true);
				}
				$("#dataWidth").text("Width: " + Math.round(imgdata.width) + "px");
				$("#dataHeight").text("Height: " + Math.round(imgdata.height) + "px");
				
			}
		});
		
	});
	$('.savecrop').on('click',function()
	{
		$.post( "/crop/"+id, {imgdata: imgdata, src: src } ).done(function(data) {
			//width = imgdata['width'];
			//height = imgdata['height'];
			
			var siteID = JSON.parse(data);
			window.location.href="/"+siteID+"/media";
		});
		
		
	});
	
}

function addYoutubeLink(siteID)
{
	$('.addvideo').on('click',function()
	{
		$('#addVideoModal').modal('show');
	});
	
	var clickcount = 1;
	$('.youtubeLink').on('click',function(i)
	{
		var linkHTML = '<div class="form-group link_'+clickcount+'">\
                                        <div class="col-lg-10">\
                                                <input class="form-control" type="text" placeholder="You Tube Link" name="SiteName" value="" />\
                                        </div>\
                                        <div class="col-lg-2 text-right-lg">\
                                                <a id="'+clickcount+'" class="remove btn btn-danger btn-block" href="javascript:void()">Remove</a>\
                                        </div>\
                                </div>';
		$('.links').append(linkHTML);
		
		clickcount++;
		
		$('.remove').on('click',function() 
		{
			$(this).closest('.form-group').remove();
		});	
	});
	
	$('button[name=addVideo]').on('click',function(i)
	{
		$("div[class*='link_']").each(function(){
			var Link = $(this).children().find('input').val();
			$.post( "/" + siteID + "/media/add-video", {link: Link} ).done(function(data) {
				window.location.href="/"+siteID+"/media";
			});
		});
		
	});
	
}

function setupEditableTextboxes(){
    $.fn.editable.defaults.mode = 'inline';
    
    $('.title').editable({
        type: 'text',

        title: 'Edit Title'
    });
    $('.description').editable({
        type: 'text',

        title: 'Edit Description'
    });
}

