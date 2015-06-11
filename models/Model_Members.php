<?php
class Model_Members extends Model
{
	public $f3,$mybb,$mybbi,$db;

	public function loadUserOutput($row,$siteid)
	{
		$id = $row['id'];
		$phone = $row['phone'];
		
		if ($row['mybb'])
		{
			$name = $row['username'];
		}
		else
		{
			$name = $row['username'] . ' ' . $row['password'];
		}
		$email = $row['email'];
		$role = $row['role'];
		
		$joindate = $row['joindate'];
		if ($joindate)
		{
			$joindate = date('M d, Y',$row['joindate']);
		}
		else
		{
			if ($row['regdate'])
			{
				$joindate = date('M d, Y',$row['regdate']);
			}
			else
			{								
				$joindate = 'N/A';
			}
		}
		
		if ($row['id'])
		{										
			$u = user_permissions($row['id']);
			$user = Bridge::get_userfields($row['id']);
			if ($u['cancp'])
			{
				$levelid = 2;
			}
		}
		else
		{
			$levelid = Bridge::users_info($row['id'],'level');
			
		}

		$level_name = Bridge::level_info($levelid,'name');
		$level_hopen = Bridge::level_info($levelid,'html_open');
		$level_hclose = Bridge::level_info($levelid,'html_close');
		$level = $level_hopen . $level_name . $level_hclose;
		
		
		$gLevel = Bridge::users_groups($row['id'],$siteid);
		
		if ($gLevel)
		{
			$level .= ' <i>(Site Manager)</i>';
			$type = "Site Manager";
			$icon = "glyphicon glyphicon-star-empty";
		}
		else
		{
			$type = "Regular User";
			$icon = "glyphicon glyphicon-user";
		}
		if (!empty($email))
		{
			
			if (!($row['mybb']))
			{
				$output .= '<tr id="'.$row['id'].'" title="This member has never logged in." data-toggle="tooltip" class="warning">';
			}
			else
			{
				$phone = $user['fid4'];
				$output .= '<tr id="'.$row['id'].'">';
			}

			$output .= '
					<td><span title="'.$type.'" data-toggle="tooltip"><i class="'.$icon.'"></i></span> '.$name.'</td>
					<td><i class="icon-envelope"></i> '.$email.'</td>
					<td class="hidden-phone"><i class="icon-bullhorn"></i> '.$phone.'</td>
					<td>'.$role.'</td>
					<td>'.$level.'</td>
				</tr>
			';
		}
		return $output;
	}

	public function roles()
	{
		$query = $this->db->query("SELECT DISTINCT role FROM users_role");
		$data = array();
		$i = 0;
		while ($row = $this->db->fetch_array($query))
		{
			$i++;
			$data[$i]['role'] = $row['role'];
		}
		return $data;
	}
	
	
	public function addlist($siteID)
	{		
		$query = $this->db->query("SELECT * FROM mybb_users ORDER BY username ASC");
		$data = array();
		$i = 0;
		while ($row = $this->db->fetch_array($query)) 
		{
			//$userName = users_info($row['id'],'fname') . ' ' . users_info($row['id'],'lname');
			$username = $row['username'];
			$groups = Bridge::users_groups($row['uid']);
			$groups = explode(',',$groups);
			
			if (!empty($row['email'])) 
			{
				if ( !(in_array($siteID,$groups)) )
				{
					//echo '<option value="'.$row['uid'].'">'.$username.' - '.$row['email'].'</option>';
					$i++;
					$data[$i]['uid'] = $row['uid'];
					$data[$i]['username'] = $username;
					$data[$i]['email'] = $row['email'];
					
				}					
			}			
		}
		return $data;		
	}
	
	public function manage($id,$sort,$order,$letter,$search,$num = 0)
	{
		
		if ($order != 'asc' && $order != 'desc')
		{
			$order = 'asc';
		}
		
		if ($sort !== 'fname' && $sort !== 'email' && $sort !== 'joindate')
		{
			$sort = 'fname';
		}
		
		
		if ( (strlen($letter) > 1) || (!ctype_alpha($letter)) )
		{
			$letter = 0;
		}

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
			$numUsers++;
			$output .= self::loadUserOutput($row,$id);
		}
		
		
		
		if ($sort == 'fname')
		{
			$fname = '<b>Member Name</b>';
		}
			

		$data .= '<tr class="head">
		
	'.Bridge::sortlink('Member Name',$letter,'fname','asc','members').'
	'.Bridge::sortlink('Email',$letter,'email','asc','members').'
	<td>Phone</td>
	<td>Role</td>
	<td>Level</td>
	</tr>'.$output;
		if ($num)
		{			
			return $numUsers;
		}
		else
		{
			return $data;
		}
	}	
	
}