<?php
class Controller extends Model
{
	public $f3, $mybb, $db, $mybbi;
	
	public function __construct($f3)
	{
		$this->f3 = $f3;
		$this->loadmybb();	
		$this->config();
	}		
	
	public function header($output = '')
	{	
		$hive = $this->f3->get('PARAMS');
		$this->f3->set('route',$hive);
		$this->f3->set('headinclude','views/headinclude.html');
		$this->f3->set('header','views/header.html');
	}
	
	public function main($output = 'views/main.html')
	{
		$this->f3->set('main',$output);
	}
	
	public function index ()
	{
		$this->header();
		$this->main();
		$this->footer();
	}
	
	public function footer($output = 'views/footer.html')
	{
		$this->f3->set('footer',$output);
	}
	
	public function redirect($uri = '/',$error = '')
	{
		$extrahead = '<META http-equiv="refresh" content="2;URL='.$uri.'">';
		$this->f3->set('uri',$uri);
		$this->f3->set('error',$error);
		$this->f3->set('main','views/redirect.html');
		$this->f3->set('extrahead',$extrahead);
		echo View::instance()->render('views/layout.html');
		exit;
	}
	
}
?>