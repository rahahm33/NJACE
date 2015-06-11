function initMap(points) 
{
	var mapOptions = {
		zoom: 8,
		minZoom: 5,
		center: new google.maps.LatLng(40.0000 , -74.5000 ),
		panControl:true,
		zoomControl: true,
		zoomControlOptions: 
		{
		  style: google.maps.ZoomControlStyle.LARGE
		},
		mapTypeControlOptions: 
		{
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		},
		navigationControl: true,
		scaleControl:true,
		streetViewControl:true,
		overviewMapControl:true,
		rotateControl:true
	};
	
	NJCoordinates =[
		new google.maps.LatLng(41.3572, -74.6950),
		new google.maps.LatLng(41.3394, -74.6559),
		new google.maps.LatLng(40.9934, -73.8940),
		new google.maps.LatLng(40.8398, -73.9586),
		new google.maps.LatLng(40.7691, -74.0094),
		new google.maps.LatLng(40.6994, -74.0231),
		new google.maps.LatLng(40.6786, -74.0437),
		new google.maps.LatLng(40.6515, -74.0808),
		new google.maps.LatLng(40.6421, -74.1357),
		new google.maps.LatLng(40.6452, -74.1962),
		new google.maps.LatLng(40.5952, -74.2003),
		new google.maps.LatLng(40.5566, -74.2195),
		new google.maps.LatLng(40.4877, -74.2552),
		new google.maps.LatLng(40.4762, -74.2264),
		new google.maps.LatLng(40.5253, -73.9503),
		new google.maps.LatLng(40.4846, -73.8885),
		new google.maps.LatLng(40.0045, -73.9352),
		new google.maps.LatLng(39.6131, -74.0410),
		new google.maps.LatLng(39.4744, -74.2209),
		new google.maps.LatLng(38.9882, -74.6713),
		new google.maps.LatLng(38.8664, -74.8553),
		new google.maps.LatLng(38.8472, -75.0476),
		new google.maps.LatLng(39.0565, -75.1685),
		new google.maps.LatLng(39.2525, -75.3250),
		new google.maps.LatLng(39.4500, -75.5544),
		new google.maps.LatLng(39.4966, -75.5612),
		new google.maps.LatLng(39.4998, -75.5283),
		new google.maps.LatLng(39.5411, -75.5338),
		new google.maps.LatLng(39.5761, -75.5090),
		new google.maps.LatLng(39.6237, -75.5708),
		new google.maps.LatLng(39.6713, -75.5104),
		new google.maps.LatLng(39.7167, -75.4843),
		new google.maps.LatLng(39.8033, -75.4156),
		new google.maps.LatLng(39.8360, -75.2632),
		new google.maps.LatLng(39.8823, -75.1918),
		new google.maps.LatLng(40.1180, -74.7922),
		new google.maps.LatLng(40.1390, -74.7331),
		new google.maps.LatLng(40.2565, -74.8485),
		new google.maps.LatLng(40.3361, -74.9419),
		new google.maps.LatLng(40.4020, -74.9721),
		new google.maps.LatLng(40.4240, -75.0627),
		new google.maps.LatLng(40.4898, -75.0613),
		new google.maps.LatLng(40.5733, -75.1067),
		new google.maps.LatLng(40.5639, -75.2138),
		new google.maps.LatLng(40.6192, -75.2028),
		new google.maps.LatLng(40.6494, -75.2069),
		new google.maps.LatLng(40.8284, -75.0806),
		new google.maps.LatLng(40.8429, -75.0998),
		new google.maps.LatLng(40.8689, -75.0504),
		new google.maps.LatLng(40.9913, -75.1369),
		new google.maps.LatLng(41.2293, -74.8677),
		new google.maps.LatLng(41.3479, -74.7537),
		new google.maps.LatLng(41.3469, -74.7249),
		new google.maps.LatLng(41.3593, -74.6960)
	];
	var NJBorders;
	infowindow = null;
	map = new google.maps.Map(document.getElementById("map"), mapOptions);
	setMarkers(map, points);
	
	// Construct the polygon.
	NJBorders = new google.maps.Polygon({
		paths: NJCoordinates,
		strokeColor: "#4d90fe",
		strokeOpacity: 1.0,
		strokeWeight: 5,
		fillColor: "Transparent",
		fillOpacity: 0.35
	});
	
	NJBorders.setMap(map);
}

