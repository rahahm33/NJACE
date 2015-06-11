<?php
class Model_Recruit extends Model
{
	public $f3,$mybb,$mybbi,$db;

	public function map($siteID = '')
	{
		if (intval($siteID))
		{
			$locate = 'AND id = \''.$siteID.'\'';
		}
		
		$contact = $this->db->query("SELECT * FROM sites WHERE `recruiting` = '1' AND `street` != '' AND `city` != '' AND `state` != '' AND `zip` != '' ".$locate."");
		$Markers = array();
		
		while($add = $this->db->fetch_array($contact)) 
		{
			$PiAndCoPi ="";
			$info="";
			$QgetPeople = $this->db->query("SELECT *,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users LEFT JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '".$add['id']."' ORDER BY newsort ASC LIMIT 1");				
			$co_IP = $this->db->query("SELECT *,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users LEFT JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '".$add['id']."' AND users_role.role = 'Co-Investigator' ORDER BY newsort ASC");
			
			while ($row2 = $this->db->fetch_array($QgetPeople)) 
			{
				$role=Bridge::users_role($row2['user_id'],$add['id']);
				$words = explode(" ", $role);
				$acronym = "";

				foreach ($words as $w) {
				  $acronym .= $w[0];
				}
				$PiAndCoPi .= $row2['fname']. ' '. $row2['lname'] . ' (';
				if(!empty($role))
				{
					$PiAndCoPi .= $acronym . '); ';
				}
			}
			while ($row3 = $this->db->fetch_array($co_IP)) 
			{
				$acronym ="Co-PI";
				
				$PiAndCoPi .= $row3['fname'] . ' ' . $row3['lname'] . ' (';
				if(!empty($role))
				{
					$PiAndCoPi .= $acronym . '); ';
				}
			}
			$PiAndCoPi = trim($PiAndCoPi,'; ');
			$PiAndCoPi = '<i>' . $PiAndCoPi . '</i>';
			
			$q = $this->db->query("select * from sites where `id` = '".$add['id']."'");
			$contact1 = $this->db->query("select * from sites where `id` = '".$add['id']."'");
			
			while ($row1 = $this->db->fetch_array($q)) 
			{
				$recruit = $row1['id'];
				$email=$row1['site_email'];
				$phone=$row1['site_phone'];
				
				if(!empty($email))
				{
					if (!empty($this->mybb->user['uid']))
					{
						$info .= '<a href="'.Bridge::defaults('url').'admin/email/?recruit='.$recruit.'">' .$email.'</a><br/>';
					}
					else
					{
						$info .= '<a href="mailto:'.$email.'">' .$email.'</a><br/>';
					}
				}
				
				if(!empty($phone))
				{
					$info .= $phone;
					$info .= '<br/>';
				}
				
				if($this->db->num_rows($contact1))
				{	
					while($add1 = $this->db->fetch_array($contact1)) 
					{
						if(!empty($add1['street']) && !empty($add1['city']) && !empty($add1['state']) && $add1['zip']!='00000')
						{
							$info .= $add1['street'] . '<br />';
							$info .= $add1['city'] . ', ' . $add1['state'] . ' ' . $add1['zip'];
						}
					}
				}						
			}
			
			
			$Address = $add['street'] . ', ' . $add['city'] . ', ' . $add['state'] . ' ' . $add['zip'];
			$Address = urlencode($Address);
			$request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&sensor=true";
			$xml = simplexml_load_file($request_url) or die("url not loading");
			$status = $xml->status;
			if ($status=="OK") {
				$Lat = $xml->result->geometry->location->lat;
				$Lon = $xml->result->geometry->location->lng;
				$LatLon = $Lat.','.$Lon;
				$name = '<a style="font-weight:bold; font-size:14px;" href="'. Bridge::defaults('url') . $add['id'].'/'.Bridge::seo($add['short_name']).'/">'.$add['short_name'] .'</a>';
				
				$Markers[] = array($name, $Lat, $Lon, $PiAndCoPi, $info);
			}
		}

		$points = array();
		for($i=0; $i<count($Markers); $i++)
		{			
			$name = $Markers[$i][0];
			$latitude = $Markers[$i][1][0];
			$longitude = $Markers[$i][2][0];
			$PiAndCoPi = $Markers[$i][3];
			$info = $Markers[$i][4];
			$mutual = false;
			
			for($j=0; $j<count($points); $j++)
			{
				if(($points[$j][1] == $latitude) && ($points[$j][2] == $longitude))
				{
					$points[$j][0] .= '|'. $name;
					$points[$j][3] .= '|'. $PiAndCoPi;
					$points[$j][4] .= '|'. $info;
					$mutual = true;
					break;
				}
			}
			if(!$mutual)
			{
				$aux = array($name, (string)$latitude, (string)$longitude, $PiAndCoPi, $info);
				$points[]= $aux;
			}
		}


		//showing the addresses
		$data .='<div id="map"></div> ';
		$data .= '<script type="text/javascript">loadMap('.json_encode($points).');</script>';
		return $data;
	}
	
