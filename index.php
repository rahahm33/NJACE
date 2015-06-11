<?php

// Kickstart the framework
$f3=require($_SERVER['DOCUMENT_ROOT'].'/lib/base.php');

// Load Model and Controller Classes
$f3->set('AUTOLOAD',''.$_SERVER['DOCUMENT_ROOT'].'/controllers/; '.$_SERVER['DOCUMENT_ROOT'].'/models/');

// Error Reporting
$f3->set('DEBUG',3);
if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

if (!($f3->get('DEBUG')))
{
	$f3->set('ONERROR',
		function($f3) {
			$f3->reroute('/home');
		}
	);
}


// Start Routing the URLs

/****************** Home Controller ******************/
$f3->route('GET|HEAD /',
    function($f3) {
        $f3->reroute('/home');
    }
);
$f3->route('GET|HEAD /index',
    function($f3) {
        $f3->reroute('/home');
    }
);
$f3->route('GET /home','Controller_Home->index');

$f3->route('GET /site-map',
	function($f3,$params) {
		$view = new Controller_Home($f3);
		$view->siteMap(0);
    }
);
$f3->route('GET /sitemap.xml',
	function($f3,$params) {
		$view = new Controller_Home($f3);
		$view->siteMap(1);
    }
);
/****************** Ticket Controller ******************/
//submit ticket route
$f3->route('GET|POST /ticket/submit','Controller_Ticket->submit');

$f3->route('GET|POST /ticket/manage',
    function($f3,$params) {
		$view = new Controller_Ticket($f3);
		$view->manage(1);
    }
);

$f3->route('GET|POST /ticket/view',
    function($f3,$params) {
		$view = new Controller_Ticket($f3);
		$view->manage(0);
    }
);

//submit a log hour route
$f3->route('GET|POST /log-hours','Controller_Ticket->submitLogHours');
//view log hours report route
$f3->route('GET|POST /log-hours/report','Controller_Ticket->viewLogHoursReport');
//edit log hours
$f3->route('GET|POST /log-hours/edit/@id',
	function($f3,$params) {
		$view = new Controller_Ticket($f3);
		$view->editLogHours($params['id']);
    }
);

/****************** Login Controller ******************/
$f3->route('GET|POST /login','Controller_Login->index');
$f3->route('GET|HEAD /login/reset',
    function($f3) {
        $f3->reroute('/forum/member.php?action=lostpw');
    }
);



/****************** Announcements Controller ******************/
$f3->route('GET|POST /announcements','Controller_Announcements->index');
$f3->route('GET /event/@id/*',
    function($f3,$params) {
		$view = new Controller_Announcements($f3);
		$view->body('event',$params['id']);
    }
);

$f3->route('GET /news/@id/*',
    function($f3,$params) {
		$view = new Controller_Announcements($f3);
		$view->body('news',$params['id']);
    }
);

/****************** Resources Controller ******************/
$f3->route('GET /resources',
    function($f3,$params) {
		$view = new Controller_Resources($f3);
		$view->index();
    }
);

$f3->route('GET /resources/@fid/*',
    function($f3,$params) {
		$view = new Controller_Resources($f3);
		$view->body($params['fid'],0);
    }
);
$f3->route('GET /resources/@fid',
    function($f3,$params) {
		$view = new Controller_Resources($f3);
		$view->body($params['fid'],1);
    }
);

/****************** Research Sites Controller ******************/

$f3->route('GET /@id/*',
    function($f3,$params) {
		$view = new Controller_Sites($f3);
		$view->body($params['id'],0);
    }
);
$f3->route('GET /@id',
    function($f3,$params) {
		$view = new Controller_Sites($f3);
		$view->body($params['id'],1);
    }
);

$f3->route('POST /sites/edit/@id',
    function($f3,$params) {
		$view = new Controller_Sites($f3);
		$view->edit($params['id']);
    }
);

//edit media
$f3->route('GET|POST /@id/manage-media',
	function($f3,$params) {
		$view = new Controller_Test($f3);
		$view->editMedia($params['id']);
    }
);
/****************** Manage Members Controller ******************/
$f3->route('GET /@id/members',
    function($f3,$params) {
		$view = new Controller_Members($f3);
		$view->body($params['id']);
    }
);

$f3->route('POST /@id/members/create',
    function($f3,$params) {
		$view = new Controller_Members($f3);
		$view->create($params['id']);
    }
);

$f3->route('POST /@id/members/add',
    function($f3,$params) {
		$view = new Controller_Members($f3);
		$view->add($params['id']);
    }
);

$f3->route('POST /@id/members/edit',
    function($f3,$params) {
		$view = new Controller_Members($f3);
		$view->edit($params['id']);
    }
);

$f3->route('POST /@id/members/info',
    function($f3,$params) {
		$view = new Controller_Members($f3);
		$view->info($params['id']);
    }
);

/****************** Manage Media Controller ******************/

//Media Page
$f3->route('GET /@id/media',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->body($params['id']);
    }
);

//Crop Page
$f3->route('GET /crop/@id',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->crop($params['id']);
    }
);

//Crop an Image
$f3->route('POST /crop/@id',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->saveCrop($params['id']);
    }
);

//Update Media Priority
$f3->route('POST /@id/media',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->update($params['id']);
    }
);

//Delete Media
$f3->route('POST /@id/media/delete',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->delete($params['id']);
    }
);

//Media Information
$f3->route('POST /@id/media/info',
    function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->info($params['id']);
    }
);

//Update a picture
$f3->route('POST /media-update/@id',
    function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->updatePicture($params['id']);
    }
);

//Add Video Link
$f3->route('POST /@id/media/add-video',
	function($f3,$params) {
		$view = new Controller_Media($f3);
		$view->addVideo($params['id']);
    }
);

/****************** Recruit (How Can I Help) Controller ******************/

$f3->route('POST /how-can-i-help',
    function($f3,$params) {
		$view = new Controller_Recruit($f3);
		$view->mail();
    }
);


$f3->route('GET /how-can-i-help',
    function($f3,$params) {
		$view = new Controller_Recruit($f3);
		$view->body();
    }
);

$f3->route('GET /map',
    function($f3,$params) {
		$view = new Controller_Recruit($f3);
		$view->map();
    }
);

$f3->route('GET /@id/map',
    function($f3,$params) {
		$view = new Controller_Recruit($f3);
		$view->map($params['id'],1);
    }
);


$f3->route('GET /@id/*/map',
    function($f3,$params) {
		$view = new Controller_Recruit($f3);
		$view->map($params['id'],0);
    }
);



// Run the framework
$f3->run();
