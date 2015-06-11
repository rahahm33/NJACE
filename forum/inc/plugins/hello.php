<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/Bridge.php';
error_reporting(E_ALL);
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

/*$plugins->add_hook("pre_output_page", "hello_world");
*/
$plugins->add_hook("postbit", "resourcelink_redir");
$plugins->add_hook("newthread_start", "resourcelink");
$plugins->add_hook("postbit", "postbit_sites");
$plugins->add_hook("member_profile_end", "profile_sites");
$plugins->add_hook("global_start", "main_menu");
$plugins->add_hook("global_end", "main_menu");
$plugins->add_hook("forumdisplay_start", "threadinfid");



//print_r2(get_declared_classes());
$plugins->add_hook("index_start", "indexredir");

function resourcelink_redir()
{
	global $mybb,$thread,$post;
	if (!$mybb->input['mod'] == 1)
	{
		if ($thread['firstpost'] == $post['pid'])
		{
			if(filter_var($post['message'], FILTER_VALIDATE_URL)){ 
				header('Location: '.$post['message'].'');
				exit;
				//redirect($post['message'],'Redirecting to '.$thread['subject']);
			}
		}
	}
}

function resourcelink(&$forum)
{
	global $mybb, $templates, $lang, $forum, $db;
	$forum['resourcelink'] = '<tr>
<td class="trow2" width="20%"><strong>Resource Link</strong></td>
<td class="trow2"><input type="text" class="textbox" id="resourcelink" name="link" size="40" maxlength="255" value="" tabindex="1" /><div style="display:inline-block;padding-left:15px;" id="linkcheck"></div></td>
</tr>

<script type="text/javascript">

$(\'#resourcelink\').keyup(function() {
								//if ( ) {
								if (validURL($(this).val()) || (!($(this).val())) ) {	
									if (validURL($(this).val()) && (($(this).val())) ) {	
										$(\'#linkcheck\').html(\'<i class="glyphicon glyphicon-ok"></i>\');
										//$(\'.button\').removeAttr(\'disabled\');
									}
									else
									{
										$(\'.sceditor-container\').show();
									}
								} else {									
									
									$(\'#linkcheck\').html(\'<i class="glyphicon glyphicon-remove"></i>\');
									$(\'.sceditor-container\').hide();
									//$(\'.button\').attr(\'disabled\',\'disabled\');
								}						
							});
</script>';
	
}


function indexredir()
{
	header('Location:'.Bridge::defaults('url').'resources');
}

function threadinfid()
{
	global $mybb,$forum;
	if (!$mybb->input['mod'] == 1)
	{
		if ($mybb->input['fid'] == 2)
		{
			header('Location:'.Bridge::defaults('url').'resources');
		}
		else
		{
			//echo $mybb->input['fid'];
			//exit;
			header('Location:'.Bridge::defaults('url').'resources/'.$mybb->input['fid'].'');
		}
	}
}
	
function hello_info()
{
	/**
	 * Array of information about the plugin.
	 * name: The name of the plugin
	 * description: Description of what the plugin does
	 * website: The website the plugin is maintained at (Optional)
	 * author: The name of the author of the plugin
	 * authorsite: The URL to the website of the author (Optional)
	 * version: The version number of the plugin
	 * guid: Unique ID issued by the MyBB Mods site for version checking
	 * compatibility: A CSV list of MyBB versions supported. Ex, "121,123", "12*". Wildcards supported.
	 */
	return array(
		"name"			=> "Hello World!",
		"description"	=> "A sample plugin that prints hello world and prepends the content of each post to 'Hello world!'",
		"website"		=> "http://www.mybb.com",
		"author"		=> "MyBB Group",
		"authorsite"	=> "http://www.mybb.com",
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}

/**
 * ADDITIONAL PLUGIN INSTALL/UNINSTALL ROUTINES
 *
 * _install():
 *   Called whenever a plugin is installed by clicking the "Install" button in the plugin manager.
 *   If no install routine exists, the install button is not shown and it assumed any work will be
 *   performed in the _activate() routine.
 *
 * function hello_install()
 * {
 * }
 *
 * _is_installed():
 *   Called on the plugin management page to establish if a plugin is already installed or not.
 *   This should return TRUE if the plugin is installed (by checking tables, fields etc) or FALSE
 *   if the plugin is not installed.
 *
 * function hello_is_installed()
 * {
 *		global $db;
 *		if($db->table_exists("hello_world"))
 *  	{
 *  		return true;
 *		}
 *		return false;
 * }
 *
 * _uninstall():
 *    Called whenever a plugin is to be uninstalled. This should remove ALL traces of the plugin
 *    from the installation (tables etc). If it does not exist, uninstall button is not shown.
 *
 * function hello_uninstall()
 * {
 * }
 *
 * _activate():
 *    Called whenever a plugin is activated via the Admin CP. This should essentially make a plugin
 *    "visible" by adding templates/template changes, language changes etc.
 *
 * function hello_activate()
 * {
 * }
 *
 * _deactivate():
 *    Called whenever a plugin is deactivated. This should essentially "hide" the plugin from view
 *    by removing templates/template changes etc. It should not, however, remove any information
 *    such as tables, fields etc - that should be handled by an _uninstall routine. When a plugin is
 *    uninstalled, this routine will also be called before _uninstall() if the plugin is active.
 *
 * function hello_deactivate()
 * {
 * }
 */
/*ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ALL);
	*/

function main_menu()
{
	
	global $templates, $member_current_act, $db, $mybb, $f3;
	$vars['url'] = $mybb->settings['homeurl'];
	$vars['title'] = $mybb->settings['homename'];
	if ($mybb->user['uid'])
	{
		$vars['member'] = 1;
		$vars['mybb']['username'] = $mybb->user['username'];
		if ( ($mybb->usergroup['cancp']) || ($mybb->usergroup['canmodcp']) )
		{
			$vars['usermenu'] = Bridge::menu('user',1);
			$vars['resourcesmenu'] = Bridge::menu('resources');
			$vars['sitesmenu'] = Bridge::menu('sites');
		}
		else
		{
			$vars['usermenu'] = Bridge::menu('user');
			$vars['resourcesmenu'] = Bridge::menu('resources');
			$vars['sitesmenu'] = Bridge::menu('sites');
		}
	}
	else
	{
		$vars['member'] = 0;
	}
	$mybb->user['headinclude'] = Bridge::loadView( $_SERVER['DOCUMENT_ROOT'] . '/views/headinclude.html',$vars);
	$mybb->user['header'] = Bridge::loadView( $_SERVER['DOCUMENT_ROOT'] . '/views/header.html',$vars);
	$mybb->user['footer'] = Bridge::loadView( $_SERVER['DOCUMENT_ROOT'] . '/views/footer.html',$vars);
}

function profile_sites()
{
	global $templates, $member_current_act, $db, $memprofile;
	
	
	$groups = Bridge::users_groups($memprofile['uid']);
	$groups = explode(',',$groups);
	foreach ($groups as $gid)
	{
		if (!empty($gid))
		{
			$memprofile['rsites'] .= '<tr><td class="trow1"><a style="font-weight:bold" href="/sites/?id='.$gid.'">'.Bridge::sites_info($gid,'short_name') . '</a></td><td class="trow1"><span class="float_left">'. Bridge::users_role($memprofile['uid'],$gid) .'</span></td></tr>';
		}
	}
	if (!empty($memprofile['rsites']))
	{
		$memprofile['rsites'] = '<tr>
					<td colspan="2" class="thead"><strong>Research Sites</strong></td>
				</tr>' . $memprofile['rsites'];
	}
	
	
}

function postbit_sites(&$post)
{

	$groups = Bridge::users_groups($post['uid']);
	$groups = explode(',',$groups);
	foreach ($groups as $gid)
	{
		if (!empty($gid))
		{
			$site .= '<a href="/sites/?id='.$gid.'">'.Bridge::sites_info($gid,'short_name') . '</a><br />';
		}
	}
	if (!empty($site))
	{
		$post['user_details'] = $post['user_details'] . '<br /><br /><div style="padding:5px;background:#eee;color:#333;"><b>Research Sites:</b></div><br />' .$site;
	}
	if (stripos($post['onlinestatus'],'online'))
	{
		$post['onlinestatus'] = '<span style="padding-left:6px;font-size:11px;font-weight:bold;color:#11B422">Online</span>';
	}
	else
	{
		$post['onlinestatus'] = '<span style="padding-left:6px;font-size:11px;font-weight:bold;color:red">Offline</span>';
	}
	
}
