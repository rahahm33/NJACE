<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

/*$plugins->add_hook("pre_output_page", "hello_world");
*/

$plugins->add_hook("postbit", "postbit_sites");
$plugins->add_hook("member_profile_end", "profile_sites");
$plugins->add_hook("global_start", "main_menu");
$plugins->add_hook("forumdisplay_start", "threadinfid");
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/core.php';


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

function threadinfid()
{
	global $mybb;
	echo 'lol';
}
	
function main_menu()
{
	
	global $templates, $member_current_act, $db, $mybb;
	$menu = new genmenu($pageName, $mybb);
	$mybb->user['mainmenu'] = $menu->buildMenu();
	
}

function profile_sites()
{
	global $templates, $member_current_act, $db, $memprofile;
	
	
	$groups = users_groups($memprofile['uid']);
	$groups = explode(',',$groups);
	foreach ($groups as $gid)
	{
		if (!empty($gid))
		{
			$memprofile['rsites'] .= '<tr><td class="trow1" colspan="2"><a href="/sites/?id='.$gid.'">'.sites_info($gid,'short_name') . '</a></td></tr>';
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

	$groups = users_groups($post['uid']);
	$groups = explode(',',$groups);
	foreach ($groups as $gid)
	{
		if (!empty($gid))
		{
			$site .= '<a href="/sites/?id='.$gid.'">'.sites_info($gid,'short_name') . '</a><br />';
		}
	}
	if (!empty($site))
	{
		$post['user_details'] = $post['user_details'] . '<br /><br /><div style="padding-bottom:5px;border-bottom:1px solid #eee"><b>Research Sites:</b></div><br />' .$site;
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
