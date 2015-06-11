<?php

/********************************************************************************??
 *
 *  Invite Only Threads.
 *  Author: Pratik Unadkat (mybblabs.com)
 *  Copyright: © 2012 Pratik Unadkat (crazy4cs)
 *  
 *  Website: http://www.mybblabs.com/
 *  License: www.mybblabs.com/license.php
 *  Any codes in this plugin are copyrighted and not allowed to be reproduced or distributed.
 * 
 *  Allows a user to define who can view his thread.
 *  
 *  v1.3 : Now uses one query less and done some bug fixes.
 **********************************************************************************/

if(!defined("IN_MYBB"))
	die("This file cannot be accessed directly.");
	
$plugins->add_hook("newthread_start", "iot_newthread_display");
$plugins->add_hook('datahandler_post_validate_thread', 'iot_newthread_validate');
$plugins->add_hook("datahandler_post_insert_thread", "iot_newthread_action");
$plugins->add_hook("editpost_action_start", "iot_editpost_display");
$plugins->add_hook("editpost_do_editpost_start", "iot_editpost_action");
$plugins->add_hook("showthread_start", "iot_check");
$plugins->add_hook("newreply_start", "iot_reply_check");
$plugins->add_hook("forumdisplay_thread","iot_sites"); 

function iot_info()
{
    return array(
        "name"            => "Invite Only Threads.",
        "description"    => "Allows a user to define who can view or see his thread.",
        "website"        => "http://www.mybblabs.com",
        "version"        => "1.3",
        "author"        => "Pratik Unadkat",
        "authorsite"    => "http://www.mybblabs.com",
        "compatibility"  => "*",
        'guid'        => "62ca85866945ed4274e7f3dfb2166150"
    );
}
function iot_sites()
{
	global $mybb, $lang, $thread;
	
	
	$tid = $thread['tid'];
	$checkit = $thread['inviteonlycheck'];
	$check_uids = $thread['inviteonlyuids'];
		
	$uids_list = explode(",", $check_uids);
	$iot_groups = $mybb->settings['iot_usergroup'];
	$fid = $thread['fid'];
	$forums = explode(",", $mybb->settings['iot_fid']);

// My Groups
	$groups = Bridge::users_groups($mybb->user['uid']);	
	$groups = explode(',',$groups);	
	$access = 0;
	
	
	if( !(Bridge::defaults('member')) || (Bridge::defaults('admin')) )
	{
		foreach ($uids_list as $key)
		{
			$fileSites .= '<a href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
		}
	}
	else
	{		
		foreach ($groups as $val)
		{	
			unset($fileSites);
			if ($val)
			{
				foreach ($uids_list as $key)
				{
					
					if ($key == $val)
					{
						$access = 1;
						//echo $val . ' - ' . $key  .' <br />';
						$fileSites .= '<a style="font-weight:bold;" href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
					}
					else
					{
					//	echo $val . ' - ' . $key .  ' <br />';
						$fileSites .= '<a href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
					}
					
					//$fileSites .= '<a href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
					
				}
			}
			
		}
	}



	
	$fileSites = rtrim($fileSites,', ');
	if ($checkit == 0)
	{
		$accessSite = 'All Research Sites';
		$access = 1;
	}
	else
	if ($checkit == 1)
	{
		$accessSite = $fileSites;
	}
	
	if (Bridge::defaults('admin'))
	{
		$access = 1;
	}
	
	if (!$access)
	{
		$thread['hide'] = 'hidden';
	}
	//echo $access;
	
	if($mybb->settings['iot_enabled'] == "1")
	{
	$thread['sites'] = '<i><b>Shared with:</b></i> ' . $accessSite .'';
	}
	
	
}
function iot_install()
{

global $db;

$db->query("ALTER TABLE ".TABLE_PREFIX."threads ADD inviteonlycheck INT(1) NOT NULL");
$db->query("ALTER TABLE ".TABLE_PREFIX."threads ADD inviteonlyuids TEXT NOT NULL");
	
$iot = array(
        'name' => 'iot',
        'title' => 'Invite Only Threads',
        'description' => 'Manage settings for invite only threads plugin.',
        'disporder' => '999',
        'isdefault' => 'no'
    );
    $db->insert_query('settinggroups',$iot);
    $gid = $db->insert_id();
    
    
    $iot2 = array(
        "name" => "iot_enabled",
        "title" => "Is this plugin enabled?",
        "description" => "If selected yes, the plugin will function.",
        "optionscode" => "yesno",
        "value" => "0",
        "disporder" => "2",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $iot2);
	
	$iot3 = array(
        "name" => "iot_fid",
        "title" => "Allowed Forums",
        "description" => "Enter the forum id (fid) of the forum(s), in which the plugin is functional. Separate each fid with a comma.",
        "optionscode" => "text",
        "value" => "",
        "disporder" => "3",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $iot3);
	
	$iot4 = array(
        "name" => "iot_usergroup",
        "title" => "Allowed groups",
        "description" => "Which usergroups can use this feature? If more than one, seperate each by a comma.",
        "optionscode" => "text",
        "value" => "",
        "disporder" => "4",
        "gid" => intval($gid),
        );
    $db->insert_query("settings", $iot4);
	
		$template1 = array(
		"title"		=> "inviteonlythreads",
		"template"	=> '<tr>
<td class=\"trow2\" valign=\"top\"><strong>{$lang->iot_title}</strong></td>
<td class=\"trow2\"><span class=\"smalltext\">
<label><input type=\"checkbox\" class=\"checkbox\" name=\"inviteonlycheck\" value=\"1\" {$checked}/>&nbsp;{$lang->iot_cbox_label}</label><br />
<input type=\"text\" class=\"textbox\" name=\"inviteonlyuids\" size=\"40\" value=\"{$existinguids}\" tabindex=\"1\" /><br />
{$lang->iot_tbox_note}
</span></td>
</tr>',
		"sid"		=> -1
	);
	$db->insert_query("templates", $template1);
        
rebuild_settings();	
	
}

