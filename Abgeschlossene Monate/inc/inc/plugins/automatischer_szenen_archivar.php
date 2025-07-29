<?php
/**
 * Automatischer Szenen-Archivar v4.1 (Final - Vereinfacht & Stabil)
 */

// Sicherheitsprüfung
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// HOOKS
$plugins->add_hook("admin_tools_menu", "automatischer_szenen_archivar_menu");
$plugins->add_hook("admin_tools_action_handler", "automatischer_szenen_archivar_action_handler");
$plugins->add_hook("admin_tools_permissions", "automatischer_szenen_archivar_permissions");
$plugins->add_hook("admin_load", "automatischer_szenen_archivar_run");

function automatischer_szenen_archivar_info()
{
    global $lang;
    $lang->load('automatischer_szenen_archivar');
    return array(
        'name'          => $lang->archivar_name,
        'description'   => $lang->archivar_description,
        "website"       => "https://shadow.or.at/index.php",
        "author"        => "Dani",
        "authorsite"    => "https://github.com/ShadowOfDestiny",
        "version"       => "2.0",
        "guid"          => "",
        "compatibility" => "18*"
    );
}

function automatischer_szenen_archivar_install()
{
    global $db, $lang;
    $lang->load('automatischer_szenen_archivar');
	
    $query = $db->simple_select("settinggroups", "gid", "name = 'archivarsettings'");
    if($db->num_rows($query) == 0) {
        $settinggroup = array(
			'name' => 'archivarsettings', 
			'title' => $db->escape_string($lang->archivar_settings_title), 
			'description' => $db->escape_string($lang->archivar_settings_description), 
			'disporder' => 50, 
			'isdefault' => 0
		);
        $gid = $db->insert_query("settinggroups", $settinggroup);
		
        $settings = array(
            'archivar_enabled' => array(
				'title' => $db->escape_string($lang->archivar_enabled), 
				'description' => $db->escape_string($lang->archivar_enabled_desc), 
				'optionscode' => 'yesno', 
				'value' => 1, 
				'disporder' => 1
			),
			
            'archivar_completed_months_fid' => array(
				'title' => $db->escape_string($lang->archivar_completed_months_fid), 
				'description' => $db->escape_string($lang->archivar_completed_months_fid_desc), 
				'optionscode' => 'forumselectsingle', 
				'value' => 0, 
				'disporder' => 2
			),
			
            'archivar_inplay_forums' => array(
				'title' => $db->escape_string($lang->archivar_inplay_forums), 
				'description' => $db->escape_string($lang->archivar_inplay_forums_desc), 
				'optionscode' => 'forumselect', 
				'value' => '', 
				'disporder' => 3
			)
        );
		
        foreach($settings as $name => $setting) {
            $setting['name'] = $name; $setting['gid'] = $gid; $db->insert_query('settings', $setting);
        }
        rebuild_settings();
    }
}

function automatischer_szenen_archivar_uninstall()
{
    global $db;
    $db->delete_query('settinggroups', "name='archivarsettings'");
    rebuild_settings();
    $db->delete_query('templates', "title='archivar_confirmation_page'");
}

function automatischer_szenen_archivar_is_installed()
{
    global $db;
    $query = $db->simple_select("settinggroups", "gid", "name = 'archivarsettings'");
    return $db->num_rows($query) > 0;
}

