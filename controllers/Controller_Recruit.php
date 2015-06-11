<?php
class Controller_Recruit extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('main',$output);
	}
	
	public function footer($output = '')
	{
		return false;
	}
	
	
	public function body()
	{	
		parent::index();
		$this->config('page_name','How can I help?'); 
		$this->main('views/recruit/main.html');
		$this->f3->set('sites',Model_Recruit::data());
		$this->f3->set('js','<script type="text/javascript">loadRecruit();</script>');
		echo View::instance()->render('views/layout.html');
	}
	
	public function mail()
	{
		if (!empty($_POST['submit']))
		{
			$fname = $this->db->escape_string($_POST['fname']);
			$lname = $this->db->escape_string($_POST['lname']);
			$phone = $this->db->escape_string($_POST['phone']);
			$email = $this->db->escape_string($_POST['email']);
			$contact = $this->db->escape_string($_POST['contact']);
			$contacttime = $this->db->escape_string($_POST['contacttime']);
			if (!empty($_POST['study']))
			{
				$study = 1;
			}
			if (!empty($_POST['sites']))
			{
				$sites = $_POST['sites'];
				$site_email = Bridge::sites_info($sites,'site_email');
				$site_name = Bridge::sites_info($sites,'short_name');
				$site_phone = Bridge::sites_info($sites,'site_phone');
				$site_street = Bridge::sites_info($sites,'street');
				$site_city = Bridge::sites_info($sites,'city');
				$site_state = Bridge::sites_info($sites,'state');
				$site_zip = Bridge::sites_info($sites,'zip');
				$message[0] = 'Dear '.$fname .' '.$lname.',<br /><br />

		Thank you for your interest in the New Jersey Autism Center of Excellence (NJACE) Research Site: '.$site_name.'. We have recorded the information you entered and your interest in participating in their research.  For your information the contact information for this research site is:
		<br /><br />
		Email:' . "&emsp;&emsp;" . ''.$site_email.'<br />
		Phone:' . "&emsp;&emsp;" . ''.$site_phone.'<br />
		Address:' . "&emsp;&emsp;" . ''.$site_street.', '.$site_city.', '.$site_state.' '.$site_zip.'<br /><br />

		Someone from the NJACE "'.$site_name.'" research site will reach out to coordinate your participation.  Thanks again for your interest and in advance for your help in advancing the research with regards to Autism.
		<br /><br />
		Best wishes,<br />
		The NJACE Coordinating Center at Montclair State University (CC MSU)<br />
		http://njace-cc.montclair.edu';
			$message[1] = 'Dear NJACE Research Site Team: '.$site_name.'<br /><br />

		Below is contact information for a person interested in participating in your research:<br />
		Name:' . "&emsp;&emsp;" . ''.$fname.' '.$lname.'<br />
		Email:' . "&emsp;&emsp;" . ''.$email.'<br />
		Phone:' . "&emsp;&emsp;" . ''.$phone.'<br /><br />

		Please reach out to this individual directly to coordinate his or her participation in your research.<br /><br />

		For your information the below email was sent to this individual.
		<br /><br />
		Best wishes,<br />
		NJACE CC MSU<br />
		http://njace-cc.montclair.edu
		<br /><br />
		-----------------------------------------------------------------<br />
		Copy of email sent to potential participant:<br />
		-----------------------------------------------------------------<br />
		<p style="margin-left:10px;">
		'.$message[0].'
		</p>
		-----------------------------------------------------------------
		';

			Bridge::sendmail($site_email,'NJACE-CC','NJACE-CC: New Contact Information',$message[1]);
			}
			else
			{
				$message[0] = 'Dear '.$fname .' '.$lname.',<br /><br />

		Thank you for your interest in the New Jersey Autism Center of Excellence (NJACE) Study Opportunities. We have recorded the information you entered and your interest in study opportunities.
		<br /><br />
		Someone from NJACE will reach out to you in the future.  Thanks again for your interest and in advance for your help in advancing the research with regards to Autism.
		<br /><br />
		Best wishes,<br />
		The NJACE Coordinating Center at Montclair State University (CC MSU)<br />
		http://njace-cc.montclair.edu';
			$message[1] = 'Dear NJACE-CC,
		<br /><br />
		Below is contact information for a person interested in participating in study opportunities:<br />
		Name:' . "&emsp;&emsp;" . ''.$fname.' '.$lname.'<br />
		Email:' . "&emsp;&emsp;" . ''.$email.'<br />
		Phone:' . "&emsp;&emsp;" . ''.$phone.'<br /><br />

		Please reach out to this individual.<br /><br />

		Best wishes,<br />
		NJACE CC MSU<br />
		http://njace-cc.montclair.edu';

			}
			

			
			Bridge::sendmail($email,'NJACE-CC','NJACE-CC: Thanks for your interest',$message[0]);
			

			/*$admins = get_users(2);
			foreach ($admins as $user)
			{
				Bridge::sendmail($user['email'],'NJACE-CC','NJACE-CC: New Contact Information',$message[1]);
			}*/
			
			
			$phone = preg_replace('/[^0-9+]/', '', $phone);
			
		//	echo("INSERT INTO mailinglist (fname, lname, phone, email, sites, study, time, ip, contact, contacttime) VALUES ('$fname','$lname','$phone','$email', '$sites', '$study', '".time()."', '".$_SERVER['REMOTE_ADDR']."', '$contact', '$contacttime')");
			
			$query = $this->db->query("INSERT INTO mailinglist (fname, lname, phone, email, sites, study, time, ip, contact, contacttime) VALUES ('$fname','$lname','$phone','$email', '$sites', '$study', '".time()."', '".$_SERVER['REMOTE_ADDR']."', '$contact', '$contacttime')");
			
		}
	}

	
	public function map($id = '',$seo)
	{	
		if ($id)
		{
			if ($seo)
			{
				$recruiting = Bridge::sites_info($id,'recruiting');
				$shortname = Bridge::sites_info($id,'short_name');
				if (empty($recruiting))
				{
					$this->f3->reroute('/map');
				}
				else
				{					
					$this->f3->reroute('/'.$id.'/'.Bridge::seo($shortname).'/map');
				}
			}
		}
		
		parent::index();
		$this->config('page_name','Map'); 
		$this->main('views/recruit/map.html');
	//	$this->f3->set('header','');
		$this->f3->set('extrahead','<style type="text/css">footer{display:none;visibility;hidden}
		html, body, #map, main, main>div, main>div>div {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
		</style>');
		
		$this->f3->set('data',Model_Recruit::map($id));
		echo View::instance()->render('views/layout.html');
	}
}
?>