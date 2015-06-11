<?php
	class Model_Ticket extends Model
	{
		public $f3,$mybb,$mybbi,$db;
		
		
		/*////////////////////////////////////////////////////////////////////////
		
			VIEW AND SUBMIT TICKETS SECTION
		
		*////////////////////////////////////////////////////////////////////////
		
		public function getRoles()
		{
			$query = $this->db->query("SELECT DISTINCT role FROM users_role");
			$roles = array();
			while ($row = $this->db->fetch_array($query))
			{
				$roles[] = $row['role'];
			}
			return $roles;
		}
		
		public function submitTicket()
		{
			for ($i=0; $i < count($_POST['desc']); $i++) 
			{
				$post .= $_POST['desc'][$i] . '<br />';

				$email = $this->db->escape_string(stripcslashes($_POST['email'][$i]));
				$prior = $this->db->escape_string(stripcslashes($_POST['priority'][$i]));
				$desc  = $this->db->escape_string(stripcslashes($_POST['desc'][$i]));

				$role  = array();
				
				$n = 0;
				foreach($_POST['role'] as $r)
				{
					$n++;
					if($n % 2 == 0)
					{
						array_push($role, $r);
					}
				}
					
				// Store into database	
				$this->db->query("INSERT INTO tickets(email, description, priority, time, role) VALUES('".$email."', '".$desc."', '".$prior."', '".time()."', '".$role[$i]."')");
				
				// Get all admin emails
				$query = $this->db->query("SELECT * FROM users WHERE lname IN ('rahim','jaradeh','fails') GROUP BY lname");
				$admins = array();
				while ($row = $this->db->fetch_array($query)) 
				{
					$admins[] = $row['email'];
				}
				$subject = "NJACE-CC: New Ticket Issued.";
				$from = "NJACE-CC";
				$headers = "From: NJACE-CC\r\n";
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$message .= Model_Ticket::createHtml($email, $desc, $role);
				// Loop thru all admins and send them all an email
				
			}
			
			foreach ($admins as $address) {
					$to = $address;
					mail($to,$subject,$message,$headers);
			}
			
			$this->redirect('/home', 'Thank you for helping us make this site better. We will get back to you as soon as possible regarding the issue you are facing.');
		
		}
		
		// Sends back html for mail function
		public function createHtml($email, $desc, $role) 
		{
			$str = '
				<html>
				<head>
					<title>New Ticket Issued</title>
				<body>
					Admin:
					<br /><br />
					A new ticket has been issued on NJACE-CC by <strong>'.$email.'</strong> for <strong>'.Bridge::role_info($role, 'name').'</strong>:
					<h4>'.$desc.'</h4>
					<br />
					NJACE-CC System
				</body>
				</head>
				</html>
				';

			return $str;
		}
		
		public function getPriority()
		{
			$res = $this->db->query("SELECT id, priority FROM tickets_priority");
			while ($row = $this->db->fetch_array($res)) 
			{
				$options .= '<option value="'.$row['id'].'">'.$row['priority'].'</option>';
			}
			return $options;
		}
		
		public function getStatus()
		{
			$res = $this->db->query("SELECT id, status FROM tickets_status");
			while ($row = $this->db->fetch_array($res)) 
			{
				$options .= '<option value="'.$row['id'].'">'.$row['status'].'</option>';
			}
			return $options;
		}	
		
		public function getTickets($search, $sort, $order, $stat, $prio, $from, $to, $admin)
		{
			//$search = $_GET['q']; to search for a description -> q
			//$stat = $_GET['s']; filter based on the status -> p[]
			//$prio = $_GET['p']; filter based on the priority -> s[]
			//$sort = $_GET['sort']; sorting -> sort
			//$order = $_GET['order']; ordering -> order
			//$from = $_POST['from_date']
			//$to = $_POST['to_date']
			
			$firsttime = true;
			
			foreach ($stat as $s) {
				$status .= ''.$s.',';
			}
			$status = rtrim($status,',');
			
			foreach ($prio as $p) {
				$priority .= ''.$p.',';
			}
			$status = rtrim($status,',');
			$priority = rtrim($priority,',');
			
			if (strlen($status) < 1)
			{
				$status = '0,1,2';
			}
			if (strlen($priority) < 1)
			{
				$priority = '0,1,2,3';
			}
			
			$ticCount = 0;
			$ticUnresolved = 0;
			
			if ($order != 'asc' && $order != 'desc')
			{
				$order = 'desc';
			}
			
			if ($sort !== 'description' && $sort !== 'email' && $sort !== 'time')
			{
				$sort = 'a.status ASC, a.priority DESC, a.time';
			}
			else
			{
				$sort = 'a.status ASC, a.priority DESC, ' . $sort;
				
			}
			
			if (!empty($search))
			{
				$search = $this->db->escape_string($search);
			}
			else
			{
				$search = '';
			}
			//echo $_GET['s'];
			if (empty($admin))
			{
				$manage = 'AND email = \''.$this->mybb->user[email].'\'';
			}
			//echo $admin;
			
			$res = $this->db->query("
			SELECT *,a.id as mainid FROM tickets a INNER JOIN tickets_priority b ON a.priority=b.id INNER JOIN tickets_status c ON a.status=c.id 
			WHERE (c.id IN (".$status.") AND (description LIKE '%".$search."%' OR email LIKE '".$search."')) 
			AND (b.id IN (".$priority.") AND (description LIKE '%".$search."%' OR email LIKE '".$search."'))
			".$manage."
			ORDER BY ".$sort." ".$order."
			");
			
			while ($row = $this->db->fetch_array($res)) 
			{
				$id = $row['mainid'];
				$prior = $row['priority'];
				$role = $row['role'];
				$status = $row['status'];
				$desc = $row['description'];
				$text = Bridge::limit_words($desc,7);
				if ($desc !== $text)
				{
					$text = $text . ' ...';
				}
				if (!Bridge::users_info(null,'fname',null,$row['email']))
				{
					$by = $row['email'];
				}
				else
				{
					$by = Bridge::users_info(null,'fname',null,$row['email']) . ' ' . Bridge::users_info(null,'lname',null,$row['email']);
				}
				
				$time = $row['time'];
				
				$countTime = date('m/d/Y g:i A',$time);
				$ticCount++;
				if ($status !== 'Resolved')
				{
					$ticUnresolved++;
				}
				$output .= '
					<tr id="'.$id.'" class="'.$status. ' ' . strtolower($prior).' node main_tr">
						<td style="display:none">
							<span class="tic_id">'.$id.'</span>
							<span class="tic_prior">'.$prior.'</span>
							<span class="tic_status">'.$status.'</span>
							<span class="tic_email">'.$by.'</span>
							<span class="tic_time">'.$time.'</span>
							<span class="tic_desc">'.$desc.'</span>
						</td>
						<td class="icon">
							<span aria-hidden="true" class="glyphicon '.($status == 'Resolved' ? "glyphicon-ok" : ($status == 'In-Progress' ? "glyphicon-eye-open" : "glyphicon-info-sign")).' glyphicon-white headerIcon"></span>&nbsp;&nbsp;'.$status.''.'
						</td>
						<td width="40%">'.$text.'</td>
						
						<td >'.$by.'</td>
						<td >'.$role.'</td>
						<td class="hidden-phone" data-id="'.$id.'">'.$countTime.'</td>
					</tr>
				';
			}
			
			if (!empty($search))
			{
				$table = '<tr class="head">
							<td>&nbsp;</td>
							'.Bridge::sortlink('Description',null,'description','asc','/ticket/view',$search).'
							'.Bridge::sortlink('User',null,'email','asc','/ticket/view',$search).'
							<td>Role</td>
							'.Bridge::sortlink('Date',null,'time','asc','/ticket/view',$search).'
						</tr>
							'.$output;
			}
			else
			{
				$table = '<tr class="head">
							<td>&nbsp;</td>
							'.Bridge::sortlink('Description',null,'description','asc','/ticket/view',null).'
							'.Bridge::sortlink('User',null,'email','asc','/ticket/view',null).'
							<td>Role</td>
							'.Bridge::sortlink('Date',null,'time','asc','/ticket/view',null).'
						</tr>
							'.$output;
			}
			
			return $table;
		}
		
		public function bringTicketHistory($ticket_id)
		{
			$output.= 
			'
				<tr class="head">
					<th style="width:20%">Status</th>
					<th style="width:40%">Date Changed</th>
					<th>Changed By</th>
					<th>Priority</th>
				<tr>
			';

			$query = $this->db->query("
								SELECT FROM_UNIXTIME( ticket_log.date_changed, '%m/%d/%Y %h:%i:%s' ) AS d, tickets_priority.priority, ticket_log.ticket_discription, tickets_status.status, mybb_users.username
								FROM tickets_priority
								INNER JOIN ticket_log ON tickets_priority.id = ticket_log.priority_id
								INNER JOIN tickets_status ON ticket_log.status_id = tickets_status.id
								INNER JOIN mybb_users ON ticket_log.user_id = mybb_users.uid
								WHERE ticket_id = '$ticket_id'
								ORDER BY ticket_log.date_changed");
			$i=1;
			while($row = $this->db->fetch_array($query))
			{
				$prior = $row['priority'];
				$color = ($prior == 'Immediate' ? '3' : ($prior == 'High' ? '2' : ($prior == 'Medium' ? '1' : '0')));
				$status = $row['status'];
				$changedDate = $row['d'];
				$changedBy = $row['username'];
				$description = $row['ticket_discription'];
				
				$output .= '
					<tr class="'.$status. ' ' . strtolower($prior).' node in_tr">
						<td style="display:none">
							<span class="ticket_desc">'.$description.'</span>
						</td>
						<td class="icon">
							<span aria-hidden="true" class="glyphicon '.($status == 'Resolved' ? "glyphicon-ok" : ($status == 'In-Progress' ? "glyphicon-eye-open" : "glyphicon-info-sign")).' glyphicon-white headerIcon"></span>&nbsp;&nbsp;'.$status.''.'
						</td>
						<td>'.$changedDate.'</td>
						<td>'.$changedBy.'</td>
						<td>'.$prior.'</td>
					</tr>
					<tr style="display:none" id="adddesc_'. $i .'">
						<td  colspan="4"><p style="color:black; font-size:13px; padding: 5px; cursor:default">'.$description.'</p></td>
					</tr>
				';
				$i++;
			}
			
			return $output;
		}
		
		public function filterByDate($from, $to)
		{
			if($to == "")
			{
				$to = date('m/d/Y');
			}
			if($to > $from)
			{
				$firsttime = false;
				$res = $this->db->query("SELECT *,a.id as mainid FROM `tickets` a INNER JOIN tickets_priority b ON a.priority=b.id INNER JOIN tickets_status c ON a.status=c.id
				WHERE FROM_UNIXTIME(time, '%m/%d/%Y') >= '" . $from . "' 
				AND FROM_UNIXTIME(time, '%m/%d/%Y') <= '" . $to . "'");
			}
			while ($row = $this->db->fetch_array($res)) 
			{
				$id = $row['mainid'];
				$prior = $row['priority'];
				$role = $row['role'];
				$status = $row['status'];
				$desc = $row['description'];
				$text = Bridge::limit_words($desc,7);
				if ($desc !== $text)
				{
					$text = $text . ' ...';
				}
				if (!Bridge::users_info(null,'fname',null,$row['email']))
				{
					$by = $row['email'];
				}
				else
				{
					$by = Bridge::users_info(null,'fname',null,$row['email']) . ' ' . Bridge::users_info(null,'lname',null,$row['email']);
				}
				
				$time = $row['time'];
				
				$countTime = date('m/d/Y g:i A',$time);
				$ticCount++;
				if ($status !== 'Resolved')
				{
					$ticUnresolved++;
				}
				$output .= '
					<tr id="'.$id.'" class="'.$status. ' ' . strtolower($prior).' node main_tr">
						<td style="display:none">
							<span class="tic_id">'.$id.'</span>
							<span class="tic_prior">'.$prior.'</span>
							<span class="tic_status">'.$status.'</span>
							<span class="tic_email">'.$by.'</span>
							<span class="tic_time">'.$time.'</span>
							<span class="tic_desc">'.$desc.'</span>
						</td>
						<td class="icon">
							<span aria-hidden="true" class="glyphicon '.($status == 'Resolved' ? "glyphicon-ok" : ($status == 'In-Progress' ? "glyphicon-eye-open" : "glyphicon-info-sign")).' glyphicon-white headerIcon"></span>&nbsp;&nbsp;'.$status.''.'
						</td>
						<td width="40%">'.$text.'</td>
						
						<td >'.$by.'</td>
						<td >'.$role.'</td>
						<td class="hidden-phone" data-id="'.$id.'">'.$countTime.'</td>
					</tr>
				';
			}
			
			$table = '<tr class="head">
							<td>&nbsp;</td>
							'.Bridge::sortlink('Description',null,'description','asc','/ticket/view',null).'
							'.Bridge::sortlink('User',null,'email','asc','/ticket/view',null).'
							<td>Role</td>
							'.Bridge::sortlink('Date',null,'time','asc','/ticket/view',null).'
						</tr>
							'.$output;
		}
		
		public function updatePost($bug_ID, $bug_Desc, $bug_Status, $bug_Prior)
		{
			$bug_Desc = $this->db->escape_string($bug_Desc);
			$user_id = $this->mybb->user['uid'];
			$changedDate = strtotime(date('m/d/Y G:i:s'));
			//echo"UPDATE tickets SET description= '$bug_Desc', priority= '$bug_Prior', status= '$bug_Status' WHERE id= '$bug_ID'";
			$this->db->query("UPDATE tickets SET description='$bug_Desc', priority='$bug_Prior', status='$bug_Status' WHERE id= '$bug_ID'") or die($db->error());
			$this->db->query("INSERT INTO activity (event, madeBy, time) VALUES('Ticket Status Changed', 'Admin [".$this->mybb->user['username']."]', now())") or die($db->error());
			$this->db->query("INSERT INTO `ticket_log` (`ticket_id`, `user_id`, `date_changed`, `priority_id`, `status_id`, `ticket_discription`) VALUES ('$bug_ID', '$user_id', '$changedDate', '$bug_Prior', '$bug_Status', '$bug_Desc')");
		}
		
		/*////////////////////////////////////////////////////////////////////////
		
			SUBMIT LOG HOURS AND LOG HOURS REPORT SECTION
		
		*////////////////////////////////////////////////////////////////////////
		
		public function getResearchSites()
		{
			$output="";
			$query = $this->db->query("SELECT id, short_name FROM sites order by short_name ASC");
			
			while ($row = $this->db->fetch_array($query))
			{
				$investigators = $this->db->query("SELECT * , CASE WHEN (role_sort IS NULL OR role_sort =  '')THEN  '999' ELSE role_sort END AS newsort FROM users LEFT JOIN users_role ON users_role.user_id = users.id LEFT JOIN role ON users_role.role = role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id =  '" . $row['id'] . "' AND users_role.role = 'Principal Investigator' ORDER BY newsort ASC ");
				//After migrating all the users to mybb then we have to use the following query
				//SELECT * , CASE WHEN (role_sort IS NULL  OR role_sort =  '' ) THEN  '999' ELSE role_sort END AS newsort FROM mybb_users LEFT JOIN users_role ON users_role.user_id = mybb_users.uid LEFT JOIN role ON users_role.role = role.name INNER JOIN users_groups ON users_groups.user_id = mybb_users.uid WHERE users_groups.group_id = users_role.group_id AND users_role.group_id =  'RESEARCH SITE ID' AND users_role.role =  'Principal Investigator' ORDER BY newsort ASC 
				$item = $row['short_name'];
				$item .= ' (';
				while ($row2 = $this->db->fetch_array($investigators))
				{
					//getting the investigators
					$item.= $row2['fname'] . ' ' . $row2['lname'] .'; ';
				}
				$item = trim($item,'; ');
				$item .= ')';
				$output.= '<option name="'.$row['id'].'" value="'.$row['id'].'">'.$item.'</option>';
			}
			return $output;
		}
		
		public function getAllTicket()
		{
			$query = $this->db->query("SELECT ticket_id FROM `time_log`");
			$tickets = array();
			while($row = $this->db->fetch_array($query))
			{
				$tickets[]=$row['ticket_id'];
			}
			return $tickets;
		}
		
		public function getTicketByID($id)
		{
			$query = $this->db->query("SELECT * from time_log where ticket_id = '" .$id. "'");
			while($row = $this->db->fetch_array($query))
			{
				return $row;
			}
			
		}
		
		public function getResearchSitesByTicketID($id)
		{
			//bringing all the research sites that are related to a specific log hour
			$query = $this->db->query("SELECT site_id from time_log_sites where log_id = '" .$id. "'");
			$checkedSites = array();
			while ($row = $this->db->fetch_array($query))
			{
				$checkedSites[] = $row['site_id'];
			}
			
			$output="";
			$query = $this->db->query("SELECT id, short_name FROM sites order by short_name ASC");
			while ($row = $this->db->fetch_array($query))
			{
				$investigators = $this->db->query("SELECT * , CASE WHEN (role_sort IS NULL OR role_sort =  '')THEN  '999' ELSE role_sort END AS newsort FROM users LEFT JOIN users_role ON users_role.user_id = users.id LEFT JOIN role ON users_role.role = role.name INNER JOIN users_groups ON users_groups.user_id = users.id WHERE users_groups.group_id = users_role.group_id AND users_role.group_id =  '" . $row['id'] . "' AND users_role.role = 'Principal Investigator' ORDER BY newsort ASC ");
				//After migrating all the users to mybb then we have to use the following query
				//SELECT * , CASE WHEN (role_sort IS NULL  OR role_sort =  '' ) THEN  '999' ELSE role_sort END AS newsort FROM mybb_users LEFT JOIN users_role ON users_role.user_id = mybb_users.uid LEFT JOIN role ON users_role.role = role.name INNER JOIN users_groups ON users_groups.user_id = mybb_users.uid WHERE users_groups.group_id = users_role.group_id AND users_role.group_id =  'RESEARCH SITE ID' AND users_role.role =  'Principal Investigator' ORDER BY newsort ASC 
				$item = $row['short_name'];
				$item .= ' (';
				while ($row2 = $this->db->fetch_array($investigators))
				{
					//getting the investigators
					$item.= $row2['fname'] . ' ' . $row2['lname'] .'; ';
				}
				$item = trim($item,'; ');
				$item .= ')';
				if(in_array($row['id'], $checkedSites))
					$output.= '<option name="'.$row['id'].'" value="'.$row['id'].'" selected="selected">'.$item.'</option>';
				else
					$output.= '<option name="'.$row['id'].'" value="'.$row['id'].'">'.$item.'</option>';
			}
			
			return $output;
		}
		
		public function storeTimeTrack($startdate, $starttime, $enddate, $endtime, $desc, $sites)
		{
			$startdate = $this->db->escape_string($startdate);
			$starttime = $this->db->escape_string($starttime);
			$enddate = $this->db->escape_string($enddate);
			$endtime = $this->db->escape_string($endtime);
			$desc = $this->db->escape_string($desc);
			
			$startAt = strtotime("$startdate $starttime");
			$endAt = strtotime("$enddate $endtime");
			
			$this->db->query("INSERT INTO time_log (ticket_user, ticket_description, starttime, endtime, time) VALUES ('". $this->mybb->user['uid'] ."', '". $desc . "', '". $startAt . "', '". $endAt ."', now())");
			$id = $this->db->insert_id();
			for ($j=0; $j<count($sites);$j++){
				$this->db->query("INSERT INTO time_log_sites (log_id, site_id) VALUES ('".$id."', '" .$sites[$j]."')");
			}
		}
		
		public function updateTimeTrack($id, $startdate, $starttime, $enddate, $endtime, $sites, $desc)
		{
			//delete the old sites
			$this->db->query("DELETE FROM time_log_sites WHERE log_id = '" . $id ."'");
			
			$startdate = $this->db->escape_string($startdate);
			$starttime = $this->db->escape_string($starttime);
			$enddate = $this->db->escape_string($enddate);
			$endtime = $this->db->escape_string($endtime);
			
			$startAt = strtotime("$startdate $starttime");
			$endAt = strtotime("$enddate $endtime");
			
			$desc = $this->db->escape_string($desc);
			
			$this->db->query("UPDATE time_log SET ticket_description = '" . $desc . "', starttime = '" . $startAt . "', endtime = '" . $endAt . "' WHERE ticket_id = " . $id);
			for ($j=0; $j<count($sites);$j++){
				$this->db->query("INSERT INTO time_log_sites (log_id, site_id) VALUES ('".$id."', '" .$sites[$j]."')");
			}
			
			$this->redirect('/log-hours/report', 'Your changes has been successfully saved...');
			
		}
		
		public function deleteTimeTrack($id)
		{
			//delete the old sites
			$this->db->query("DELETE FROM time_log_sites WHERE log_id = '" . $id ."'");
			//then delete the log hour report
			$this->db->query("DELETE FROM time_log WHERE ticket_id = '" . $id . "'");
			
			$this->redirect('/log-hours/report', 'Your hour has been deleted successfully...');
		}		
		
		public function filterLogHourByDate($from, $to, $action)
		{	
			$output.= 
			'
				<tr class="head">
					<th style="width:50%">Description</th>
					<th>Started</th>
					<th>Finished</th>
					<th>Hours</th>
					<th></th>
				<tr>
			';
			if($action == 1)
			{
				//echo("SELECT * FROM `time_log` WHERE ticket_user = '".$this->mybb->user[uid]."' AND starttime >= '" . $from . "' AND endtime <= '" .$to. "'");
				//exit();
				$query = $this->db->query("SELECT * FROM `time_log` WHERE ticket_user = '".$this->mybb->user[uid]."' AND starttime >= '" . $from . "' AND endtime <= '" .$to. "'");
			}
			else
			{
				$startMonth = strtotime(date('m/01/Y'));
				$query = $this->db->query("SELECT * FROM `time_log` WHERE ticket_user = '".$this->mybb->user[uid]."' AND starttime >= '" . $startMonth . "'");
			}
			$counter = 0;
			while ($row = $this->db->fetch_array($query)) 
			{
				$counter++;
				$userName = Bridge::users_info($row['ticket_user'],'fname') . ' ' . Bridge::users_info($row['ticket_user'],'lname');
				
				// If the user is an admin, change the placeholder & icon, otherwise set as default
		
				$difference = abs((($row['starttime'] - $row['endtime'])/60)/60);
				// Display vars inside html data attr tags so jquery can make use of them
				$output.= '
					<tr>
						<td class="nodes"> '.$row['ticket_description'].'</td>
						<td class="nodes"> '.date('m/d/Y g:i A',$row['starttime']).'</td>
						<td class="nodes"> '.date('m/d/Y g:i A',$row['endtime']).'</td>
						<td class="nodes"> '.$difference.'</td>
						<td><a style="color:#0088cc;text-transform:uppercase;font-weight:bold;font-size:10px;" href="/log-hours/edit/'.$row['ticket_id'].'">Edit</a></td>
						
					</tr>	
				';
				$diffSum += $difference;
			}
			
			if($counter > 1)
			{
				$output.= '
						<tr class="'.$color.' groups">			
							<td colspan="3" class="nodes"><b>Total Hours Worked:</b> </td>
							<td class="nodes"> <b>'.$diffSum.'</b></td>
							<td class="nodes"> &nbsp;</td>
						</tr>
					';
			}
			
			
			return $output;
		}
	}
?>