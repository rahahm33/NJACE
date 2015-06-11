<?php
class Controller_Resources extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		if (Model_Resources::data($output))
		{
			$this->f3->set('output',Model_Resources::data($output));
		}
		else
		{
			$this->f3->error(404);
		}
		$this->f3->set('main','views/resources/main.html');
	}
	
	public function index()
	{	
		parent::index();		
		$this->config('page_name','Resources');		
		echo View::instance()->render('views/layout.html');
	}
	
	public function body($fid,$seo = 0)
	{	
		$shortname = get_forum($fid);
		if ($seo)
		{
			
			if (empty($shortname['name']))
			{
				$this->f3->error(404);
			}
			else
			{
				
				$this->f3->reroute('/resources/'.$fid.'/'.Bridge::seo($shortname['name']));
			}
		}
		parent::index();
		$this->config('page_name',$shortname['name'] . ' | Resources');	
		$this->main($fid);
		echo View::instance()->render('views/layout.html');
	}
	
}
?>