function iot_is_installed()
{

	global $db;
	
	if($db->field_exists("inviteonlycheck", "threads") && $db->field_exists("inviteonlyuids", "threads"))
	{
		return true;
	}
	
	return false;
}

function iot_activate()
{
    global $mybb;

    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("newthread", '#{\$modoptions}#', "{\$modoptions}{\$iot}");
	find_replace_templatesets("editpost", '#{\$disablesmilies}#', "{\$disablesmilies}{\$iot}");

}

function iot_deactivate()
{
    global $mybb;

    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("newthread", '#'.preg_quote('{$iot}').'#', '',0);
	find_replace_templatesets("editpost", '#'.preg_quote('{$iot}').'#', '',0);

}

function iot_uninstall()
{
    global $db;
	
$db->query("ALTER TABLE ".TABLE_PREFIX."threads DROP `inviteonlycheck`");	
$db->query("ALTER TABLE ".TABLE_PREFIX."threads DROP `inviteonlyuids`");	

$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='iot'");  
$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='iot_enabled'");
$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='iot_fid'");
$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='iot_usergroup'");
$db->delete_query("templates","title = 'inviteonlythreads'");

rebuild_settings();
	
}

function iot_perms($grp)
{
	global $mybb;
	
	if ($grp == '')
		return false;
		
	$groups = explode(",", $grp);
	
	$iotgroups = explode(",", $mybb->user['additionalgroups']);
	$iotgroups[] = $mybb->user['usergroup'];

	if(count(array_intersect($iotgroups, $groups)) == 0)
		return false;
	else
		return true;
}

//Eval when creating new thread

function iot_newthread_display()
{
    global $mybb, $templates, $iot, $lang, $db;
	
	$lang->load("iot");

	$fid = intval($mybb->input['fid']);
	$forums = explode(",",$mybb->settings['iot_fid']);
	$iot_groups = $mybb->settings['iot_usergroup'];
	
	
	
	
	
	
	$groups = Bridge::users_groups($mybb->user['uid']);	
	$groups = explode(',',$groups);	
	$access = 0;
	if(Bridge::defaults('admin'))
	{
		$access = 1;
		$query = $db->query("SELECT *,id as group_id FROM sites WHERE enabled = 1");
		while ($row = $db->fetch_array($query))
		{
			$fileSites .= '<option value="'.$row['group_id'].'">'.(Bridge::sites_info($row['group_id'],'short_name')).'</option>';
		}
	}
	else
	{
		foreach ($groups as $val)
		{	
			if ($val)
			{
				$access = 1;
				$fileSites .= '<option value="'.$val.'">'.(Bridge::sites_info($val,'short_name')).'</option>';
			}
		}
	}
	
	$groupselect .= '<select class="multiselect" id="inviteonlyuids" name="inviteonlyuids[]" multiple="multiple">' . $fileSites . '</select> <script type="text/javascript">
												$(\'#inviteonlyuids\').multiSelect({
									selectAll: false,
									noneSelected: \'All Research Sites\',
									oneOrMoreSelected: \'% Research Sites\'
								});
							
								</script>';
		
	if (!$access)
	{
		$hidden_iot = 'hidden';
	}
	//echo $access;
	
											/*	if (Bridge::defaults('admin'))
												{
													$query = $db->query("SELECT *,id as group_id FROM sites WHERE enabled = 1");
												}
												else
												{													
													$query = $db->query("SELECT * FROM users_groups WHERE user_id = '".$mybb->user['uid']."'");
												}
												while ($row = $db->fetch_array($query))
												{
													$groupselect .= '<option value="'.$row['group_id'].'">'.(Bridge::sites_info($row['group_id'],'short_name')).'</option>';
												}
												*/
												
	
	//if($mybb->settings['iot_enabled'] == "1" AND iot_perms($iot_groups) AND in_array($fid, $forums))
	if($mybb->settings['iot_enabled'] == "1")
	{
		eval("\$iot = \"".$templates->get("inviteonlythreads")."\";");
    }

}