function setMarkers(map, locations) 
{
	var markers = [];
	var image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
	$(locations).each(function(i) 
	{
		var site = locations[i];
		var contentString = site[0];
		var mutual = false;
		var nbMutual = 0;
		if (contentString.indexOf("|") >= 0)
		{
			mutual = true;
			nbMutual = contentString.match(/\|/g).length;
		}
		var myLatLng = new google.maps.LatLng(site[1], site[2]);
		var marker  = new google.maps.Marker({
			position: myLatLng,
			map: map,
			icon: image,
			title: site[0]
		});	

		markers.push(marker);
		
		var final ="";
		if(mutual)
		{
			var titles = contentString.split("|");
			var invests = site[3].split("|");
			var locs = site[4].split("|");
			
			for (var i=0; i<nbMutual+1; i++) 
			{
				if(i == nbMutual)
				{
					final += "<table><tr><td><p>"+titles[i]+"<br/>" + invests[i] + "</p></td></tr><tr><td><p>"+ locs[i] + "</p></td></tr></table>";
				}
				else
				{
					final += "<table><tr><td><p>"+titles[i]+"<br/>" + invests[i] + "</p></td></tr><tr><td><p>"+ locs[i] + "</p></td></tr></table><br/>";
				}
			}
		}
		else
		{
			final = "<table><tr><td><p>"+ site[0] +"<br/>"+ site[3] +"</p></td></tr><tr><td><p>"+ site[4] +"</p></td></tr></table>";
		}

		
		
		google.maps.event.addListener(marker, "click", function() 
		{
			if (infowindow) 
			{ 
				infowindow.close(); 
				infowindow = new google.maps.InfoWindow(); 
			} 
			else 
			{ 
				infowindow = new google.maps.InfoWindow(); 
			} 
			infowindow.setContent(final);
			infowindow.open(map, marker);
		});
	});
	var mc = new MarkerClusterer(map, markers);
}
				
function loadMap(points)
{				
	google.maps.event.addDomListener(window, "load", initMap(points));
}

function recruitPostForm(id)
{	
	var fname = $('.fname').val();
	var lname = $('.lname').val();
	var email = $('.email').val();
	var phone = $('.phone').val();

	var contact = 0;
	if ($('.contactP').is(':checked')) {
		contact = 1;
	}
	var contacttime = $('.contacttime').val();
	var sites = id;
	var sitesInfo = 'Sites can contact you. ';
	
	var study = 1;
	//alert(sites);

	var error = 0;
	$("#recruitingForm input").each(function() {
		if ($(this).attr('required') == 'required')
		{
			//alert($(this).attr('name'));
			if ($(this).val().length < 1)
			{
				error++;
			}
		}
	});
//	alert(error);
	if (error == 0) 
	{
		$.post( "/how-can-i-help", { submit:1, fname: fname, lname: lname, email: email, phone: phone, study: study, sites: sites, contact: contact, contacttime: contacttime })
		.done(function(data) {
			$('.results').hide().html('<span class="blue" style="Font-weight:bold;">Thanks! We will contact you soon!</span>').fadeIn();
			//$('.results').html(data);
			$('.mlist').attr('disabled', 'disabled');
			//alert(data);
		});
	}
	else
	{
		$('.results').hide().html('<span style="color:red;font-weight:bold;">Name and Email required</span>').fadeIn();
	}
}

function loadRecruit()
{
	$('.mlist').on('click',function() 
	{
		recruitPostForm(0);
	});
}

function loadManageMedia(id)
{
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	});	
	
	$('select.priority').each(function(i)
	{
		var pictureID = $(this).parent().parent().attr('id');
		$(this).on('change',function()
		{
			$.post('/'+id+'/media', { picid: pictureID, priority: $(this).val() })
			.done(function(data) {
				$('main').html(data);
			});			
		});
	});
	
	$('#images tr td:nth-child(3)').on('click',function()
	{
		var pictureID = $(this).parent().attr('id');
		$.post('/'+id+'/media/info', { picid: pictureID })
		.done(function(data) {
			data = JSON.parse(data);				
			$.each(data, function(index, e) {
				$('#editImageModal .form-control').each(function()
				{
					if (index == $(this).attr('name'))
					{
						$(this).val(e);
					}
				});				
			});
		});
		$('#editImageModal').modal('show');
	});
	
	$('.addimage').on('click',function()
	{
		$('#addImageModal').modal('show');
	});
}

