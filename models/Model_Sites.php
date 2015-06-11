<?php
class Model_Sites extends Model
{
	public $f3,$mybb,$mybbi,$db;

	public function rlink($id,$type = '') 
	{
		if (($this->f3->get('groupOwner')))
		{
			return '&nbsp;&nbsp;&nbsp;&nbsp;<a href="/resources/7/recruitment">
			Manage '.ucfirst($type).'
			</a>
			&nbsp;&nbsp;&nbsp;&nbsp;<a href="/forum/newthread.php?fid=7">
			Add '.ucfirst($type).'
			</a>';
		}
	}
	
	public function button($uri,$text,$class)
	{
		if (!($text))
		{
			$text = 'Edit';
		}
		if (!($class))
		{
			$class = 'btn-default';
		}
		if (!($uri))
		{
			$uri = 'javascript:void(0);';
		}
		
		return '<a class="btn btn-xs '.$class.' editbutton" href="'.$uri.'">'.$text.'</a>';
	}

	
	public function links($id)
	{
		$query = $this->db->query("SELECT link, link_text FROM links WHERE link_id='$id'");
		while ($row = $this->db->fetch_array($query)) {
			$output .= '<p><a href="' . $row['link'] . '" target="_blank">' . $row['link_text'] . '</a></p>';
		}
		return $output;
	}
	
	public function members($id)
	{
		$query = $this->db->query("SELECT DISTINCT (id),avatar as phone,mybb,username,password,email,role,newsort FROM ((SELECT usergroup as mybb,uid as id,username,password,email,role,avatar,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM mybb_users LEFT JOIN users_role ON users_role.user_id=mybb_users.uid LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = mybb_users.uid WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '$id' GROUP BY users_groups.user_id ORDER BY newsort ASC
)
UNION ALL
(
SELECT mybb as mybb,users_role.user_id as id,fname,lname,email,role,phone,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users INNER JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '$id' GROUP BY users_groups.user_id ORDER BY newsort ASC
)) a
GROUP BY id
ORDER BY newsort ASC");
		while ($row = $this->db->fetch_array($query)) 
		{
			$user = Bridge::get_userfields($row['id']);
			foreach ($row as $key=>$val)
			{
				$member[$row[id]][$key] = $val;
				
				if (empty($member[$row[id]]['mybb']))
				{
					$member[$row[id]]['newusername'] = $member[$row[id]]['username']. ' ' .$member[$row[id]]['password'];
					
				}
				else
				{
					$member[$row[id]]['newusername'] = $member[$row[id]]['username'];
				}
				if (($member[$row[id]]['mybb']))
				{
					$member[$row[id]]['phone'] = $user['fid4'];
				}											
			}									
		}
		
		foreach ($member as $s => $row)
		{										
			$i++;										
			
			if (!$row['mybb'])
			{
				$output .= '<b>'.$row['newusername'] . '</b><br />';
			}
			else
			{
				$output .= '<a href="'.Bridge::defaults('url').'forum/user-'.$row['id'].'.html"><b>'.$row['newusername'].'</b></a><br />';
			}
			
			if (!empty($row['role']))
			{
				$output .= '<i>'.$row['role'] . '</i><br />';
			}
			
			if (!$row['mybb'])
			{
				$output .= '<a href="mailto:'.$row['email'].'">'.$row['email'] . '</a><br />';
			}
			else
			{
				$output .= '<a href="'.Bridge::defaults('url').'forum/member.php?action=emailuser&uid='.$row['id'].'">'.$row['email'] . '</a><br />';
			}
			if ($row['phone'])
			{
				$output .= $row['phone'] . '<br />';
			}
			$output .= '<br />';										
		}
		return $output;
	}
	
	public function tags($state)
	{
		if (!($state))
		{
			$query = $this->db->query('SELECT DISTINCT site_affiliation FROM sites WHERE enabled = 1');
			$output = array();
			$i = 0;
			while ($row = $this->db->fetch_array($query)) {
				$i++;
				$output[$i] = $row['site_affiliation'];
			}
		}
		else
		{
			$output = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AP');
		}
		
		return $output;
	}
	
	public function websites($id)
	{
		if (!($id))
		{
			$id = 2;
		}
		$result = $this->db->query("SELECT link,link_text FROM links WHERE link_id = ".$id." ORDER BY link_text ASC");
		$data = array();
		$i = 0;
		while ($row = $this->db->fetch_array($result))
		{		
			$i++;
			$data[$i]['link_text'] = $row['link_text'];
			$data[$i]['link'] = $row['link'];
		}
		return $data;
	}
	
	public function images($id = 2)
	{
			
		$images = $this->db->query("SELECT * FROM images WHERE group_id = '$id' ORDER BY priority ASC");
		while ($row = $this->db->fetch_array($images)) 
		{						
		$output .= '<div class="image-block">';
		if ($row['type'] == 'image')
				{
					$output .= '<img src="http://njace-cc.montclair.edu/img/raw/'.$row['src'].'" alt="'.$row['title'].'">';
				}
				else
				{
					$output .= '<div class="embed-responsive embed-responsive-16by9"><iframe src="'.$row['src'].'" allowfullscreen></iframe></div>';
				}
		//	<img src="http://njace-cc.montclair.edu/img/raw/'.$row['src'].'" alt="'.$row['title'].'">';
			if ($row['title'])
			{
				$output .= '<div class="carousel-caption">
				  <h4>'.$row['title'].'</h4>
				  <p>'.$row['description'].'</p>
				</div>';
			}
		  $output .= '</div>';
		}
		if (empty($output))
		{
			$output .= '<div class="image-block">
			<img src="http://njace-cc.montclair.edu/img/raw/thumbnail-default.jpg" alt="">';
			
		  $output .= '</div>';
		}
		return $output;
	}	
	
}