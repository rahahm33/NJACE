<?php
class Model_Medias extends Model
{
	public function info($id)
	{
		$query = $this->db->query("SELECT * FROM images WHERE id = '".$id."'");
		return ($this->db->fetch_array($query));
	}
	
	public function data($id)
	{
		$query = $this->db->query("SELECT * FROM images WHERE group_id = '".$id."' ORDER BY priority ASC");
		$count = $this->db->num_rows($query);
		$output .= '<tr>
				<th>Order</th>
				<th style="width:15%">Images</th>
				<th>Info</th>
                                <th></th>
				
			</tr>';
		while ($row = $this->db->fetch_array($query))
		{
	
			$output .= '
			<tr id="'.$row['id'].'" >
				<td>
					<select class="priority" name="priority">';
				
					for($i = 0; $i < $count; $i++)
					{
						if ($row['priority'] == $i)
						{
							$output .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
						}
						else
						{
							$output .= '<option value="'.$i.'">'.$i.'</option>';
						}
					}
					
				$output .= '</select>
				</td>';
			
			if ($row['type'] == 'image')
			{
                            $output .= '<td><img class="img-thumbnail img-responsive" src="/img/raw/'. $row['src'].'" alt="" /></td>';
			}
			else
			{
				$output .= '<td><div class="img-thumbnail embed-responsive embed-responsive-4by3">
				<iframe class="img-thumbnail embed-responsive-item" src="'. $row['src'].'"></iframe>
				</div></td>';
			}

                       // if(!empty($row['title'])){
                           $title = $row['title'];
                        //}
                        //else{
                           
                        //}
                        //if(!empty($row['description'])){
                            $desc = $row['description'];
                        //}
                        
			$output .='<td>
                                    <a class="title" style="font-weight:bold" href="javascript:void()">'.$title.'</a><br />
                                    <a class="description" href="javascript:void()">'.$desc.'</a>
				</td>';
                        $output.= '<td><a class="crop btn btn-warning" href="/crop/'.$row['id'].'">Crop / Resize</a></td></tr>';
                        
		}
                
		return $output;
	}
	
	public function delImage($fileID, $fileSrc, $sideID)
	{
		
	}
	
	public function getYouTubeIdFromURL($url)
	{
		$url_string = parse_url($url, PHP_URL_QUERY);
		parse_str($url_string, $args);
		return isset($args['v']) ? $args['v'] : false;
	}
	
	public function getYouTubeIdFromURL2($url) //regular expression
	{
		$pattern = '/(?:youtube.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu.be/)([^"&?/ ]{11})/i';
		preg_match($pattern, $url, $matches);

		return isset($matches[1]) ? $matches[1] : false;
	}
	
	public function get_youtube_info($url, $request, $group_id)
	{
		$result = array();
		$id = Model_Medias::getYouTubeIdFromURL($url);
		$data=@file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.$id.'&key=AIzaSyCOx2QslX55fkTfusMot5aYo7sUCXqRheM&alt=json&prettyprint=true');
		if (false===$data)
		{
			return false;
		}
		$obj = json_decode($data);
		for($i = 0; $i < count($request); $i++) {
			$result[]= $obj->items[0]->snippet->$request[$i];
		}
		
		$src="http://www.youtube.com/embed/".$id;
		$this->db->query("INSERT INTO images(group_id, src , title, description, type, priority) VALUES('".$group_id."', '".$src."', '".$result[0]."', '".$result[1]."', 'video', '0')");
		
	}
}

?>