function loadManageMembers(id,members,short_name,role)
{
	$('div.modal-body > div.form-group > div:nth-child(1)').addClass('text-right-lg');
	
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	});	
	
	
	$('#memberModalCreate input').each(function()
	{
		var label = $(this).parent().parent().find('label').text();
		$(this).attr('placeholder',label);
	});
	rolesCombobox();
	$( ".roles" ).combobox();
	
	$('#memberModal #submitbutton').on('click',function()
	{
		var userid = $('.members').val();
		$.post('/'+id+'/members/add', { userid: userid })
		.done(function(data) {
			location.reload();
		});	
	});
	
	$('#memberModalCreate #submitbutton').on('click',function()
	{
		var data = {};
		var error = 0;
		$('#memberModalCreate .form-control').each(function()
		{
			if (!($(this).attr('name') === undefined))
			{
				if ($(this).attr('required') == 'required')
				{
					if ($(this).val().length < 1)
					{
						error++;
					}
				}
				data[$(this).attr('name')] = $(this).val();
			}
		});
		if (error == 0)
		{
			$.post('/'+id+'/members/create', data, function(data) {
				if (data.length > 1)
				{
					alert(data);
				}
				else
				{
					location.reload();
				}
			});
		}
		else
		{
			alert('Please complete the required fields.');
		}
	});
	
	$('.createmember').on('click',function()
	{
		var roleHTML = '';
		$('#memberModalCreateLabel').text('Create member & add to '+short_name);
		$.each(role, function(index, e) {
			roleHTML += '<option'
			$.each(e, function(index, e) {
				roleHTML += ' value="'+e+'">'+e;
			});
			roleHTML += '</option>';
		});
		$('#memberModalCreate .roles').empty().append(roleHTML);
		$('#memberModalCreate').modal('show');
		
		
	});
	
	$('#members tr').on('click',function()
	{
		$('#memberModalEditLabel').text('Edit');
		$('#memberModalEdit').modal('show');
		var userid = $(this).attr('id');
		$('#memberModalEdit .userid').val(userid);
		$('#memberModalEdit .groupid').val(id);
		//var userData = '';
		$.post( '/'+id+'/members/info', { userid : userid })
		.done(function( data ) {
			userData = JSON.parse(data);
			$.each(userData, function(index, e) {
	
				if (index == 'role')
				{					
					$('#memberModalEdit input.custom-combobox-input').val(e);					
				} 
				else
				if (index == 'email')
				{
					$('#memberModalEditLabel').text('Edit '+e);
				} 
				else
				if (index == 'glevel')
				{
					if (e == 1)
					{
						$('#memberModalEdit .checkbox input').prop('checked', true);
					}
					else
					{
						$('#memberModalEdit .checkbox input').prop('checked', false);
					}
				}				
			});
		});
		
	});
	
	$('.addmember').on('click',function()
	{
		$('#memberModalLabel').text('Add member to '+short_name);
		
		var membHTML = '';
		$.each(members, function(index, e) {
			membHTML += '<option'
			$.each(e, function(index, e) {
				if (index == 'uid')
				{
					membHTML += ' value="'+e+'">'
				}
				if (index == 'username')
				{
					membHTML += e;
				}
				if (index == 'email')
				{
					membHTML += ' - '+e;
				}
				//alert(index + ' - ' + e);
			});
			membHTML += '</option>';
		});
		$('#memberModal .members').empty().append(membHTML);
		$('#memberModal').modal('show');
	});
}

function loadSites(id,tags,fullname,shortname,siteaffil,sitedesc,shortdesc,sitecrit,email,phone,street,city,state,zip,statetags,websites,sitename)
{

	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	});
	
	$(function($){
		$(".phone").mask("(999) 999-9999");
	});
	
	$('.mlist').on('click',function() 
	{
		recruitPostForm(id);
	});
	

	var clickcount = 200
	$('.addlink').on('click',function(i)
	{
		
		clickcount++;
		
		var linkHTML = '<div class="form-group link_'+clickcount+'">\
						<div class="col-lg-5">\
							<input class="form-control" type="text" placeholder="Website Name" name="SiteName" value="" />\
						</div>\
						<div class="col-lg-5">\
							<input class="form-control" type="text" placeholder="Website Address" name="SitePath" value="" />\
						</div>\
						<div class="col-lg-2 text-right-lg">\
							<a id="'+clickcount+'" class="remove btn btn-danger btn-block" href="#remove">Remove</a>\
						</div>\
					</div>';
		$('.websites').append(linkHTML);
		
		$('.remove').on('click',function() 
		{
			$(this).closest('.form-group').remove();
		});
					
	});
	
	$('.titleSwitch').click(function()
	{
		changeSiteName();
	});
	$('.editbutton').not('.noModal').each(function(i)
	{
		$(this).on('click',function()
		{	
			editSite(i,id,tags,fullname,shortname,siteaffil,sitedesc,shortdesc,sitecrit,email,phone,street,city,state,zip,statetags,websites,sitename);
		});
	});
	
	
	$('div.modal-body > div.form-group > div:nth-child(1)').addClass('text-right-lg');
	changeSiteName();
	
}

