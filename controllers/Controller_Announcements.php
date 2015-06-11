<?php
class Controller_Announcements extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('data',Model_Announcements::data($output));
		$this->f3->set('main','views/announcements/main.html');
	}
	
	public function footer($output = '')
	{	
		$this->f3->set('footer','views/footer.html');
	}
	
	public function body($type,$id)
	{	
		parent::index();
		$this->main($id);
		$this->f3->set('announcements',0);
		echo View::instance()->render('views/layout.html');
	}
	
	public function index()
	{
		parent::index();
		
		$js = '<script type="text/javascript">
		loadAnnouncements('.$this->f3->get('admin').');
		</script>';
		
		$this->f3->set('output',Model_Announcements::getAnnoun($_GET['sort'],$_GET['order']));		
		$this->f3->set('js',$js);
		
		if (intval($_GET['id']))
		{
			$this->f3->reroute(Model_Announcements::getURI($_GET['id']));
		}
		if (!empty($_POST))
		{
			if (!empty($_POST['disable']))
			{
				Model_Announcements::updatePost($_POST,'disable');
			} else
			if (!empty($_POST['enable']))
			{
				Model_Announcements::updatePost($_POST,'enable');
			}
			else
			if (!empty($_POST['add']))
			{
				Model_Announcements::updatePost($_POST,'add');
			}
			else
			if (!empty($_POST['delete']))
			{
				Model_Announcements::updatePost($_POST,'delete');
			}
			else
			{
				//Bridge::print_r2($_POST);
				Model_Announcements::updatePost($_POST);
			}
			$this->redirect('/announcements','Announcement has been updated.');
		}

		$this->f3->set('types',Model_Announcements::getTypes());
		$this->f3->set('edit','views/announcements/edit.html'); 
		$this->f3->set('add','views/announcements/add.html');
		$this->f3->set('main','views/announcements/index.html');
		$this->config('page_name','Announcements');
		echo View::instance()->render('views/layout.html');
	}

}
?>