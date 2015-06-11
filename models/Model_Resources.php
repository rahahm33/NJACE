<?php
class Model_Resources extends Model
{
	public $f3,$mybb,$mybbi,$db;
	public function rlink($id,$type = '') 
	{
		if ($this->f3->get('member'))
		{
			return '<a class="rlink_'.$type.'_'.$id.'" href="/forum/newthread.php?fid='.$id.'">Add New Resource</a>';
		}
	}
	public function data($forumid,$sitesVar = '')
	{
		$resource = array();
		if ($this->f3->get('member'))
		{
			if (!empty($forumid))
			{
				$fid = 'AND fid IN ('.$forumid.')';
				
				$fids = explode(',',$forumid);
				foreach ($fids as $key)
				{
					if (!is_numeric($key))
					{
						unset($fid);
					}
				}
				$query = $this->db->query("SELECT * FROM mybb_forums WHERE active = 1 ".$fid." ORDER BY disporder ASC");
			}
			else
			{
				$query = $this->db->query("SELECT * FROM mybb_forums WHERE pid = 2 AND active = 1 ORDER BY disporder ASC");
			}								
			
		}
		else
		{
			if (!empty($forumid))
			{
				$fid = 'AND fid IN ('.$forumid.')';
				
				$fids = explode(',',$forumid);
				foreach ($fids as $key)
				{
					if (!is_numeric($key))
					{
						unset($fid);
					}
				}
				$query = $this->db->query("SELECT * FROM mybb_forums WHERE active = 1 ".$fid." ORDER BY disporder ASC");
			}
			else
			{
				$query = $this->db->query("SELECT * FROM mybb_forums WHERE pid = 2 AND fid IN (14,22,27) AND active = 1 ORDER BY disporder ASC");
			}
		}
		while ($row = $this->db->fetch_array($query)) 
		{	
			foreach ($row as $key=>$val)
			{
				$resource[$row[fid]][$key] = $val;
			}
			
			$i = 0;
			$cols = count($row);
			$fid = $row['fid'];
			$fid1 = range(28,33);
			$fid2 = range(15,20);
			foreach ($fid1 as $key=>$val)
			{
				if ($fid == $val)
				{
					$fid = $val . ',' . $fid2[$key];
				}
			}
			
			if ($sitesVar)
			{
				$sitecheck = 'AND inviteonlyuids IN ('.$sitesVar.')';
			}
			
			$query1 = $this->db->query("SELECT * FROM mybb_threads WHERE fid IN ($fid) ".$sitecheck."  AND closed = '' AND sticky = 0 AND visible != '-1' ORDER BY dateline ASC");
			while ($row1 = $this->db->fetch_array($query1))
			{																		
				$i++;
				
				foreach ($row1 as $key=>$val)
				{
					$resource[$row[fid]][$i][$key] = $val;
				}								
			}

		
	
			$query2 = $this->db->query("SELECT * FROM mybb_forums WHERE pid IN ($fid) AND active = 1 ORDER BY disporder ASC");
			while ($row2 = $this->db->fetch_array($query2))
			{																	
				$i++;				
				foreach ($row2 as $key=>$val)
				{
					$resource[$row[fid]][$i][$key] = $val;
				}
				
			}	
		}
		$t = 0;
		
		foreach ($resource as $s => $row)
		{
			$i = 0;
			
			$t++;
			//$return .= $t;
			$delLink = 'glyphicon glyphicon-list-alt headerIcon';
			if ($sitesVar)
			{
				$hidden = 'display:none;';
			}
			
			if ($t == 1)
			{
			$return .= '<div class="navigation" style="'.$hidden.'font-size:12px;">';
				
				
				$parents = explode(',',$row['parentlist']);
				foreach ($parents as $key)
				{										
					$forum = get_forum($key);										
					$navout .= ' ›
					<!-- end: nav_sep -->
					<!-- end: nav_bit --><!-- start: nav_bit -->';
					if ( ($forumid == $forum['fid']) || (empty($forumid)) )
					{
						$navout .= $forum['name'];
					}
					else
					{
						$navout .= '<a href="/forum/forum-'.$forum['fid'].'.html">'.$forum['name'].'</a>';
					}
					$navout .= '<!-- end: nav_bit -->';
					if (empty($forumid))
					{
						break;
					}
				}
				$navout = rtrim(ltrim($navout,' ›'),' ›');
				$return .= $navout;
			$return .= '</div>';
			}
			$return .= '<nav class="bg-heading navbar-default">
		<div class="container-fluid"><div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#search-collapse" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<h4>					
					<span style="margin-right:10px" class="'.$delLink.'" id="cat_'.$s.'"></span>
					<a href="/forum/forum-'.$row['fid'].'.html">'.$row['name'].'</a>
				</h4>
			</div>
			<div class="navbar-collapse collapse" id="search-collapse" aria-expanded="false" style="height: 1px;">	
				<div class="navbar-right" style="margin-top:10px;">';
					if ( ($row['type'] == 'f') && (!$row['linkto']) && ($row['open']) )
					{
						$return .= Model_Resources::rlink($s);
					}
					if ($this->f3->get('admin'))
					{
						$return .= '<a style="margin-left:20px;" href="/forum/forum-'.$row['fid'].'.html?&mod=1">Moderate</a>';
					}	
				$return .='					
				</div>		
			</div></div>
    </nav>';
			$return .= '
				
				<div class="table-responsive">
					<table id="'.$s.'_'.strtolower($row['name']).'" class="'.$row['type'].' table table-hover users">';
					if ( (!$row['linkto']) && ($row['open']) )
					{
						if ($row['type'] == 'c')
						{
							$return .= '<thead><tr class="head">
						
							<th colspan="2" style="width:35%">Title</td>
							<th style="width:30%">Shared With</td>
							<th style="width:20%">Last Post</td>
							<th style="width:10%">Member</td>
							</tr></thead><tbody>';
						}
						else
						{
							$return .= '<thead><tr class="head">
						
							<th style="width:35%">Title</td>
							<th style="width:10%">Hits</td>
							<th style="width:30%">Shared With</td>
							<th style="width:20%">Upload Date</td>
							<th style="width:10%">Member</td>
							</tr></thead><tbody>';
							
						}
					unset($delLink);
					
						
						
						foreach ($row as $src)
						{	
							$i++;
							
							if ($i > $cols)
							{		
								
								$sites = explode(',',$src['inviteonlyuids']);
								foreach ($sites as $key)
								{
									
									$fileSites .= '<a href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
									
								}
								
								$fileSites = rtrim($fileSites,', ');
								if ($src['inviteonlycheck'] == 0)
								{
									$access = 'All Research Sites';
								}
								else
								if ($src['inviteonlycheck'] == 1)
								{
									$access = $fileSites;
								}
							
								
							
								if ($src['pid'])
								{
									$views = Bridge::forum_info($src['fid'],'hits');
									$fthreads = Bridge::forum_info($src['fid'],'threads');
									$date = Bridge::forum_info($src['fid'],'lastpost');
									$lastposter = Bridge::forum_info($src['fid'],'lastposter');
									$lastposterid = Bridge::forum_info($src['fid'],'lastposteruid');
									$fid1 = range(28,33);
									$fid2 = range(15,20);
									foreach ($fid1 as $key=>$val)
									{
										if ($src['fid'] == $val)
										{
											$views = Bridge::forum_info($fid2[$key],'hits') + Bridge::forum_info($val,'hits');
											$fthreads = Bridge::forum_info($fid2[$key],'threads') + Bridge::forum_info($val,'threads');
											if (empty($date))
											{
												$date = Bridge::forum_info($fid2[$key],'lastpost');
											}
											if (empty($lastposter))
											{
												$lastposter = Bridge::forum_info($fid2[$key],'lastposter');
											}
											
											if (empty($lastposterid))
											{
												$lastposterid = Bridge::forum_info($fid2[$key],'lastposteruid');
											}
										}
									}
									
									//print_r2(get_forum(15));	
									
									$output .= '<tr class="user">														
									<td colspan="2" title="'.$src['name'].'"><span class="hidden">1.</span><a style="border-bottom:1px dotted;" title="'.$src['name'].'" href="/forum/forum-'.$src['fid'].'.html">'.$src['name'].'</a>';
									
									$output .= '<br /><small style="font-style:italic;">'.$fthreads.' Resources&nbsp;&nbsp;&bull;&nbsp;&nbsp;'.$views.' Hits</small>';
									
									$output .='
									</td>
									<td>'.$access.'</td>
									<td>'.Bridge::fdate($date).'</td>
									<td><a href="/forum/user-'.$lastposterid.'.html">'.$lastposter.'</a></td>
									
									</tr>';
								}
								else
								{
									$output .= '<tr class="user">														
									<td title="'.$src['subject'].'"><a title="'.$src['subject'].'" href="/forum/thread-'.$src['tid'].'.html">'.$src['subject'].'</a></td>
									<td>'.$src['views'].'</td>
									<td>'.$access.'</td>
									<td date="'.date('m/d/Y',$src['dateline']).'">'.Bridge::fdate($src['dateline']).'</td>
									<td><a href="/forum/user-'.$src['uid'].'.html">'.$src['username'].'</a></td>
									
									</tr>';

								}
								
								unset($fileSites,$delLink,$editLink,$access);
							}
							
						}
					}
			if ( (!$row['linkto']) && ($row['open']) )
			{		
				if (empty($output)) 
				{
					$output .= '<tr><td colspan="5">No '.$row['name'].' resources</td></tr>';
				}
			}
			
			$return .= $output;
			unset($output);
			
			$return .= '</tbody></table>
				</div>';
		}
		return $return;
	}
	
}