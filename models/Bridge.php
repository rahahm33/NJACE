<?php
/*****************************************************

	Functions in the Bridge class are functions that must be shared between MyBB and the Fat Free Framework. 
	Think of them as globally defined functions accessible from anywhere throughout the website.
	
	DO NOT ADD OR MODIFY ANY FUNCTIONS IN THE BRIDGE CLASS UNLESS ABSOLUTELY NECESSARY. 
	
******************************************************/

class Bridge
{
	public function loadView($file,$vars){
		ob_start();
		foreach ($vars as $key=>$val)
		{
			$$key = $val;
		}
		require($file);
		return ob_get_clean();
	}

	public function menu($type,$level = 0)
	{	
		global $mybb,$db;
		
		// Generate User Menu
		if ($type == 'user')
		{
			$output .= "<li><a href=\"/ticket/submit\"><i class=\"glyphicon glyphicon-bullhorn\"></i> Submit Ticket</a></li>
					<li><a href=\"/ticket/view\"><i class=\"shortcut-icon glyphicon glyphicon-fire\"></i> View Tickets</a></li>
					<li class=\"divider\"></li>
					<li><a href=\"/log-hours\"><i class=\"glyphicon glyphicon-time\"></i> Log Hours</a></li>
					<li><a href=\"/log-hours/report\"><i class=\"glyphicon glyphicon-list-alt\"></i> Logged Hours Report</a></li>
					
					<li class=\"divider\"></li>";
		
			if ($level)
			{				
				$output .= "<li><a href=\"/announcements\"><i class=\"glyphicon glyphicon-th\"></i> Manage Announcements</a></li>
				<li><a href=\"/forum/memberlist.php\"><i class=\"glyphicon glyphicon-user\"></i> Manage Users</a></li>
				<li class=\"hidden\"><a href=\"/activity\"><i class=\"shortcut-icon glyphicon glyphicon-th-list\"></i> Activity Log</a></li>
				<li><a href=\"/sites/manage\"><i class=\"shortcut-icon glyphicon glyphicon-info-sign\"></i> Manage Sites</a></li>
				<li><a href=\"/ticket/manage\"><i class=\"shortcut-icon glyphicon glyphicon-fire\"></i> Manage Tickets <span class=\"badge\">" . Bridge::nb_unresolved_tickets() . "</span></a></li>
				<li class=\"hidden\"><a href=\"/email\"><i class=\"shortcut-icon glyphicon glyphicon-envelope\"></i> Send an Email</a></li>
				<li class=\"divider\"></li>";
				$output .= "<li><a href=\"".$mybb->settings['bburl']."/admin/\"><i class=\"glyphicon glyphicon-user\"></i> Admin Panel</a></li>";
				$output .= "<li><a href=\"".$mybb->settings['bburl']."/modcp.php\"><i class=\"glyphicon glyphicon-user\"></i> Mod Panel</a></li><li class=\"divider\"></li>";
			}
				
			$output .= "<li><a href=\"".$mybb->settings['bburl']."/member.php?action=profile&amp;uid=".$mybb->user['uid']."\"><i class=\"glyphicon glyphicon-user\"></i> Profile</a></li>";
			
			if ($mybb->settings['enablepms'])
			{
				$output .= "<li><a href=\"".$mybb->settings['bburl']."/private.php\"><i class=\"shortcut-icon glyphicon glyphicon-fire\"></i> Private Messages</li>";
			}			
			$output .= "<li><a class=\"log-in\" href=\"".$mybb->settings['bburl']."/member.php?action=logout&amp;logoutkey=".$mybb->user['logoutkey']."\"><i class=\"glyphicon glyphicon-off\"></i> Logout</a></li>";		
		}
		else
		// Generate Resources Menu
		if ($type == 'resources')
		{
			$output .= "<li><a href=\"/resources\">Resources</a></li>		 
				 <li><a href=\"/announcements\" tabindex=\"-1\">Announcements</a></li>			  
				  <li class=\"divider\"></li>
				  <li><a href=\"/resources/27\" tabindex=\"-1\">For Families</a></li>
				  <li><a href=\"/resources/14\" tabindex=\"-1\">For Providers</a></li>
				  <li><a href=\"/resources/22\" tabindex=\"-1\">For Researchers</a></li>";
		}
		else
		// Generate Research Sites Menu
		if ($type == 'sites')
		{
			
			$res = $db->query("SELECT DISTINCT site_affiliation FROM sites WHERE enabled = 1 ORDER BY site_affiliation ASC");
			$z = 0;
			$SIarr = array();
					$groups = Bridge::users_groups($mybb->user['uid']);	
				//	print_r($groups);
					$groups = explode(',',$groups);
					
			while ($row = $db->fetch_array($res)) 
			{
				if($row['site_affiliation'] != "test")
				{
					// Check if $z is not equal to 0
					$SIarr[$z] = $row['site_affiliation'];  // Store value
					// Display title
					$output .=  '<li>
					<a style="color:#333;font-weight:bold;" id="" class="collapselink" data-toggle="collapse" data-parent="#accordion1" href="#link'.$z.'">
					'.$SIarr[$z].'
					</a></li><ul id="link'.$z.'" class="collapse">';
					
					$query = sprintf("SELECT * FROM sites WHERE enabled = 1 && site_affiliation = \"%s\" ORDER BY short_name ASC", $db->escape_string($SIarr[$z]));

					$res2 = $db->query($query);
					
					while ($row2 = $db->fetch_array($res2)) {
					
						
						$gLevel = Bridge::users_groups($mybb->user['uid'],$row2['id']);
						//echo $gLevel;
						if ( ((in_array($row2['id'],$groups)) && (!empty($gLevel))) || ($level) )
						{
							$groupOwner = 1;
							$manageButton = '<i class="glyphicon glyphicon-user" style="margin-right:10px"></i>';
							//$manageButton = '<div class="pull-right" style="border-radius:20px;width:1px;height:1px;display:inline-block;position:relative;top:5px;padding:3px;margin-right:5px;text-transform:uppercase;font-weight:bold;background:#AB4D4D;color:#FFF"></div>';
						}
						else
						{
							$manageButton = '';
							$groupOwner = 0;
						}
						
						
						
						
						// If the shortname is specified append that, otherwise append the name
						if ($row2['short_name'] == '')
						{
							$name =  $row2['site_name'];
						}
						else
						{
							$name =  $row2['short_name'];
						}
						
				
						// Append the href with the correct get id
						$output .=  '<li><a href="/'.$row2['id'].'/'.Bridge::seo($name).'" tabindex="-1">'.$manageButton.''.$name.'</a></li>';
						
					}
					$output .= '</ul>';
					$z++;
				}
			}
		}
		return $output;	
	}
	public function defaults($var,$value = null)
	{
		global $mybb;
		$config['url'] = $mybb->settings['homeurl'];
		$config['title'] = $mybb->settings['homename'];
		$config['short_title'] = 'NJACE';
		$config['page_name'] = 'Home';
		
		if ( ($mybb->usergroup['cancp']) || ($mybb->usergroup['canmodcp']) )
		{
			$config['admin'] = 1;
		}
		else
		{
			$config['admin'] = 0;
		}
		
		if ($mybb->user['uid'])
		{
			$config['member'] = 1;
			$config['mybb'] = $mybb->user;
			if ($config['admin'] == 1)
			{
				$config['usermenu'] = Bridge::menu('user',1);
			}
			else
			{
				$config['usermenu'] = Bridge::menu('user');
			}
		}
		else
		{
			$config['member'] = 0;
		}
		
		$config['resourcesmenu'] = Bridge::menu('resources');
		$config['sitesmenu'] = Bridge::menu('sites');
		
		if ($value)
		{
			$config[$var] = $value;
		}
		
				
		return $config[$var];
	}
	public function get_userfields($uid)
	{
		global $db;
		$query = $db->query("SELECT * FROM mybb_userfields WHERE ufid = '$uid'");
		while ($row = $db->fetch_array($query))
		{
			return $row;
		}
	}
	
