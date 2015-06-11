<?php
class Controller_Login extends Controller
{
	public $f3,$mybb,$mybbi,$db;	
	
	public function main($output = '')
	{
		if (empty($output))
		{
			$this->f3->set('main','views/login/main.html');
		}
		else
		{
			$this->f3->set('main',$output);
		}
	}
	
	public function index()
	{	
		// Redirect to home page if the user is already logged in.
		if ($this->mybb->user['uid'])
		{
			//redirect('/home/','Redirecting to home');
			$this->f3->reroute('home');
		}
		
		parent::index();
		if (!empty($_POST))
		{
			Model_Login::login();
		}		
		$this->config('page_name','Login');
		$this->f3->set('announcements',0);
		
		echo View::instance()->render('views/layout.html');
	}	

}
?>