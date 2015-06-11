<?php
class Controller_Media extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('siteID',$output);		
		$this->f3->set('data',Model_Medias::data($output));
		$this->f3->set('js','loadManageMedia('.$output.')');
                $this->f3->set('setupEditableTextboxes','setupEditableTextboxes()');
		$this->f3->set('addVideoPopUp','addYoutubeLink('.$output.')');
		$this->f3->set('modal-addimage','views/media/add-image.html');
		$this->f3->set('modal-addvideo','views/media/add-video.html');
		$this->f3->set('modal-edit','views/media/edit.html');
		$this->f3->set('main','views/media/main.html');
	}
	
	
	public function body($id)
	{	
		parent::index();
		
		$this->f3->set('shortname',Bridge::sites_info($id,'short_name'));
		$this->config('page_name','Manage Media | '.$this->f3->get('shortname'));
		if (!($this->f3->get('shortname')))
		{
			$this->f3->error(404);
		}
		$this->main($id);
		echo View::instance()->render('views/layout.html');
	}
	
	public function info($id)
	{
		if ($_POST['picid'])
		{
			echo json_encode(Model_Medias::info($_POST['picid']));
		}
	}
	
	public function delete($id)
	{
		//Automatically, generating a new cropped image will override the old one
		$this->db->query("DELETE FROM images WHERE id = '".$id);
		//unlink("../img/raw/".rawurlencode($fileSrc));
	}
	
	public function crop($id)
	{
		parent::index();
		
		
		$arr = Model_Medias::info($id);
		$src = $arr['src'];
		$fullSRC = $arr['dir'].$arr['src'];
		
		$this->f3->set('src', $fullSRC);
		
		$this->f3->set('js', 'crop('.$id.')');
		
		$this->f3->set('main','views/media/crop.html');
		// Set Page Name
		$this->config('page_name','Crop Image');
		
		// Output Layout
		echo View::instance()->render('views/layout.html');
	}
	
	public function update($id)
	{
		$this->f3->set('shortname',Bridge::sites_info($id,'short_name'));
		$this->db->query("UPDATE images SET priority = '".$_POST['priority']."' WHERE id = '".$_POST['picid']."'");
		$this->main($id);
		echo View::instance()->render('views/media/main.html');
	}
	
	public function saveCrop($id)
	{
		$arr = Model_Medias::info($id);
		$siteID = $arr['group_id'];
		$src       = '' . ltrim($_POST['src'],'/');
		$filebreak = explode('/', $src);
		$dstdir = 'images/';
		$dstfile = rand(0,999) .'_'. $filebreak[2];
		$dst       = $dstdir . $dstfile;
		
		CropImage::crop($src, $dst, $_POST['imgdata']);
		
		//update the database
		$this->db->query("UPDATE images SET src='".$dstfile."', dir='/".$dstdir."' WHERE id=".$id);
		
		echo(json_encode($siteID));
	}
	
	public function addVideo($siteID)
	{
		//The call back function for adding a you-tube link
		$requested_info = array("title", "description");
		Model_Medias::get_youtube_info($_POST['link'], $requested_info, $siteID);
		
	}
        
        public function updatePicture($picID)
        {
            
        }
}
?>