	public function data() 
	{
		$query = $this->db->query("SELECT * FROM sites WHERE enabled = 1 AND recruiting = 1 ORDER BY id ASC, site_crit DESC");
		while ($row = $this->db->fetch_array($query))
		{
			$data .= '<tr>';
			if (empty($row['site_crit']))
			{
				$crit = 'Not Specified';
			}
			else
			{
				$crit = $row['site_crit'];
			}

			$QgetPeople = $this->db->query("SELECT *,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users LEFT JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '".$row['id']."' ORDER BY newsort ASC LIMIT 1");						
			
			$co_IP = $this->db->query("SELECT *,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users LEFT JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '".$row['id']."' AND users_role.role = 'Co-Investigator' ORDER BY newsort ASC");
			//$data .= '<td>';

		//	$data .= '</td>';
			
			$data .= '<td><div class="col-lg-6">';
			$imagesCnt = 0;
			$images = $this->db->query("SELECT * FROM images WHERE group_id = '$row[id]' ORDER BY priority ASC LIMIT 1");
			while ($row1 = $this->db->fetch_array($images)) 
			{	
				$imagesCnt++;
				if ($row1['type'] == 'image')
				{
					$data .= '<img class="img-responsive" src="http://njace-cc.montclair.edu/img/raw/'.$row1['src'].'" alt="'.$row1['title'].'">';
				}
				else
				{
					$data .= '<div class="embed-responsive embed-responsive-4by3"><iframe src="'.$row1['src'].'" allowfullscreen></iframe></div>';
				}
				
			}
			$data .= '</div>';
			if ($imagesCnt) {
			$data .= '<div class="col-lg-6">';
			} else
			{
				$data .= '<div class="col-lg-12">';
			}
			
			$data .='<p><a href="/'.$row['id'].'/'.Bridge::seo($row['short_name']).'">'.$row['short_name'].'</a></p>';
			
			$data .= '<p>';
				while ($row2 = $this->db->fetch_array($QgetPeople)) 
				{
					$role=Bridge::users_role($row2['user_id'],$row['id']);
					$words = explode(" ", $role);
					$acronym = "";

					foreach ($words as $w) {
					  $acronym .= $w[0];
					}
					$data .=   $row2['fname']. ' '. $row2['lname'] . ', ';
						if(!empty($role))
						{
							$data .= '<i title="Principle Investigator">' .$acronym. '</i><br/>';
						}
				}
				while ($row3 = $this->db->fetch_array($co_IP)) 
				{
					$acronym ="Co-PI";
					
					$data .=  $row3['fname']. ' '. $row3['lname'] . ', ';
					if(!empty($role))
					{
						$data .= '<i title="Co-Investigator">' .$acronym. '</i><br/>';
					}
				}
			$data .= '</p>';
			
				// Getting contact info		
				$contact = $this->db->query("SELECT * FROM sites WHERE id=".$row['id']);
				//$QgetPeople = $this->db->query("SELECT *,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users LEFT JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '".$row['id']."' ORDER BY newsort ASC LIMIT 1");

				$q = $this->db->query("select * from sites where `id` = '".$row['id']."'");
				
				while ($row1 = $this->db->fetch_array($q)) 
				{
					$recruit = $row1['id'];
					$email=$row1['site_email'];
					$phone=$row1['site_phone'];
					
					$data .= '<p>';
					
					if(!empty($email))
					{
						if (!empty($this->mybb->user['uid']))
						{
							$data .= '<a href="'.Bridge::defaults('url').'admin/email/?recruit='.$recruit.'">' .$email.'</a>';
						}
						else
						{
							$data .= '<a href="mailto:'.$email.'">' .$email.'</a>';
						}
					}
					if(!empty($phone))
					{
						$data .= '<br/>';
						$data .=  $phone;
						$data .= '<br/>';
						$data .= '<br/>';
					}
					
					if($this->db->num_rows($contact))
					{
							
							while($add = $this->db->fetch_array($contact)) 
							{
								if(!empty($add['street']) && !empty($add['city']) && !empty($add['state']) && $add['zip']!='00000')
								{
									$data .= '<a title="Get Location" href="/'.$row['id'].'/'.Bridge::seo(Bridge::sites_info($row['id'],'short_name')).'/map">';
										$data .=  $add['street'] . '<br />';
										$data .=  $add['city'] . ', ' . $add['state'] . ' ' . $add['zip'];
									$data .= '</a>';
								}
							}
					}
					
						$data .= '</p></div>';
											
				}
			
			$data .= '</td>';

			$data .= '<td><div><p>'.$crit.'</p></div></td>';
				

			
			
			$data .= '</tr>';
			
		}
		return $data;
	}
	

}