function automatischer_szenen_archivar_activate()
{
    global $db;
    $template = array(
        "title"		=> 'archivar_confirmation_page',
        "template"	=> $db->escape_string('
	<html>
		<head>
			<title>{$mybb->settings[\'bbname\']} - {$lang->archivar_page_title}</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
				<div class="container">
					<form action="index.php?module=tools-archivar&action=execute_archive" method="post">
						<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
						<input type="hidden" name="tids" value="{$tids_string}" />
						<input type="hidden" name="month" value="{$month_input}" />
				<div class="modal">
				<div style="text-align: center;">
					<h2>{$page_title}</h2>
						<p>{$page_p1}</p>
						<p>{$page_p2}</p>
				<div style="margin: 20px auto; width: 80%; text-align: left; border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;">
					<ul>{$thread_list}</ul>
				</div>
				<div class="bottom_buttons">
					<input type="submit" class="button" value="{$lang->archivar_confirm_button_yes}" />
						<a href="index.php?module=tools-archivar" class="button">{$lang->archivar_confirm_button_no}</a>
				</div>
				</div>
				</div>
					</form>
				</div>
			{$footer}
		</body>
	</html>'
),
        "sid"		=> "-1", 
		"version"	=> "2.0", 
		"dateline"	=> TIME_NOW
    );
    $query = $db->simple_select("templates", "tid", "title='archivar_confirmation_page'");
    if($db->num_rows($query) > 0) {
        $tid = $db->fetch_field($query, "tid");
        $db->update_query("templates", array("template" => $template['template'], "version" => $template['version']), "tid='{$tid}'");
    } else {
        $db->insert_query("templates", $template);
    }
}

function automatischer_szenen_archivar_deactivate() {}

function automatischer_szenen_archivar_menu(&$sub_menu)
{
    global $lang;
    $lang->load('automatischer_szenen_archivar');
    $sub_menu['100'] = array(
		'id' => 'archivar', 
		'title' => $lang->archivar_module_name, 
		'link' => 'index.php?module=tools-archivar');
}

function automatischer_szenen_archivar_action_handler(&$actions)
{
    $actions['archivar'] = array(
		'active' => 'archivar', 
		'file'   => ''
	);
}

function automatischer_szenen_archivar_permissions(&$admin_permissions)
{
    global $lang;
    $lang->load("automatischer_szenen_archivar");
    $admin_permissions['archivar'] = $lang->archivar_module_name;
}

function archivar_normalize_month($input)
{
    $input = strtolower(trim($input));
    $months = [
		'01'=>'01','1'=>'01','januar'=>'01','january'=>'01',
		'02'=>'02','2'=>'02','februar'=>'02','february'=>'02',
		'03'=>'03','3'=>'03','märz'=>'03','marz'=>'03','march'=>'03',
		'04'=>'04','4'=>'04','april'=>'04',
		'05'=>'05','5'=>'05','mai'=>'05','may'=>'05',
		'06'=>'06','6'=>'06','juni'=>'06','june'=>'06',
		'07'=>'07','7'=>'07','juli'=>'07','july'=>'07',
		'08'=>'08','8'=>'08','august'=>'08',
		'09'=>'09','9'=>'09','september'=>'09',
		'10'=>'10','oktober'=>'10','october'=>'10',
		'11'=>'11','november'=>'11',
		'12'=>'12','dezember'=>'12','december'=>'12'];
    return isset($months[$input]) ? $months[$input] : false;
}

function automatischer_szenen_archivar_run()
{
    global $page, $mybb, $db, $lang, $templates;
    
    if ($page->active_action != 'archivar') return;

    $lang->load('automatischer_szenen_archivar');

    if ($mybb->input['action'] == 'find_threads') {
        verify_post_check($mybb->get_input('my_post_key'));
        $month_input = $mybb->get_input('month_to_archive');
        $month_number = archivar_normalize_month($month_input);
        if (!$month_number) {
            flash_message($lang->archivar_error_invalid_month, 'error');
            admin_redirect("index.php?module=tools-archivar");
        }
        $inplay_fids = $mybb->settings['archivar_inplay_forums'];
        if (empty($inplay_fids)) {
            flash_message($lang->archivar_error_no_inplay_fids, 'error');
            admin_redirect("index.php?module=tools-archivar");
        }
        $query = $db->query("SELECT s.tid FROM ".TABLE_PREFIX."inplayscenes s JOIN ".TABLE_PREFIX."threads t ON (s.tid = t.tid) WHERE s.date LIKE '%-".$db->escape_string($month_number)."-%' AND t.fid IN (".$db->escape_string($inplay_fids).")");
        $tids_to_move = [];
        while ($row = $db->fetch_array($query)) { $tids_to_move[] = (int)$row['tid']; }
        if (!empty($tids_to_move)) {
            admin_redirect("index.php?module=tools-archivar&action=confirm_archive&tids=".implode(',', $tids_to_move)."&month=".urlencode($month_input));
        } else {
            flash_message($lang->sprintf($lang->archivar_info_no_threads_found, htmlspecialchars_uni($month_input)), 'error');
            admin_redirect("index.php?module=tools-archivar");
        }
    }
    elseif ($mybb->input['action'] == 'confirm_archive') {
        $page->add_breadcrumb_item($lang->archivar_module_name, "index.php?module=tools-archivar");
        $page->add_breadcrumb_item($lang->archivar_confirm_breadcrumb);
        $page->output_header($lang->archivar_page_title);
        $tids_string = $mybb->get_input('tids', MyBB::INPUT_STRING);
        $month_input = htmlspecialchars_uni($mybb->get_input('month', MyBB::INPUT_STRING));
        $tids = explode(',', $tids_string);
        $thread_list = '';
        if (!empty($tids)) {
            $tids_escaped = "'".implode("','", array_map('intval', $tids))."'";
            $query = $db->query("SELECT tid, subject FROM ".TABLE_PREFIX."threads WHERE tid IN (".$tids_escaped.") ORDER BY subject ASC");
            while ($thread = $db->fetch_array($query)) {
                $thread_link = $mybb->settings['bburl']."/".get_thread_link($thread['tid']);
                $thread_list .= '<li><a href="'.$thread_link.'" target="_blank">'.htmlspecialchars_uni($thread['subject']).'</a></li>';
            }
        }
        $page_title = $lang->sprintf($lang->archivar_confirm_title, $month_input);
        $page_p1 = $lang->sprintf($lang->archivar_confirm_p1, $month_input);
        $page_p2 = $lang->archivar_confirm_p2;
        eval("\$confirmation_page = \"".$templates->get('archivar_confirmation_page')."\";");
        echo $confirmation_page;
        $page->output_footer();
    }
    elseif ($mybb->input['action'] == 'execute_archive') {
        verify_post_check($mybb->get_input('my_post_key'));
        $tids_string = $mybb->get_input('tids', MyBB::INPUT_STRING);
        $month_input = $mybb->get_input('month', MyBB::INPUT_STRING);
        $tids = array_map('intval', explode(',', $tids_string));
        $archive_fid = (int)$mybb->settings['archivar_completed_months_fid'];
        if ($archive_fid == 0) {
            flash_message($lang->archivar_error_no_fid, 'error');
            admin_redirect("index.php?module=tools-archivar");
        }
        if (!empty($tids)) {
            require_once MYBB_ROOT."inc/class_moderation.php";
            $moderation = new Moderation();
            $moderation->move_threads($tids, $archive_fid);
            log_admin_action(count($tids).' Szenen für Monat '.htmlspecialchars_uni($month_input).' archiviert.', $lang->archivar_title);
            flash_message($lang->sprintf($lang->archivar_success_moved, count($tids), htmlspecialchars_uni($month_input)), 'success');
        }
        admin_redirect("index.php?module=tools-archivar");
    }
    else {
        $page->add_breadcrumb_item($lang->archivar_module_name);
        $page->output_header($lang->archivar_title);
        $form = new Form("index.php?module=tools-archivar&action=find_threads", "post");
        $form_container = new FormContainer($lang->archivar_form_title);
        $form_container->output_row($lang->archivar_form_month_label, $lang->archivar_form_month_desc, $form->generate_text_box('month_to_archive', '', array('id' => 'month_to_archive')), 'month_to_archive');
        $form_container->end();
        $buttons[] = $form->generate_submit_button($lang->archivar_form_submit_button);
        $form->output_submit_wrapper($buttons);
        $form->end();
        $page->output_footer();
    }
}
?>