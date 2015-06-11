<?php
class Controller_Sites extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('siteID',$output);
		$this->f3->set('grantname',Bridge::sites_info($output,'grant_title')); 
		$this->f3->set('shortname',Bridge::sites_info($output,'short_name')); 
		$this->config('page_name',$this->f3->get('shortname')); 
		
		$this->f3->set('desc',Bridge::sites_info($output,'site_desc')); 
		$this->f3->set('shortDesc',Bridge::sites_info($output,'short_desc')); 
		$this->f3->set('crit',Bridge::sites_info($output,'site_crit')); 
		
		$this->f3->set('site_email',Bridge::sites_info($output,'site_email')); 
		$this->f3->set('site_phone',Bridge::sites_info($output,'site_phone')); 
		$this->f3->set('street',Bridge::sites_info($output,'street')); 
		$this->f3->set('city',Bridge::sites_info($output,'city')); 
		$this->f3->set('state',Bridge::sites_info($output,'state')); 
		$this->f3->set('zip',Bridge::sites_info($output,'zip')); 	
		$this->f3->set('enabled',Bridge::sites_info($output,'enabled')); 
		$this->f3->set('recruiting',Bridge::sites_info($output,'recruiting')); 				
		$siteaffil = trim(rtrim(ltrim(Bridge::sites_info($output,'site_affiliation'))));
		$sitename =  trim(rtrim(ltrim(Bridge::sites_info($output,'site_name'))));	
		if ( (Bridge::sites_info($output,'site_affiliation')) && (Bridge::sites_info($output,'site_name')) )
		{
			
			if ($siteaffil == $sitename)
			{
				$this->f3->set('name',$siteaffil);
			}
			else
			{
				$this->f3->set('name',$sitename . ', ' . $siteaffil);
			}
		}
		else
		{
			$this->f3->set('name',$siteaffil);
		}
		
		$groups = Bridge::users_groups($this->mybb->user['uid']);	
		$groups = explode(',',$groups);	
		$gLevel = Bridge::users_groups($this->mybb->user['uid'],$output);
		
		if ( ((in_array($output,$groups)) && (!empty($gLevel))) || $this->f3->get('admin') )
		{
			$this->f3->set('groupOwner',1);
		}
		
		
		if ($this->f3->get('groupOwner'))
		{
			$this->f3->set('button',array(
					0 => Model_Sites::button(),		
					8 => Model_Sites::button('/'.$output.'/members',null,'btn-danger noModal')			
				)
			);
		}
		
		if ( (($this->f3->get('grantname')) && ($this->f3->get('shortname'))) && ( ($this->f3->get('grantname') != $this->f3->get('shortname')) ) )
		{
			$this->f3->set('button[9]',Model_Sites::button('javascript:void(0);','Short Title','btn-success titleSwitch noModal'));
		}
		else
		{
			$this->f3->set('grantname',Bridge::sites_info($output,'short_name'));	
		}
		
		$this->f3->set('recruiting',Bridge::sites_info($output,'recruiting'));
		
		if ($this->f3->get('recruiting'))
		{
			$this->f3->set('button[10]',Model_Sites::button('/how-can-i-help','Currently Recruiting','btn-info noModal'));
		}
		else
		{
			$this->f3->set('button[10]',Model_Sites::button('/how-can-i-help','Currently Not Recruiting','btn-default noModal'));
		}
		
		
		$this->f3->set('t',Bridge::sites_info($output,'short_name'));	
		$this->f3->set('images',Model_Sites::images($output));		
		$this->f3->set('members',Model_Sites::members($output));
		$this->f3->set('links',Model_Sites::links($output));
		$this->f3->set('resources',Model_Resources::data(7,$output));
		$this->f3->set('modal','views/sites/edit.html');
		
		$this->f3->set('js','loadSites('.$output.',
			'.json_encode(Model_Sites::tags(0)).',
			'.json_encode(Bridge::sites_info($output,'grant_title')).',
			'.json_encode(Bridge::sites_info($output,'short_name')).',
			'.json_encode(Bridge::sites_info($output,'site_affiliation')).',	
			'.json_encode(Bridge::sites_info($output,'site_desc')).',
			'.json_encode(Bridge::sites_info($output,'short_desc')).',
			'.json_encode(Bridge::sites_info($output,'site_crit')).',
			'.json_encode(Bridge::sites_info($output,'site_email')).',
			'.json_encode(Bridge::sites_info($output,'site_phone')).',
			'.json_encode(Bridge::sites_info($output,'street')).',
			'.json_encode(Bridge::sites_info($output,'city')).',
			'.json_encode(Bridge::sites_info($output,'state')).',
			'.json_encode(Bridge::sites_info($output,'zip')).',
			'.json_encode(Model_Sites::tags(1)).',
			'.json_encode(Model_Sites::websites($output)).',
			'.json_encode(Bridge::sites_info($output,'site_name')).'
			);');
		$this->f3->set('main','views/sites/main.html');
	}
	

	
	public function edit($id)
	{		
		if (($id))
		{
			$siteNames = array();
			$sitePaths = array();
			$arPos = 0;
		
			foreach ($_POST as $key => $val)
			{				
				if (!($key == 'sitePath' || $key == 'siteName'))
				{
					$this->db->query("UPDATE sites SET ".$key."='".$this->db->escape_string($val)."' WHERE id='".$id."'");
				}
				else
				{		
					if ($key == 'siteName')
					{			
						foreach ($val as $x)
						{
							$siteNames[] = $x;
						}						
					}
					if ($key == 'sitePath')
					{
						foreach ($val as $x) 
						{
							$sitePaths[] = $x;
						}
					}
				}				
			}
			
			$this->db->query("DELETE FROM links WHERE link_id=".$id);
			foreach ($sitePaths as $x) 
			{
				if (!empty($x))
				{
					$this->db->query("INSERT INTO links(link_id, link, link_text) VALUES(".$id.", '".$this->db->escape_string($x)."', '".$this->db->escape_string($siteNames[$arPos++])."')");
				}
			}
		}
	}
	
	public function body($id,$seo = 0)
	{	
		if ($seo)
		{
			$shortname = Bridge::sites_info($id,'short_name');
			if (empty($shortname))
			{
				$this->f3->error(404);
			}
			else
			{
				$this->f3->reroute('/'.$id.'/'.Bridge::seo($shortname));
			}
		}
		parent::index();
		$this->main($id);
		echo View::instance()->render('views/layout.html');
	}
	
}
?>