	public function seo($str, $replace=array(), $delimiter='-') 
	{
		$str = ltrim(rtrim(trim($str)));
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		$clean = ltrim(rtrim(trim($clean)));
		return $clean;
	}
	
	public function sendmail($to,$from = 'no-reply@njace-cc.montclair.edu',$subject,$message)
	{

		$headers = 'From: ' . $from. "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		if (mail($to, $subject, $message, $headers))
		{
			return true;
		}
		else
		{
			return 'Error';
		}
	}
	
	public function nb_unresolved_tickets()
	{
		global $db;
		// Getting number of issue tickets
		$res = $db->query("SELECT COUNT(*) as tickets FROM tickets WHERE NOT (status = 2)");
		while ($row = $db->fetch_array($res))
		{
			return($row['tickets']);
		}
	}
	
	public function nb_all_tickets()
	{
		global $db;
		// Getting number of issue tickets
		$res = $db->query("SELECT COUNT(*) as all_tickets FROM tickets");
		while ($row = $db->fetch_array($res))
		{
			return($row['all_tickets']);
		}
	}
	
	public function print_r2($val){
		echo '<pre>';
		print_r($val);
		echo '</pre>';
	}
	
	public function forum_info($fid,$info = '')
	{
		global $db;
		if ($info == 'threads')
		{
			return intval(end($db->fetch_array($db->query("SELECT COUNT(*) FROM mybb_threads WHERE fid = '".$fid."'"))));
		}
		else
		if ($info == 'hits')
		{
			return intval(end($db->fetch_array($db->query("SELECT SUM(views) as views FROM mybb_threads WHERE fid = '".$fid."'"))));
		}
		else
		{
			$query = $db->query("SELECT * FROM mybb_forums WHERE fid = '".$fid."'");
			while ($row = $db->fetch_array($query))
			{
				return $row[$info];
			}
		}
	}
	public function truncate($string,$length=100,$append="&hellip;") 
	{
		  $truncated_str = "";
			$useAppendStr = (strlen($string) > intval($length))? true:false;
			$truncated_str = substr($string,0,$length);
			$truncated_str .= $append;
			return $truncated_str;
	}
	
