<?php
class Model_Announcements extends Model
{
	public $f3,$mybb,$mybbi,$db;
	
	public function getTypes()
	{
		$query = $this->db->query("SELECT type_id,name FROM announcements_type");
		while($row = $this->db->fetch_array($query)) 
		{
		   $announType .= '<option value="'.$row['type_id'].'">'.$row['name'].'</option>';
		}
		return $announType;
	}
	
	public function updatePost($vars,$action = 'update')
	{
		if ($action == 'disable')
		{
			$this->db->query("UPDATE announcements SET enabled=0 WHERE announ_id = '$vars[id]'");		
		}
		else
		if ($action == 'enable')
		{
			$this->db->query("UPDATE announcements SET enabled=1 WHERE announ_id = '$vars[id]'");	
		}
		else
		if ($action == 'delete')
		{
			$this->db->query("DELETE FROM announcements WHERE announ_id = '$vars[id]'");	
		}
		else
		{
			$bug_Desc = $this->db->escape_string($vars['siteLeft']);
			$shortTitle = $this->db->escape_string($vars['homepagetitle']);
			$mainTitle = $this->db->escape_string($vars['announcementtitle']);
			$sdate = strtotime($vars['sdate']);
			$edate = strtotime($vars['edate']);
			$aFile = Bridge::uploadAnn('files/pdfs/',0);
			$aImg = Bridge::uploadAnn('files/imgs/',1);
			
			if ($action == 'add')
			{
				$aType = $vars['announType'];
				$res = $this->db->query("INSERT INTO announcements (announ_short, announ_title, announ_left, announ_url, announ_type, announ_author, startdate, enddate, enabled, announ_image) VALUES('". $shortTitle ."', '". $mainTitle ."', '". $bug_Desc ."', '". $aFile ."', '". $aType ."', '". $this->mybb->user['uid'] ."', '". $sdate ."', '". $edate ."', 1, '". $aImg ."')");
				$action = 'adde';
			}
			else
			{
				if ($aFile)
				{
					$this->db->query("UPDATE announcements SET announ_url='".$aFile."' WHERE announ_id=".$vars['id']) or die($this->db->error());
				}	
				
				if ($aImg)
				{
					$this->db->query("UPDATE announcements SET announ_image='".$aImg."' WHERE announ_id=".$vars['id']) or die($this->db->error());
				}	
				
				if (isset($_POST['clearAttach']))
				{
					$this->db->query("UPDATE announcements SET announ_url='announcements/', announ_image='' WHERE announ_id=".$vars['id']) or die($this->db->error());
				}
				
				$this->db->query("UPDATE announcements SET announ_short='".$shortTitle."', announ_title='".$mainTitle."', startdate='".$sdate."', enddate='".$edate."', announ_left='".$bug_Desc."' WHERE announ_id=".$vars['id']) or die($this->db->error());
			}
		}
	}
	
	public function getURI($id)
	{
		$query = $this->db->query("SELECT * FROM announcements WHERE announ_id = '".$id."'");
		while ($row = $this->db->fetch_array($query))
		{
			$sdate = date('m/d/Y', $row['startdate']);
			$date = new DateTime($sdate);
			if ( (stripos($row['announ_url'],'pdfs')) && ( (!empty($row['announ_image'])) || (!empty($row['announ_left'])) ) )
			{
				$link = '/'.Bridge::seo(Bridge::announcements_info($row['announ_type'],'type')).'/'.$row['announ_id'].'/'.Bridge::seo($row['announ_title']).'';
			}			
			else
			{
				if ( (stripos($row['announ_url'],'pdfs')) || (!empty($row['announ_image'])) )
				{
				$link = $row['announ_url'];
				}
				else
				{
				$link = '/'.Bridge::seo(Bridge::announcements_info($row['announ_type'],'type')).'/'.$row['announ_id'].'/'.Bridge::seo($row['announ_title']).'';
				}
			}			
		}
		return $link;
	}
	