function editSite(bid,id,tags,grant_title,short_name,site_affiliation,site_desc,short_desc,site_crit,site_email,site_phone,street,city,state,zip,statetags,websites,site_name)
{
	//alert(bid);
	$('#editForm .form-group').hide();
	if (bid == 0)
	{
		$('#editForm .form-group').children().find('.grant_title').parent().parent().show();
		$('#editForm .form-group').children().find('.short_name').parent().parent().show();
	}
	else
	if (bid == 1)
	{
		$('#editForm .form-group').children().find('.site_name').parent().parent().show();
		$('#editForm .form-group').children().find('.site_affiliation').parent().parent().show();
	}
	else
	if (bid == 2)
	{
		$('#editForm .form-group').children().find('.site_desc').parent().parent().show();
		$('#editForm .form-group').children().find('.short_desc').parent().parent().show();
	}
	else
	if (bid == 3)
	{
		$('#editForm .form-group').children().find('.site_crit').parent().parent().show();
	}
	else
	if (bid == 5)
	{
		$('#editForm .form-group').children().find('.site_email').parent().parent().show();
		$('#editForm .form-group').children().find('.site_phone').parent().parent().show();
		$('#editForm .form-group').children().find('.street').parent().parent().show();
		$('#editForm .form-group').children().find('.city').parent().parent().show();
		$('#editForm .form-group').children().find('.street').parent().parent().parent().parent().show();
	}
	else
	if (bid == 6)
	{
		$('#editForm .form-group').children().find('.addlink').parent().parent().show();	
	}
	else
	{
		$('#editForm .form-group').show();		
	}
	//tags = JSON.parse(tags);
	var affilTags = [];
	var stateTags = [];
	$.each(tags, function(index, element) {
		affilTags.push(element);
	});
	
	$.each(statetags, function(index, element) {
		stateTags.push(element);
	});
	var linkHTML = '';
	
	$.each(websites, function(index, e) {
		linkHTML += '<div class="form-group link_'+index+'">';
		$.each(e, function(i, e) {
			if (i == 'link')
			{
				linkHTML += '<div class="col-lg-5">\
					<input class="form-control" type="text" placeholder="Website Address" name="SitePath" value="'+e+'" />\
				</div>';
			}
			if (i == 'link_text')
			{
				linkHTML += '<div class="col-lg-5">\
				<input class="form-control" type="text" placeholder="Website Name" name="SiteName" value="'+e+'" />\
				</div>';
			}			
		});
		linkHTML += '<div class="col-lg-2 text-right-lg">\
				<a id="'+index+'" class="remove btn btn-danger btn-block" href="#remove">Remove</a>\
			</div>\
			</div>';
		$('#editForm .websites').empty().append(linkHTML);
		$('.remove').on('click',function() 
		{
			$(this).closest('.form-group').remove();
		});	
	});
	
	$('#editForm .form-control').each(function()
	{
		if (!($(this).attr('name') === undefined))
		{	
			if (!( ($(this).attr('name') == 'recruiting') || ($(this).attr('name') == 'enabled') || ($(this).attr('name') == 'SitePath') || ($(this).attr('name') == 'SiteName') ))
			{
				$(this).val(eval($(this).attr('name')));
			}
		}
	});
	$(function($){
		$("#editForm .site_phone").mask("(999) 999-9999");
	});
	doAutoComplete(affilTags,'.site_affiliation');
	doAutoComplete(stateTags,'.state');
	$('.textarea').summernote({
			  toolbar: [
		
				['style', ['bold', 'italic', 'underline', 'clear']],
			
				['para', ['ul', 'ol', 'paragraph']],
		
				['insert', ['link']], // no insert buttons
				['table', ['table']], // no table button
				['view', ['fullscreen', 'codeview']],
				
			  ]
			});
	
	
	$('#editSite').modal('show');
	$('#submitbutton.btn').on('click',function()
	{
		
		//var $btn = $(this).button('loading')
		var data = {
			sitePath: [],
			siteName: []
		};
		var error = 0;
		$('#editForm .form-control').each(function()
		{
			if (!($(this).attr('name') === undefined))
			{
				
				if ($(this).attr('name') == 'enabled')
				{
					if($(this).children().eq(0).is(':checked'))
					{
						data[$(this).attr('name')] = 1;
					}
					else
					{
						data[$(this).attr('name')] = 0;
					}
				}
				else
				if ($(this).attr('name') == 'recruiting')
				{
					if($(this).children().eq(0).is(':checked'))
					{
						data[$(this).attr('name')] = 1;
					}
					else
					{
						data[$(this).attr('name')] = 0;
					}
				}
				else
				if ($(this).attr('name') == 'SitePath')
				{
					data.sitePath.push($(this).val());
				}
				else			
				if ($(this).attr('name') == 'SiteName')
				{
					data.siteName.push($(this).val());
				}
				else
				if ($(this).hasClass('textarea'))
				{
					data[$(this).attr('name')] = $(this).code();
				}
				else
				{
					if ($(this).attr('required') == 'required')
					{
						if ($(this).val().length < 1)
						{
							error++;
						}
					}
					data[$(this).attr('name')] = $(this).val();
				}
			}
		});
		if (error == 0)
		{
			$.post('/sites/edit/'+id, data, function(data) {
				$('#editSite').modal('hide');
				location.reload();
				//$('body').html(data);
			});
		} else {
			alert('Please complete the required fields');
		}
//		$btn.button('reset')
	});
}

