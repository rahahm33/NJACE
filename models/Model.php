<?php
class Model
{
	public $f3, $mybb, $db, $mybbi;
	
	
	protected function loadmybb()
	{
		define('IN_MYBB', NULL);	
		global $mybb, $templates, $lang, $query, $db, $cache, $plugins, $displaygroupfields;
	//	global $debug, $templatecache, $templatelist, $maintimer, $globaltime, $parsetime, $header,$footer,$headerinclude;
		require_once $_SERVER['DOCUMENT_ROOT'] . '/forum/global.php';			
		$MyBBI = new MyBBIntegrator($mybb, $db, $cache, $plugins, $lang, $config); 
		
		$this->mybbi = $MyBBI;
		$this->mybb = $mybb;
		$this->db = $db;
	}
	
	public function config($var,$value = null)
	{	
		date_default_timezone_set('America/New_York');
		$defaults = array(
			'url',
			'title',
			'short_title',
			'page_name',
			'admin',
			'member',
			'mybb',
			'usermenu',
			'resourcesmenu',
			'sitesmenu'
		);
		
		
		foreach ($defaults as $key)
		{			
			$config[$key] = Bridge::defaults($key);		
		}
		
		if ($value)
		{
			$config[$var] = $value;
		}
		
		foreach ($config as $cfg=>$key)
		{			
			$this->f3->set($cfg,$key);			
		}
	}
	
	
}