	public function getAnnoun($sort = 'startdate',$order = 'desc')
	{
		if ($sort !== 'startdate' && $sort !== 'enddate' && $sort !== 'user' && $sort !== 'title' && $sort !== 'type')
		{
			$sort = 'startdate';
		}
		else
		if ($sort == 'title')
		{
			$sort = 'announ_short';
		}
		else
		if ($sort == 'type')
		{
			$sort = 'announ_type';
		}
		else
		if ($sort == 'user')
		{
			$sort = 'announ_author';
		}
		if ($order != 'asc' && $order != 'desc')
		{
			$order = 'desc';
		}
		
		
		$output .= '
		<tr class="head">
			<td>&nbsp;</td>
			'.Bridge::sortlink('Start Date',null,'startdate','asc','announcements').'
			'.Bridge::sortlink('End Date',null,'enddate','asc','announcements').'
			'.Bridge::sortlink('Type',null,'type','asc','announcements').'
			'.Bridge::sortlink('Homepage Title',null,'title','asc','announcements').'
			'.Bridge::sortlink('User',null,'user','asc','announcements').'
			</tr>';
		
		
		if (!($this->f3->get('admin'))) {
			$res = $this->db->query("SELECT * FROM announcements WHERE enabled = 1 ORDER BY ".$sort." ".$order."");
		}
		else
		{
			$res = $this->db->query("SELECT * FROM announcements ORDER BY ".$sort." ".$order."");
		}
		$enabled = 0;
		while ($row = $this->db->fetch_array($res)) {
			$id = $row['announ_id'];
			//$color = ($prior == 'Immediate' ? '3' : ($prior == 'High' ? '2' : ($prior == 'Medium' ? '1' : '0')));
			$short = $row['announ_short'];			
			$left = $row['announ_left'];
			$by = get_user($row['announ_author']);
			$by = $by['username'];
			if (empty($by))
			{
				$by = Bridge::users_info($row['announ_author'],'fname') . ' ' . Bridge::users_info($row['announ_author'],'lname');
			}
		//	$by = $row['announ_author'];z
			//$time = strtotime($row['date_time']);
			//$countTime = date('c',$time);
			$enabled = $row['enabled'];
			$sdate = date('m/d/Y', $row['startdate']);
			$edate = date('m/d/Y', $row['enddate']);
			$type = Bridge::announcements_info($row['announ_type'],'type');
			if ($enabled) 
			{
				$color = 'enabled-row';										
			} 
			else 
			{
				$color = 'disabled-row';	
			}
			
			$output .= '<tr enabled="'.$enabled.'" id="'.$id.'" email="'.$by.'" class="'.$color.' node">
					<td class="siteLeft" style="display:none;visibility:hidden;">'.$left.'</td>
					<td style="display:none;visibility:hidden;">'.$row['announ_title'].'</td>								
					<td class="'.$color.'"><i class="glyphicon glyphicon-eye-open glyphicon glyphicon-white headerIcon"></i></td>
					<td class="'.$color.'">'.$sdate.'</td>
					<td class="'.$color.'">'.$edate.'</td>	
					<td class="'.$color.'">'.$type.'</td>
					<td class="'.$color.'">'.$short.'</td>
					
					<td class="'.$color.'">'.$by.'</td>
				</tr>
			';
			
		}

		return $output;
						
	}
	
	public function data($id)
	{
		$thu = $this->db->query("SELECT * FROM announcements WHERE announ_id = '".$id."'");
		while ($tho = $this->db->fetch_array($thu))
		{
			$this->config('page_name',$tho['announ_title']);
			$output .= '<h1 id="anouncmentName" style="color: black;font-weight: bold;width:570px; position: relative;top: 0px;">'.$tho['announ_title'].'</h1>';
			$output .= '<h3 style="color: black;font-weight: bold;width:570px; position: relative;">'.Bridge::fdate($tho['startdate']).' to '.Bridge::fdate($tho['enddate']).'</h3>';
			if ($tho['announ_image'])
			{
				$img = '<img style="width:100%" src="/' . $tho['announ_image'] . '" alt="" />';
			}
		
			if ((stripos($tho['announ_url'],'pdfs')))
			{
				$pdf = '<div style="text-align:center;font-weight:bold;padding:10px;"><a href="'.$this->f3->get('url').''.$tho['announ_url'].'">Download PDF</a></div>';
			} 
			
			$date = '<div style="text-align:center;font-weight:bold;padding:10px;">'.Bridge::fdate($tho['startdate']).'</div>';
			
			if ((stripos($tho['announ_url'],'pdfs')) || ($tho['announ_image']))
			{
				$output .= '<h3 class="msucolor" style="margin-top: 0px;">'. $tho['announ_title'] .'</h3>';
				
					$output .= '<div style="float: left;padding:15px;padding-left:0;width:250px;"><a target="_blank" href="/' . $tho['announ_image'] . '">
					'.$img.'</a>'.$date . $pdf.'</div>';
			}
			$output .= str_repeat($tho['announ_left'],1);
		}
		return $output;
	}
}
?>