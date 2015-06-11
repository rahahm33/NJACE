<?php
class Controller_Home extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('main','views/home/main.html');
	}
	
	public function footer($output = '')
	{	
		$this->f3->set('feed',Model_Home::newsfeed());
		$this->f3->set('footer','views/footer.html');
	}
	
	public function index()
	{	
		parent::index();
		
		$this->f3->set('announcements',1);
		$this->f3->set('images',Model_Sites::images());
		echo View::instance()->render('views/layout.html');
	}
	
	public function siteMap($view)
	{
		parent::index();
		
		$this->f3->set('main','views/home/sitemap.html');
		//echo $view;
		// Set Page Name
		$this->config('page_name','Site Map');
		if(empty($view))
		{
			$this->f3->set('siteMap', Model_Home::siteMap());
			
			// Output Layout
			echo View::instance()->render('views/layout.html');
			
		}
		else
		{
			header('Content-type: text/xml');
			echo Model_Home::siteMapXML();
		}
		
	}
}
?>