	public function isAdmin()
	{
		if ( ($_SESSION['id'] == 75) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function fdate($time)
	{
		global $mybb;
		return my_date($mybb->settings['dateformat'] .' '. $mybb->settings['timeformat'],$time);
	}
	
	public function redirect_url($url,$error = null)
	{
		if(headers_sent())
		{
			$_SESSION['error'] = $error;
			echo '<script>window.location.href="'.$url.'";</script>';
			exit;
		}
		else
		{
			$_SESSION['error'] = $error;
			header('Location: '.$url);
			exit;
		}
	}

	
	public function uploadAnn ($url,$type)
	{
	
		if ($type == 0)
		{
			$validext = array("pdf");
			$dirtype = 'pdfs';
			$filename = 'file';
		}
		else
		{
			$validext = array("gif", "jpeg", "jpg", "png");
			$dirtype = 'imgs';
			$filename = 'image';
		}
		
		$rand = substr(uniqid('', true), -5);
		$file = $_FILES[$filename]["name"];
		
		$temp = explode(".", $file);
		$file = $temp[0] . $rand;
		$extension = end($temp);
		$extension = strtolower($extension);
		$file = $file . '.' . $extension;
		

		if (($_FILES[$filename]["size"] < 10000000) && in_array($extension, $validext))
		{
			if ($_FILES[$filename]["error"] > 0)
			{
				echo "Return Code: " . $_FILES[$filename]["error"] . "<br>";
			}
			else
			{
				$url .= $file;
				move_uploaded_file($_FILES[$filename]["tmp_name"], $url);
				//echo $url;
				//exit;
				return 'files/'.$dirtype.'/'.$file;
			}
		}
	}
	
	public function file_upload($file_id, $folder = "../../../files/pdfs/", $types = "")
	{
		if (!$_FILES[$file_id]['name'])
			return array(
				'',
				'No file specified'
			);
		
		$file_title = $_FILES[$file_id]['name'];
		//Get file extension
		$ext_arr    = split("\.", basename($file_title));
		$ext        = strtolower($ext_arr[count($ext_arr) - 1]); //Get the last extension
		
		//Not really uniqe - but for all practical reasons, it is
		$uniqer    = substr(md5(uniqid(rand(), 1)), 0, 5);
		$file_name = $uniqer . '_' . $file_title; //Get Unique Name
		
		$all_types = explode(",", strtolower($types));
		if ($types)
		{
			if (in_array($ext, $all_types));
			else
			{
				$result = "'" . $_FILES[$file_id]['name'] . "' is not a valid file."; //Show error if any.
				return array(
					'',
					$result
				);
			}
		}
		
		//Where the file must be uploaded to
		if ($folder)
			$folder .= '/'; //Add a '/' at the end of the folder
		$uploadfile = $folder . $file_name;
		
		$result = '';
		//Move the file from the stored location to the new location
		if (!move_uploaded_file($_FILES[$file_id]['tmp_name'], $uploadfile))
		{
			$result = "Cannot upload the file '" . $_FILES[$file_id]['name'] . "'"; //Show error if any.
			if (!file_exists($folder))
			{
				$result .= " : Folder don't exist.";
			}
			elseif (!is_writable($folder))
			{
				$result .= " : Folder not writable.";
			}
			elseif (!is_writable($uploadfile))
			{
				$result .= " : File not writable.";
			}
			$file_name = '';
			
		}
		else
		{
			if (!$_FILES[$file_id]['size']) //Check if the file is made
			{
				@unlink($uploadfile); //Delete the Empty file
				$file_name = '';
				$result    = "Empty file found - please use a valid file."; //Show the error message
			}
			else
			{
				chmod($uploadfile, 0777); //Make it universally writable.
			}
		}
		
		return array(
			$file_name,
			$result
		);
	}

	public function sortlink($name,$letter,$sort,$order,$page,$search = null,$id = null)
	{
		$output .= '<td><a href="'.$page.'?'.Bridge::sortquery($letter,$sort,$order,$search,$id).'">';
		$asc = '▲';
		$desc = '▼';
		if ($_GET['sort'] == $sort)
		{
			$output .= '<b>'.$name.'</b>';
			if ($_GET['order'] == 'asc')
			{
				$output .= '<span class="order" style="font-size:10px;margin-left:5px;">'.$desc.'</span>';
			}
			else
			{
				$output .= '<span class="order" style="font-size:10px;margin-left:5px;">'.$asc.'</span>';
			}
		}
		else
		{
			$output .= $name;
			$output .= '<span class="order" style="font-size:10px;margin-left:5px;">'.$asc.'</span>';
		}
		
		
		

		
		$output .= '</a></td>';
		
		return $output;
	}
	
	public function sortquery($letter,$sort,$order,$search = null,$id = null)
	{
		if (!empty($_GET['letter']))
		{
			$sortlink .= '&letter='.$_GET['letter'];
		}
		else
		{
			if (!empty($letter))
			{
				$sortlink .= '&letter='.$letter;
			}
		}
		
		if (!empty($_GET['q']))
		{
			$sortlink .= '&q='.$_GET['q'];
		}
		else
		{
			if (!empty($search))
			{
				$sortlink .= '&q='.$search;
			}
		}
		
		if (!empty($_GET['id']))
		{
			$sortlink .= '&id='.$_GET['id'];
		}
		else
		{
			if (!empty($id))
			{
				$sortlink .= '&id='.$id;
			}
		}
		
		
		if ($_GET['sort'] == $sort)
		{
			$sortlink .= '&sort='.$sort;
			if ($_GET['order'] == 'asc')
			{
				$sortlink .= '&order=desc';
			}
			else
			{
				$sortlink .= '&order=asc';
			}
		}
		else
		{
			$sortlink .= '&sort='.$sort;
			$sortlink .= '&order='.$order;
		}		
		
		$sortlink = ltrim($sortlink,'&');
		return $sortlink;
	}
	
	public function announcements_info($id,$info)
	{
		global $db;
		if ($info == 'type')
		{
			$query = $db->query("SELECT name FROM announcements_type WHERE type_id = '$id'");
			while($row = $db->fetch_array($query)) 
			{
			   return $row['name'];
			}
		}
		else
		{
			$query = $db->query("SELECT * FROM announcements WHERE announ_id = '$id'");
			while($row = $db->fetch_array($query)) 
			{
				return $row[$info];
			}
		}		
	}
	
	
	public function users_info($id,$info = NULL,$group = NULL,$email = NULL)
	{
		global $db;
		
		if ($email)
		{
			$query = $db->query("SELECT * FROM users WHERE email = '$email'");
		}
		else
		{
			$query = $db->query("SELECT * FROM users WHERE id = '$id'");
		}
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
	
	public function files_info($id,$info = NULL)
	{
		global $db;
		$query = $db->query("SELECT * FROM resources_files WHERE id = '$id'");
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
	
	public function level_info($id,$info = NULL)
	{
		global $db;
		$query = $db->query("SELECT * FROM level WHERE id = '$id'");
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
	
	public function resource_sites($id,$type)
	{
		global $db;
		if ($type == 'file')
		{
			$table = 'resources_files_sites';
		}
		else
		if ($type == 'text')
		{
			$table = 'resources_text_sites';
		}
		else
		if ($type == 'link')
		{
			$table = 'resources_links_sites';
		}
		
		$query = $db->query("SELECT site_id FROM `".$table."` WHERE ".$type."_id = '$id'");
		//echo "SELECT site_id FROM ".$table." WHERE ".$type."_id = '$id'";
		while($row = $db->fetch_array($query)) 
		{
			$groups .= $row['site_id'] . ',';
		}
		
		$groups = rtrim($groups,',');
		return $groups;
	}
	
	
	public function sites_info($id,$info = NULL, $group = NULL)
	{
		global $db;		
		if ($group == 1)
		{
			$query = $db->query("SELECT * FROM sites WHERE group_id = '$id'");
		}
		else
		{
			$query = $db->query("SELECT * FROM sites WHERE id = '$id'");
		}
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
	
	
	public function users_groups($id,$gid = null)
	{
		global $db;
		if (!$gid)
		{
			$query = $db->query("SELECT group_id FROM users_groups WHERE user_id = '$id'");
			while($row = $db->fetch_array($query)) 
			{
				if (Bridge::sites_info($row['group_id'],'enabled'))
				{
					$groups .= $row['group_id'] . ',';
				}
			}
			
			$groups = rtrim($groups,',');
			return $groups;
		}
		else
		{
			$query = $db->query("SELECT * FROM users_groups WHERE user_id = '$id' AND group_id='$gid'");
			while($row = $db->fetch_array($query)) 
			{
				return $row['level'];
			}
		}
	}
	
	public function tickets_priority ($id)
	{
		global $db;
		$query = $db->query("SELECT * FROM tickets_priority WHERE id = '$id'");
		while($row = $db->fetch_array($query)) 
		{
			return $row['priority'];
		}
	}
	
	public function role_info($id,$info = NULL)
	{
		global $db;
		$query = $db->query("SELECT * FROM role WHERE id = '$id'");
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
		
	public function limit_words($string, $word_limit)
	{
		$words = explode(" ",$string);
		if (count($words) > $word_limit)
		{
			$elip = '...';
		}
		return implode(" ",array_splice($words,0,$word_limit)) . $elip;
	}
 
 

	
	public function users_role($id,$gid)
	{
		global $db;
		$query = $db->query("SELECT role FROM users_role WHERE user_id = '$id' AND group_id = '$gid' LIMIT 1");
		
		while($row = $db->fetch_array($query)) 
		{
			return $row['role'];
		}
	}
		
	public function address_info($id,$info = NULL)
	{
		global $db;
		$query = $db->query("SELECT * FROM address WHERE id = '$id'");
		while($row = $db->fetch_array($query)) 
		{
			return $row[$info];
		}
	}
}

?>