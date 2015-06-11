<?php
class Controller_Test extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function editMedia($siteID)
	{
		//$shortname = Bridge::sites_info($siteID, 'short_name');

		parent::index();
		$this->f3->set('main','views/sites/media.html');
		$result = Model_Media::media($siteID);
		$this->f3->set('mediaClick', '<script type="text/javascript"> mediaClick('.json_encode($result).'); </script>');
		$this->f3->set('startUpload', '<script type="text/javascript">startUpload('.$siteID.');</script>');
		
		$this->f3->set('records', Model_Media::bringMediaRecords($siteID));
		
		$this->f3->set('upload', '<script type="text/javascript"> fileUploader('.$siteID.'); </script>');
		
		//Press the button that will redirect the user to the research site.
		if(isset($_POST['update']))
		{
			for($i=0; $i<$nb_images; $i++)
				echo Model_Media::updatepictures($_POST['ticket_id']);
		}
		
		if(isset($_POST['submit2']))
		{
			$requested_info = array("title", "description");
			print_r(Bridge::get_youtube_info($_POST['url'], $requested_info));

			exit();
		}
		// Set Page Name
		$this->config('page_name','Edit Media');
		
		//output the layout
		echo View::instance()->render('views/layout.html');
		
	}
}
	
?>