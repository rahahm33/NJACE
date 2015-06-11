<?php
	class Model_Media extends Model
	{
		public $f3,$mybb,$mybbi,$db;
		
		public function media($siteID)
		{
			$res = $this->db->query("SELECT * FROM images WHERE group_id='".$siteID."'");
			$result = array();
			while ($row = $this->db->fetch_array($res)) 
			{
				$title = $row['title'];
				$desc = $row['description'];
				
				if(empty($row['title']))
				{
					$title = "No Title";
				}
				if(empty($row['description']))
				{
					$desc = "No Description";
				}
				$result[]= array($row['id'] => array($row['src'], $title, $desc));
			}
			//echo'<pre>';print_r($result);echo'</pre>';
			return $result;
		}
		
		public function bringMediaRecords($siteID)
		{
			$res = $this->db->query("SELECT * FROM images WHERE group_id='".$siteID."'");
			//needs to be ordered based on priority
			$res2 = $this->db->query("SELECT count(*) as rowspan FROM images WHERE group_id='".$siteID."'");
			$rowspan = 0;
			$i=0;
			while ($row = $this->db->fetch_array($res2)) 
			{
				$rowspan = $row['rowspan'];
			}
			
			while ($row = $this->db->fetch_array($res)) 
			{
				$title = $row['title'];
				$desc = $row['description'];
				
				if(empty($row['title']))
				{
					$title = "No Title";
				}
				
				if(empty($row['description']))
				{
					$desc = "No Description";
					$htmlDesc = '<h6 style="color:#7B7B7B">Description: ' . substr($desc, 0, 200) .'</h6>';
				}
				else
				{
					$htmlDesc = '<h6 style="color:#7B7B7B">Description: ' . substr($desc, 0, 200) . ' ...</h6>';
				}
				
				
				
				//images
				if($row['type']=="image")
				{
					if($i==0)
					{
						$output .= '
						<tr class="media_tr" name="'.$i.'" style="cursor: pointer; width:40%" id=' . $row['id'] . '>
							<td style="width:50%">
								<h5><b>Title: ' . $title . '</b></h5>
								'.$htmlDesc.'
							</td>
							<td id="showMedia" rowspan="'.$rowspan.'"></td>
						</tr>
						';
					}
					else
					{
						$output .= '
						<tr class="media_tr" name="'.$i.'" style="cursor: pointer; width:40%" id=' . $row['id'] . '>
							<td style="width:50%">
								<h5><b>Title: ' . $title . '</b></h5>
								'.$htmlDesc.'
							</td>
						</tr>
						';
					}
				}
				else //videos
				{
					if($i==0)
					{
						$output .= '
						<tr class="media_tr" name="video_'.$i.'" style="cursor: pointer; width:40%" id=' . $row['id'] . '>
							<td style="width:50%">
								<h5><b>Title: ' . $title . '</b></h5>
								'.$htmlDesc.'
							</td>
							<td id="showMedia" rowspan="'.$rowspan.'"></td>
						</tr>
						';
					}
					else
					{
						$output .= '
						<tr class="media_tr" name="video_'.$i.'" style="cursor: pointer; width:40%" id=' . $row['id'] . '>
							<td style="width:50%">
								<h5><b>Title: ' . $title . '</b></h5>
								'.$htmlDesc.'
							</td>
						</tr>
						';
					}
				}
				
				
				$i++;
			}
			
			return $output;
		}
		
		public function updatepictures($imageData, $src, $title, $desc, $groupid, $imageID)
		{
			//$imageData = $_POST['imageData'];
			//$src = $_POST['src'];
			
			$filename = '../assets/img/raw/' . $src;

			if (file_exists($filename)) {
				unlink($filename);
			}
			
			list($meta, $content) = explode(',', $imageData);
			$content = base64_decode($content);
			
			file_put_contents('../assets/img/raw/' . $src, $content, LOCK_EX);

			//$title = $_POST['title'];
			//$desc = $_POST['desc'];
			//$groupid = $_POST['groupid'];
			//$imageID = $_POST['imageID'];
			
			//echo'<pre>';print_r($_POST);echo'</pre>';
			
			//mysql_query("UPDATE images SET title='".$title."', description='".$desc."' WHERE id=".$imageID." AND group_id=".$groupid);
			//echo "DELETE FROM images WHERE id = '$imageID'";
			//echo("title is:" . $titel);
			//echo("desc is:" . $desc);
			//first we have to delete the old images
			$this->db->query("DELETE FROM images WHERE id = '$imageID'");
			
			//then we insert the new cropped images
			$this->db->query("INSERT INTO images (`group_id`, `src`, `title`, `description`) VALUES ('$groupid' ,'$src', '$title', '$desc')");
		}
	}
?>