function doAutoComplete(tags,classname)
{
	$(function() 
	{
		$( classname ).autocomplete({
			 position: { my : "left top", at: "left top" },
		  source:  tags 
		});
	});
}

function changeSiteName()
{
	if($('.titleSwitch').text() == "Long Title")
	{
		$('.titleSwitch').text("Short Title");
		$('.titleSwitch').removeClass('btn-success');
		$('.titleSwitch').addClass('btn-info');
	}
	else
	{
		$('.titleSwitch').text("Long Title");
		$('.titleSwitch').addClass('btn-success');
		$('.titleSwitch').removeClass('btn-info');
	}
	var aux = $.trim($('#researchTitle').text());
	var longTitle =  $.trim($('#researchTitle').attr("name"));
	$('#researchTitle > p > strong').text(longTitle);
	$('#researchTitle').attr("name", aux);
}

function loadAnnouncements(admin)
{
		$('#addAnnoun').click(function(i) {
				$('.sdate').datepicker();
				$( ".edate" ).datepicker();
				$('.textarea').summernote({
				  toolbar: [
					//['style', ['style']], // no style button
					['style', ['bold', 'italic', 'underline', 'clear']],
				 //   ['fontsize', ['fontsize']],
					//['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
				 //   ['height', ['height']],
					['insert', ['link']], // no insert buttons
					['table', ['table']], // no table button
					['view', ['fullscreen', 'codeview']],
					//['help', ['help']] //no help button
				  ]
				});
			$('#addModal').modal('show');
		});
		// populate the fields on click
	    $('tr').click(function() {			
			
			$('.userDesc').remove();
			$('.homepageTitle').remove();
			$('.announcementTitle').remove();
			$('.startDate').remove();
			$('.endDate').remove();
			$('.imageUp').remove();
			$('.fileUp').remove();
			$('.clearAttach').remove();
				$('.userEmail').after('<div class="userDesc form-group">\
				  <div class="col-lg-3">  <label class="form-label" for="siteLeft">Description</label></div>\
				    <div class="col-lg-9"><div class="controls">\
					  <textarea name="siteLeft" id="siteLeftDesc"  rows="4" placeholder="Description" class="form-control textarea" required="required"></textarea>\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="imageUp form-group">\
				   <div class="col-lg-3"> <label class="form-label">Upload An Image<br /><span style="font-size:11px;font-style:italic;font-weight:bold">(Optional) Previous uploads will be overwritten</span></label></div>\
				    <div class="col-lg-9"><div class="controls">\
				     <input class="form-control"  type="file" name="image" id="image" />\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="fileUp form-group">\
				   <div class="col-lg-3"> <label class="form-label">Upload A PDF File<br /><span style="font-size:11px;font-style:italic;font-weight:bold">(Optional) Previous uploads will be overwritten</span></label></div>\
				    <div class="col-lg-9"><div class="controls">\
				     <input class="form-control" type="file" name="file" id="file"  />\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="clearAttach form-group">\
				   <div class="col-lg-3"> <label class="form-label" for="clearAttach">Clear All Attachments</label></div>\
				    <div class="col-lg-9"><div class="controls">\
				      <input class="input-control" autocomplete="off" value="1" name="clearAttach" type="checkbox" />\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="endDate form-group">\
				   <div class="col-lg-3"> <label class="form-label" for="email">End Date</label></div>\
				    <div class="col-lg-9"><div class="controls">\
				      <input class="form-control edatefield" autocomplete="off" type="text" name="edate" />\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="startDate form-group">\
				    <div class="col-lg-3"><label class="form-label" for="email">Start Date</label></div>\
				    <div class="col-lg-9"><div class="controls">\
				      <input class="form-control sdatefield" autocomplete="off" type="text" name="sdate" />\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="homepageTitle form-group">\
				    <div class="col-lg-3"><label class="form-label" for="email">Homepage Title</label></div>\
				    <div class="col-lg-9"><div class="controls">\
				      <input type="text" name="homepagetitle" id="homepagetitle" placeholder="Name displayed on homepage" class="form-control" required="required">\
				    </div></div>\
				  </div>');
				$('.userEmail').after('<div class="announcementTitle form-group">\
				   <div class="col-lg-3"> <label class="form-label" for="email">Announcement Title</label></div>\
				    <div class="col-lg-9"><div class="controls">\
				      <input type="text" name="announcementtitle" id="announcementtitle" placeholder="Announcement Title" class="form-control" required="required">\
				    </div></div>\
				  </div>');

			
		    	var id = $(this).attr('id');
		    	var email = $(this).attr('email');
				var enabled = $(this).attr('enabled');
				var homepagetitle = $(this).children().eq(6).html();
				$('.enabledbutton').remove();
				//alert(enabled);
				if (enabled > 0) {
					$('button.btn-success').after('<input class="enabledbutton btn btn-danger" type="submit" name="disable" form="editForm" value="Disable" />');
				} else {
					$('button.btn-success').after('<input class="enabledbutton btn btn-primary" type="submit" name="enable" form="editForm" value="Enable" />');
				}
				
				$('.enabledbutton').on("click",function() {
					$('#siteLeftDesc').val(' ');
				});
				
				$('#submitbutton').on("click",function() {
					$('#siteLeftDesc').val(' ');
				});
		    	var desc = $(this).children().eq(0).html();
				var sdate = $(this).children().eq(3).html();
				var edate = $(this).children().eq(4).html();
				
				if (desc.length < 1) {
				desc = '';
				}
				
				var announcementtitle = $(this).children().eq(1).html();
				//alert(desc);
		    	// Fill the vars to the appr inputs
				if (email.length < 1) {
				email = 'N/A';
				}
				
				if (admin) {
					
					
		    	$('#id').val(id);
				$('#email').html(email);
				$('#siteLeftDesc').val(desc);
				$('#announcementtitle').val(announcementtitle);
				$('#homepagetitle').val(homepagetitle)
				$('.sdatefield').val(sdate);
				$('.edatefield').val(edate);
				
				$('#editModalLabel').html('Edit Announcement');
				$('.sdatefield').datepicker();
				$( ".sdatefield" ).datepicker();
				$( ".edatefield" ).datepicker();
			
				$('.textarea').summernote({
  toolbar: [
    //['style', ['style']], // no style button
    ['style', ['bold', 'italic', 'underline', 'clear']],
 //   ['fontsize', ['fontsize']],
    //['color', ['color']],
    ['para', ['ul', 'ol', 'paragraph']],
 //   ['height', ['height']],
    ['insert', ['link']], // no insert buttons
    ['table', ['table']], // no table button
	['view', ['fullscreen', 'codeview']],
    //['help', ['help']] //no help button
  ]
});
				
				
				// Dhow the modal (will happen AFTER all the vars are filled out)
				if (!$(this).hasClass('head')) {
					$('#editModal').modal('show');
				}
				 } else {
					window.location.href='/announcements/?id='+id;
				 }
				 
				 $('div.modal-body > div.form-group > div:nth-child(1)').addClass('text-right-lg');
	    });
}