function iot_newthread_validate(&$dh)
{
    global $mybb,$posthandler,$forum,$session;

    /*if($mybb->input['inviteonlycheck'])
    {
		
        $dh->data['inviteonlycheck'] = $mybb->input['inviteonlycheck'];
		$dh->data['inviteonlyuids'] = $mybb->input['inviteonlyuids'];
    }*/
	
	if ($mybb->input['inviteonlyuids'])
	{
		$dh->data['inviteonlycheck'] = 1;
		$dh->data['inviteonlyuids'] = $mybb->input['inviteonlyuids'];
	}
} 




function iot_newthread_action(&$dh)
{
    global $mybb, $lang, $db;

	//if($mybb->input['inviteonlycheck'] == 1)
	//{
	
	$inviteonlyuids = $mybb->input['inviteonlyuids'];
	
	$lang->load("iot");
	
	if($inviteonlyuids == "")
	 {
	$dh->data['inviteonlycheck'] = 0;
		$dh->data['inviteonlyuids'] = '';
	 //error($lang->iot_no_uids);
	 }
	
	
	 
	 
	//}
	
	
	 
     //else{$db->query("UPDATE ".TABLE_PREFIX."threads SET inviteonlycheck=1 AND inviteonlyuids='".$db->escape_string(intval($mybb->input['inviteonlyuids']))."' WHERE tid='".$posthandler->tid."'");}
	 
	 
	 
	if($dh->action == 'thread' && $dh->method == 'insert')
	{	
		if ($dh->data['inviteonlyuids'])
		{
			foreach ($dh->data['inviteonlyuids'] as $iou)
			{
				$inviteou .= $iou . ',';
			}
			$dh->data['inviteonlyuids'] = rtrim($inviteou,',');
				
			$dh->thread_insert_data['inviteonlycheck'] = $db->escape_string($dh->data['inviteonlycheck']);
			$dh->thread_insert_data['inviteonlyuids'] = $db->escape_string($dh->data['inviteonlyuids']);
		}
	}	 		
}

//OP is editing his thread

function iot_editpost_display()
{
    global $mybb, $templates, $iot, $db, $existinguids, $checked, $post, $lang;
	
	$lang->load("iot");
	
	$fid = intval($post['fid']);
	$forums = explode(",", $mybb->settings['iot_fid']);
	$iot_groups = $mybb->settings['iot_usergroup'];
	
	$query = $db->query("SELECT tid, inviteonlycheck, inviteonlyuids, firstpost FROM ".TABLE_PREFIX."threads where tid='".$post['tid']."'");
	
	while($do = $db->fetch_array($query))
	{
	$existinguids = $do['inviteonlyuids'];
	$GLOBALS['inviteonlyuids'] = $existinguids;
	
	if($do['inviteonlycheck'] == 1)
	{
	$checked = 'checked="checked"';
	}
	$firstpost = $do['firstpost'];
	}

	
			$groupselect .= '<select class="multiselect" id="inviteonlyuids" name="inviteonlyuids[]" multiple="multiple">';
												
												if (Bridge::defaults('admin'))
												{
													$query = $db->query("SELECT *,id as group_id FROM sites WHERE enabled = 1");
												}
												else
												{													
													$query = $db->query("SELECT * FROM users_groups WHERE user_id = '".$mybb->user['uid']."'");
												}
												while ($row = $db->fetch_array($query))
												{
												
													$groups = explode(',',$existinguids);	
													foreach ($groups as $val)
													{
														
														if ($val == $row['group_id'])
														{															
															$selected = 'selected="selected"';
														}
														else
														{
															$selected = '';
														}	
													}
													
													$groupselect .= '<option '.$selected.' value="'.$row['group_id'].'">'.(Bridge::sites_info($row['group_id'],'short_name')).'</option>';
												}
												
												$groupselect .= '</select>';
												$groupselect .= "<script type=\"text/javascript\">
												$('#inviteonlyuids').multiSelect({
									selectAll: false,
									noneSelected: 'All Research Sites',
									oneOrMoreSelected: '% Research Sites'
								});
							
								</script>";
	
	if($mybb->settings['iot_enabled'] == "1" AND $post['uid'] == $mybb->user['uid'] || is_moderator($fid) AND $firstpost == $post['pid'])
	{
	eval("\$iot = \"".$templates->get("inviteonlythreads")."\";");
    }

}	

