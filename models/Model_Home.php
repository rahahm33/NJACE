<?php
class Model_Home extends Model
{
	public $f3,$mybb,$mybbi,$db;
	
	public function newsfeed()
	{
		$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$result = $this->db->query("SELECT * FROM announcements WHERE NOT announ_id = '0' ORDER BY startdate");
		while ($row = $this->db->fetch_array($result))
		{
			$sdate = date('m/d/Y', $row['startdate']);
			$date = new DateTime($sdate);
			
				
				$link = '/'.Bridge::seo(Bridge::announcements_info($row['announ_type'],'type')).'/'.$row['announ_id'].'/'.Bridge::seo($row['announ_title']).'';
				
			
			if ($row['enabled'] == 1)
			{
				if ( ((time() < $row['enddate']) && ($row['announ_type'] == 1)) || (($row['announ_type'] == 2)) )
				{
	
					$output .= '<a href="'.$link.'"><div class="announ-block row">
									<div class="date col-lg-2">
										<div class="col-lg-12 month">'.$months[$date->format('m') - 1].'</div>
										<div class="col-lg-12 day">'.$date->format('d').'</div>
									</div>
									<div class="title col-lg-8">
										'.$row['announ_short'].'
									</div>
									<div class="type col-lg-4 pull-right">
										'.Bridge::announcements_info($row['announ_type'],'type').'
									</div>
								</div></a>';
				}
			}
		}
		return $output;
	}
	
	public function siteMapXML()
	{
		
		$output.= '<?xml version="1.0" encoding="UTF-8"?>
			<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
				<url>
					<loc>'.$this->f3->get('url').'</loc>
					<changefreq>monthly</changefreq>
					<priority>1.0</priority>
				</url>
				<url>
					<loc>'.$this->f3->get('url').'resources</loc>
					<changefreq>monthly</changefreq>
					<priority>0.6</priority>
				</url>
				<url>
					<loc>'.$this->f3->get('url').'announcements</loc>
					<changefreq>monthly</changefreq>
					<priority>0.6</priority>
				</url>
				<url>
					<loc>'.$this->f3->get('url').'how-can-i-help</loc>
					<changefreq>monthly</changefreq>
					<priority>0.6</priority>
				</url>
				<url>
					<loc>'.$this->f3->get('url').'ticket/submit</loc>
					<changefreq>monthly</changefreq>
					<priority>0.6</priority>
				</url>
				<url>
					<loc>'.$this->f3->get('url').'site-map</loc>
					<changefreq>monthly</changefreq>
					<priority>0.4</priority>
				</url>';
		
		
		$query = $this->db->query("SELECT * FROM sites");
		while ($row = $this->db->fetch_array($query))
		{
			$output.= '
			<url>
				<loc>'.$this->f3->get('url').''.$row['id'].'/'.Bridge::seo($row['short_name']).'</loc>
				<changefreq>monthly</changefreq>
				<priority>0.8</priority>
			</url>';
		}
		
		$query = $this->db->query("SELECT * FROM announcements WHERE enabled = 1");
		while ($row = $this->db->fetch_array($query))
		{
			$output.= '
			<url>
				<loc>'.$this->f3->get('url').''.Bridge::seo(Bridge::announcements_info($row['announ_type'],'type')).'/'.$row['announ_id'].'/'.Bridge::seo($row['announ_title']).'</loc>
				<changefreq>weekly</changefreq>
				<priority>0.6</priority>
			</url>';
		}
		
		$query = $this->db->query("SELECT * FROM mybb_forums WHERE pid = 2 AND fid IN (14,22,27) AND active = 1 ORDER BY disporder ASC");
		while ($row = $this->db->fetch_array($query))
		{
			$output.= '
			<url>
				<loc>'.$this->f3->get('url').'resources/'.$row['fid'].'/'.Bridge::seo($row['name']).'</loc>
				<changefreq>weekly</changefreq>
				<priority>0.6</priority>
			</url>';
		}
		
		
		$output.= '</urlset>';
		
		return $output;
	}
	
	public function siteMap()
	{
		$output ="";
		if (!(Bridge::defaults('member')))
		{
			$level = 0;
		}
		else
		{
			$level = 1;
		}
		
		$output.= '<div class="container">
						<div class="row">';
		$output.= '<ul>';
		
		if ($level >= 0)
		{
			$output.= '<li><a href="'.Bridge::defaults('url').'" style="font-weight:bold">Home</a></li>';
		}
		
		if ($level >= 0)
		{
			$output.= '<li><a href="javascript:void(0);" style="font-weight:bold">Resources</a></li><ul>';
		}
		
		if ($level >= 0)
		{
			$output.= '<li><a href="'.Bridge::defaults('url').'my-resources">My Resources</a></li>';
		}
		
		if ($level >= 0)
		{
			$query = $this->db->query("SELECT * FROM resources_text WHERE cat_id = 12");
			while ($row = $this->db->fetch_array($query))
			{
				$output.= '<li><a href="'.Bridge::defaults('url').'resources/'.$row['id'].'/'.Bridge::seo($row['title']).'">'.$row['title'].'</a></li>';
			}
			$output.= '</ul>';
			
			$output.= '<li><a href="javascript:void(0);" style="font-weight:bold">Research Sites</a></li><ul>';
			$query = $this->db->query("SELECT * FROM sites WHERE short_name != 'test'");
			while ($row = $this->db->fetch_array($query))
			{
				$output.= '<li><a href="'.Bridge::defaults('url').''.$row['id'].'/'.Bridge::seo($row['short_name']).'">'.$row['short_name'].'</a></li>';
			}
			$output.= '</ul>';
		
			$output.= '<li><a style="font-weight:bold" href="'.Bridge::defaults('url').'how-can-i-help">How can I help?</a></li>';
			$output.= '<li><a style="font-weight:bold" href="'.Bridge::defaults('url').'ticket/submit">Submit a Ticket</a></li>';
		}

		if (!(Bridge::defaults('member')))
		{
			$output.= '<li><a style="font-weight:bold" href="'.Bridge::defaults('url').'login">Login</a></li><ul>';
			$output.= '<li><a href="'.Bridge::defaults('url').'login/reset">Reset Password</a></li></ul>';
		}
		else
		{
			$output.= '<li><a style="font-weight:bold" href="'.Bridge::defaults('url').'logout">Logout</a></li><ul>';
		}
		$output.= '</ul>';
		$output.= '</div></div>';
		
		return $output;
	}
}