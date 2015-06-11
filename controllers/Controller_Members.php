<?php
class Controller_Members extends Controller
{
	public $f3,$mybb,$mybbi,$db;
	
	public function main($output = '')
	{
		$this->f3->set('siteID',$output);
		$this->f3->set('membcount',Model_Members::manage($output,$_GET['sort'],$_GET['order'],$_GET['letter'],$_GET['q'],1));
		$this->f3->set('data',Model_Members::manage($output,$_GET['sort'],$_GET['order'],$_GET['letter'],$_GET['q']));
		$this->f3->set('members-add','views/members/add.html');
		$this->f3->set('members-create','views/members/create.html');
		$this->f3->set('members-edit','views/members/edit.html');
		$this->f3->set('js','loadManageMembers(
			'.$output.',
			'.json_encode(Model_Members::addlist($output)).',
			'.json_encode(Bridge::sites_info($output,'short_name')).',
			'.json_encode(Model_Members::roles()).'
		);');
		$this->f3->set('main','views/members/main.html');
	}
	
	public function add($id)
	{
		if ($id)
		{
			$this->db->query("INSERT INTO users_role(user_id,group_id,role) VALUES('".$_POST['userid']."', '".$id."', 'Research Associate')");
			$this->db->query("INSERT INTO users_groups VALUES ('',".$_POST['userid'].",".$id.",'')");
		}
	}
	
	public function edit($id)
	{
		if ($id)
		{
			foreach ($_POST['role'] as $val)
			{
				$role = $this->db->escape_string($val);
			}
			
			if ($_POST['remove'])
			{
				$query = $this->db->query("DELETE FROM users_groups WHERE user_id = ".$_POST['userid']." AND group_id=".$_POST['groupid']);
			}
			else
			if ($_POST['edit'])
			{
				$manager = $_POST['manage'];
				if (!($manager))
				{
					$manager = 0;
				}
				else
				{
					$manager = 1;
				}
				
				$this->db->query("UPDATE users_groups SET level = '".$manager."' WHERE user_id = ".$_POST['userid']." AND group_id=".$_POST['groupid']);
				//$this->db->query("UPDATE mybb_userfields SET fid4 = '".$_POST['phone']."' WHERE ufid = ".$_POST['userid']);
				
				
				$this->db->query("DELETE FROM `users_role` WHERE user_id = ".$_POST['userid']." AND group_id=".$_POST['groupid']);	
				$this->db->query("INSERT INTO users_role (user_id,group_id,role) VALUES ('".$_POST['userid']."', '".$_POST['groupid']."', '".$role."')");
			}
			$this->f3->reroute('/'.$_POST['groupid'].'/members');
		}
	}
	
	public function info($id)
	{
		if ($_POST['userid'])
		{
			$query = $this->db->query("SELECT DISTINCT (id),avatar as phone,mybb,username,password,email,role,newsort FROM ((SELECT usergroup as mybb,uid as id,username,password,email,role,avatar,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM mybb_users LEFT JOIN users_role ON users_role.user_id=mybb_users.uid LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = mybb_users.uid WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '$id' GROUP BY users_groups.user_id ORDER BY newsort ASC
)
UNION ALL
(
SELECT mybb as mybb,users_role.user_id as id,fname,lname,email,role,phone,CASE WHEN (role_sort IS NULL OR role_sort='') THEN '999' ELSE role_sort END as newsort FROM users INNER JOIN users_role ON users_role.user_id=users.id LEFT JOIN role ON users_role.role=role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id = '$id' GROUP BY users_groups.user_id ORDER BY newsort ASC
)) a
WHERE id = '$_POST[userid]'
GROUP BY id
ORDER BY newsort ASC");
			
			while ($row = $this->db->fetch_array($query)) 
			{
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
				if ($row['mybb'])
				{
					$row['phone'] = $user['fid4'];
				}
				
				$row['level'] = $levelid;
				$row['glevel'] = Bridge::users_groups($row['id'],$id);
				echo json_encode($row);
			}
		}
	}
	
		
	public function create($id)
	{
		if ($id)
		{		
			$role = array();
			foreach ($_POST as $key => $val)
			{
				if ($key == 'role')
				{			
					foreach ($val as $x)
					{
						$role[] = $x;
					}					
				}
				else 
				{				
					$$key = $this->db->escape_string($val);
				}
			}
			foreach ($role as $x) 
			{
				$roletitle .= $x;
			}
			$data = array (
				"username"  => $fname . ' ' . $lname,
				"email" => $email,
				"email2" => $email,
			);	
			$create = $this->mybbi->register($data);
			if (is_array($create))
			{
				echo $create[0];
			}
			else
			{
				$query = $this->db->query("SELECT uid FROM mybb_users WHERE email = '$email' LIMIT 1");
				while ($row = $this->db->fetch_array($query))
				{
					$newid = $row['uid'];
				}
				$this->db->query("INSERT INTO users_role(user_id,group_id,role) VALUES('".$newid."', '".$id."', '".$roletitle."')");
				$this->db->query("INSERT INTO users_groups(user_id,group_id) VALUES('".$newid."', '".$id."')");
				$this->db->query("UPDATE mybb_userfields SET fid4 = '$phone', fid5 = '$fname', fid6= '$lname' WHERE ufid = '$newid'");
			}
		}
	}
	
	public function body($id)
	{	
		parent::index();
		
		$this->f3->set('shortname',Bridge::sites_info($id,'short_name'));
		$this->config('page_name','Manage Members | '.$this->f3->get('shortname'));
		if (!($this->f3->get('shortname')))
		{
			$this->f3->error(404);
		}
		$this->main($id);
		echo View::instance()->render('views/layout.html');
	}
	
}
?>