function iot_editpost_action()
{
    global $mybb, $lang, $thread, $db, $post;
	
	$val = $mybb->input['inviteonlyuids'];
	$tid = $thread['tid'];
	
	if($mybb->input['inviteonlycheck'] == 1)
	{
	$lang->load("iot");
	
		if($val == "")
		{
		//echo 'lol';
		//error($lang->iot_no_uids);
		$db->query("UPDATE ".TABLE_PREFIX."threads SET inviteonlycheck=0, inviteonlyuids='".$db->escape_string($val)."' WHERE tid='".$post['tid']."'");
		}
		else
		{
			foreach ($val as $iou)
			{
				$inviteou .= $iou . ',';
			}
			$val = rtrim($inviteou,',');
			$db->query("UPDATE ".TABLE_PREFIX."threads SET inviteonlycheck=1, inviteonlyuids='".$db->escape_string($val)."' WHERE tid='".$post['tid']."'");	 
		}	 
	} 
	
	if($mybb->input['inviteonlycheck'] == 0)
	{
	
	$db->query("UPDATE ".TABLE_PREFIX."threads SET inviteonlycheck=0, inviteonlyuids='".$db->escape_string($val)."' WHERE tid='".$post['tid']."'");
	}
			
}

//We now have completed major tasking, time for action

function iot_check()
{
global $mybb, $lang, $thread;

$tid = $thread['tid'];
$checkit = $thread['inviteonlycheck'];
$check_uids = $thread['inviteonlyuids'];
	
$uids_list = explode(",", $check_uids);
$iot_groups = $mybb->settings['iot_usergroup'];
$fid = $thread['fid'];
$forums = explode(",", $mybb->settings['iot_fid']);

// My Groups
	$groups = Bridge::users_groups($mybb->user['uid']);	
	$groups = explode(',',$groups);	
	$access = 0;
	
foreach ($groups as $val)
{	
	if ($val)
	{
		foreach ($uids_list as $key)
		{
			if ($key == $val)
			{
				$access = 1;
			}
		}
	}
}




												foreach ($uids_list as $key)
												{
													$fileSites .= '<a href="/sites/?id='.$key.'">'.Bridge::sites_info($key,'short_name').'</a>, ';
												}
												
												$fileSites = rtrim($fileSites,', ');
												if ($checkit == 0)
												{
													$accessSite = 'All Research Sites';
												}
												else
												if ($checkit == 1)
												{
													$accessSite = $fileSites;
												}




if($mybb->settings['iot_enabled'] == "1")
{
//$thread['sites'] = '<div class="post classic"><div class="post_content" style="width:100%;text-align:center;"><i><b>This thread is shared with members of:</b></i><br /> ' . $accessSite .'</div></div>';

if($checkit == "1" && !($access) && (!$thread['uid'] == $mybb->user['uid'] || !is_moderator($fid)))
{
$lang->load("iot");

error($lang->iot_thread_error);
}
}

}

function iot_reply_check()
{
global $mybb, $lang, $thread;

$tid = $thread['tid'];
$checkit = $thread['inviteonlycheck'];
$check_uids = $thread['inviteonlyuids'];
$uids_list = explode(",", $check_uids);
$iot_groups = $mybb->settings['iot_usergroup'];
$fid = $thread['fid'];
$forums = explode(",", $mybb->settings['iot_fid']);


// My Groups
	$groups = Bridge::users_groups($mybb->user['uid']);	
	$groups = explode(',',$groups);	
	$access = 0;
	
foreach ($groups as $val)
{	
	if ($val)
	{
		foreach ($uids_list as $key)
		{
			if ($key == $val)
			{
				$access = 1;
			}
		}
	}
}


if($mybb->settings['iot_enabled'] == "1" && $checkit == "1" && !($access) && (!$thread['uid'] == $mybb->user['uid'] || !is_moderator($fid)))
{
$lang->load("iot");

error($lang->iot_thread_error);
}

}		
	
?>