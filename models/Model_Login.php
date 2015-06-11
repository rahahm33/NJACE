<?php
class Model_Login extends Model
{
	public $f3,$mybb,$mybbi,$db;
	
	public function login()
	{	
		$email = $this->db->escape_string(stripcslashes($_POST['email']));
		$password = $this->db->escape_string(stripcslashes($_POST['password']));
		// missing field validation
		if (!isset($_POST['email']) || !isset($_POST['password'])) 
		{
			$this->redirect('/','Please fill out both fields.');
		}
		else
		{	
			
			$query = $this->db->query("SELECT *,COUNT(uid) as count FROM mybb_users WHERE email = '$email'");
			while ($row = $this->db->fetch_array($query))
			{	
				// If user has MyBB account, log in!
				if ($row['count'])
				{	
					
					$login_status = $this->mybbi->login($row['username'], $this->mybbi->mybb->input['password']);
					
					if ($login_status == true)
					{
						
						$output .= '<script type="text/javascript">
							loginMyBB(\''.$row['username'].'\',\''.$this->mybbi->mybb->input['password'].'\');
						</script>';
						$this->main($output);
						$this->f3->reroute('/');
					}
					else
					{
						//$output .= 'no';
						$this->main($output);
						$this->redirect('/login', 'Incorrect Email/Password');
					} 
				}
				else
				{
					$query1 = $this->db->query("SELECT *,COUNT(id) as count FROM users WHERE email = '$email'");
					while ($row1 = $this->db->fetch_array($query1))
					{
						// If user has old account, migrate to MyBB
						if ($row1['count'])
						{
							if (($row1['password']) == (md5($password)))
							{
								if ($row1['level'] == 2)
								{
									$usergroup = 4;
								}
								else
								{
									$usergroup = 2;
								}
								$username = $row1['fname'] . ' ' . $row1['lname'];
								$data = array (
									"username"  => $username,
									"password" => $password,
									"password2" => $password,
									"email" => $email,
									"email2" => $email,
									"usergroup" => $usergroup,
									
								);	
								$create = $this->mybbi->createUser($data);
								//print_r($create);
								if (!stripos($create,'errors'))
								{						
									$query2 = $this->db->query("SELECT uid FROM mybb_users WHERE username = '$username' LIMIT 1");
									while ($row2 = $this->db->fetch_array($query2))
									{
										$newid = $row2['uid'];
									}
									$oldid = $row1['id'];
									$phone = $row1['phone'];
									$fname = $row1['fname'];
									$lname = $row1['lname'];
									$this->db->query("UPDATE users_role SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE users_groups SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE time_log SET ticket_user = '$newid' WHERE ticket_user = '$oldid'");
									$this->db->query("UPDATE resources_cats SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE resources_files SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE resources_links SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE resources_text SET user_id = '$newid' WHERE user_id = '$oldid'");
									$this->db->query("UPDATE announcements SET announ_author = '$newid' WHERE announ_author = '$oldid'");
									$this->db->query("UPDATE users SET mybb = '1' WHERE id = '$oldid'");
									//$this->db->query("INSERT INTO mybb_userfields (ufid,fid4,fid5,fid6) VALUES ('".$newid."','".$phone."','".$fname."','".$lname."')");
									$this->db->query("UPDATE mybb_userfields SET fid4 = '$phone', fid5 = '$fname', fid6= '$lname' WHERE ufid = '$newid'");
									$output .= '<script type="text/javascript">
										loginMyBB(\''.$username.'\',\''.$password.'\');
									</script>';
									$this->redirect('/', 'Welcome back. Updating account...');
								}
								else
								{
									$this->redirect('/', 'Could not create account');
								}
							}
							else
							{
								$this->redirect('/login', 'Incorrect Email/Password');
							}
						}
						else
						{
							$this->redirect('/login', 'Incorrect Email/Password');
						}
					}
				}
			}
		}			
	}
}
?>