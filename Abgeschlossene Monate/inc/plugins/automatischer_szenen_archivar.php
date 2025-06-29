<?php
/**
 * Automatischer Szenen-Archivar v1.1
 *
 * Reagiert auf Änderungen im Inplay-Kalender und schlägt die Archivierung von Szenen aus entfernten Monaten vor.
 * Diese Version beinhaltet eine eigene Sprachdatei und eine dedizierte Einstellung für das Zielarchiv.
 */

// Sicherheitsprüfung
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// HOOKS
$plugins->add_hook("admin_config_settings_change_commit", "automatischer_szenen_archivar_check_calendar_change");
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
        "version"       => "1.0",
        "guid"          => "",
        "compatibility" => "18*"
    );
}

function automatischer_szenen_archivar_install()
{
    global $db, $lang;
    $lang->load('automatischer_szenen_archivar');

    // KORREKTUR: Alle Texte aus der Sprachdatei werden nun mit $db->escape_string() für die Datenbank maskiert.
    $settinggroup = array(
        'name'          => 'archivarsettings',
        'title'         => $db->escape_string($lang->archivar_settings_title),
        'description'   => $db->escape_string($lang->archivar_settings_description),
        'disporder'     => 50,
        'isdefault'     => 0
    );
    $gid = $db->insert_query("settinggroups", $settinggroup);

    $settings = array(
        'archivar_enabled' => array(
            'title'         => $db->escape_string($lang->archivar_enabled),
            'description'   => $db->escape_string($lang->archivar_enabled_desc),
            'optionscode'   => 'yesno',
            'value'         => 1,
            'disporder'     => 1
        ),
        'archivar_completed_months_fid' => array(
            'title'         => $db->escape_string($lang->archivar_completed_months_fid),
            'description'   => $db->escape_string($lang->archivar_completed_months_fid_desc),
            'optionscode'   => 'forumselectsingle',
            'value'         => 0,
            'disporder'     => 2
        ),
        // NEUE EINSTELLUNG
        'archivar_inplay_forums' => array(
            'title'         => $db->escape_string($lang->archivar_inplay_forums),
            'description'   => $db->escape_string($lang->archivar_inplay_forums_desc),
            'optionscode'   => 'forumselect',
            'value'         => '',
            'disporder'     => 3
        )
    );
    
    foreach($settings as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;
        $db->insert_query('settings', $setting);
    }

    rebuild_settings();
}

function automatischer_szenen_archivar_uninstall()
{
    global $db;
    $db->delete_query('settinggroups', "name='archivarsettings'");
    rebuild_settings();
}

function automatischer_szenen_archivar_is_installed()
{
    global $db;
    // Wir prüfen, ob unsere Einstellungsgruppe existiert. Das ist ein eindeutiges Zeichen.
    $query = $db->simple_select("settinggroups", "gid", "name = 'archivarsettings'");
    return $db->num_rows($query) > 0;
}

function automatischer_szenen_archivar_activate()
{
    global $db;
    $template = array(
        "title"		=> 'archivar_confirmation_page',
        "template"	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->archivar_page_title}</title>
{$headerinclude}
</head>
<body>
{$header}
<div class="container">
	<form action="index.php?module=tools-archivar&action=do_move" method="post">
		<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
		<input type="hidden" name="tids" value="{$tids_string}" />
		
		<div class="modal">
			<div style="text-align: center;">
				<h2>{$page_title}</h2>
				<p>{$page_p1}</p>
				<p>{$page_p2}</p>
				
				<div style="margin: 20px auto; width: 80%; text-align: left; border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;">
					<ul>
						{$thread_list}
					</ul>
				</div>
				
				<div class="bottom_buttons">
					<input type="submit" class="button" value="{$lang->archivar_confirm_button_yes}" />
					<a href="index.php" class="button">{$lang->archivar_confirm_button_no}</a>
				</div>
			</div>
		</div>
	</form>
</div>
{$footer}
</body>
</html>'),
        "sid"		=> "-1",
        "version"	=> "1.0",
        "dateline"	=> TIME_NOW
    );
    $query = $db->simple_select("templates", "tid", "title='archivar_confirmation_page'");
    if($db->num_rows($query) == 0) {
        $db->insert_query("templates", $template);
    }
}

function automatischer_szenen_archivar_deactivate()
{
    // Die Deaktivierung lässt das Template jetzt absichtlich bestehen, um Fehler zu vermeiden.
    // Es wird bei der Deinstallation entfernt.
}

// CORE FUNCTIONS
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
    // KORREKTUR: Wir geben jetzt explizit an, dass die Logik in dieser Datei liegt.
    $actions['archivar'] = array(
        'active' => 'archivar', 
        'file'   => '' // Dies ist die korrekte, einfache Angabe.
    );
}

function automatischer_szenen_archivar_permissions(&$admin_permissions)
{
	global $lang;
	$lang->load("automatischer_szenen_archivar");
	$admin_permissions['archivar'] = $lang->archivar_module_name;
}

function automatischer_szenen_archivar_check_calendar_change()
{
    global $mybb, $db;

    if ($mybb->settings['archivar_enabled'] != 1) return;
    if ((int)$mybb->settings['archivar_completed_months_fid'] == 0) return;
    // NEU: Nicht ausführen, wenn keine Inplay-Bereiche ausgewählt wurden
    if (empty($mybb->settings['archivar_inplay_forums'])) return;


    if (isset($mybb->input['upsetting']['inplaykalender_months'])) {
        $old_months_string = $mybb->input['old_settings']['inplaykalender_months'];
        $new_months_string = $mybb->input['upsetting']['inplaykalender_months'];
        $old_months = array_map('trim', explode(',', $old_months_string));
        $new_months = array_map('trim', explode(',', $new_months_string));
        $removed_months = array_diff($old_months, $new_months);

        if (empty($removed_months)) return;

        $removed_month = reset($removed_months);
        
        // NEU: Hole die Liste der zu durchsuchenden Foren aus den Einstellungen
        $inplay_fids = $mybb->settings['archivar_inplay_forums'];
        
        // KORRIGIERTE ABFRAGE: Sucht jetzt nur noch in den ausgewählten Foren
        $query_string = "
            SELECT s.tid 
            FROM " . TABLE_PREFIX . "inplayscenes s
            LEFT JOIN " . TABLE_PREFIX . "threads t ON (s.tid = t.tid)
            WHERE s.date LIKE '%" . $db->escape_string($removed_month) . "%' 
              AND t.fid IN (" . $db->escape_string($inplay_fids) . ")
        ";
        
        $query = $db->query($query_string);
        $tids_to_move = [];
        while ($row = $db->fetch_array($query)) {
            $tids_to_move[] = (int)$row['tid'];
        }

        if (!empty($tids_to_move)) {
            $tids_string = implode(',', $tids_to_move);
            admin_redirect("index.php?module=tools-archivar&action=confirm&tids=" . $tids_string . "&month=" . urlencode($removed_month));
        }
    }
}

// ADMIN-CP SEITE
function automatischer_szenen_archivar_run()
{
    global $page, $mybb, $db, $lang, $templates;
    
    // Nur ausführen, wenn wir in unserem Modul sind
    if ($page->active_action != 'archivar') {
        return;
    }

    $lang->load('automatischer_szenen_archivar');

    if ($mybb->input['action'] == 'confirm') {
        $page->add_breadcrumb_item($lang->archivar_module_name);
        $page->output_header($lang->archivar_page_title);

        $tids_string = $mybb->get_input('tids', MyBB::INPUT_STRING);
        $removed_month = htmlspecialchars_uni($mybb->get_input('month', MyBB::INPUT_STRING));
        $tids = explode(',', $tids_string);
        
        $thread_list = '';
        if (!empty($tids)) {
            $tids_escaped = implode("','", array_map('intval', $tids));
            $query = $db->query("SELECT tid, subject FROM " . TABLE_PREFIX . "threads WHERE tid IN ('" . $tids_escaped . "')");
            while ($thread = $db->fetch_array($query)) {
                $thread_link = $mybb->settings['bburl'] . "/" . get_thread_link($thread['tid']);
                $thread_list .= '<li><a href="' . $thread_link . '" target="_blank">' . htmlspecialchars_uni($thread['subject']) . '</a></li>';
            }
        }
        
        $page_title = $lang->sprintf($lang->archivar_confirm_title, $removed_month);
        $page_p1 = $lang->sprintf($lang->archivar_confirm_p1, $removed_month);
        $page_p2 = $lang->archivar_confirm_p2;

        eval("\$confirmation_page = \"" . $templates->get('archivar_confirmation_page') . "\";");
        echo $confirmation_page;
        $page->output_footer();
        exit;
    }

    if ($mybb->input['action'] == 'do_move') {
        verify_post_check($mybb->get_input('my_post_key'));
        $tids_string = $mybb->get_input('tids', MyBB::INPUT_STRING);
        if(empty($tids_string)) {
             flash_message($lang->archivar_error_no_tids, 'error');
             admin_redirect("index.php");
        }
        $tids = array_map('intval', explode(',', $tids_string));
        
        $archive_fid = (int)$mybb->settings['archivar_completed_months_fid'];
        if ($archive_fid == 0) {
            flash_message($lang->archivar_error_no_fid, 'error');
            admin_redirect("index.php");
        }

        require_once MYBB_ROOT . "inc/class_moderation.php";
        $moderation = new Moderation();
        $moderation->move_threads($tids, $archive_fid, 'move');
        
        log_admin_action(count($tids) . ' Szenen archiviert.', $lang->archivar_title);

        flash_message($lang->sprintf($lang->archivar_success_moved, count($tids)), 'success');
        admin_redirect("index.php");
        exit;
    }

    if (!$mybb->input['action']) {
        $page->add_breadcrumb_item($lang->archivar_module_name);
        $page->output_header($lang->archivar_title);
        $page->output_nav_tabs(array('archivar' => $lang->archivar_module_name));
        
        $table = new Table;
        $table->construct_cell($lang->archivar_info_text_p1 . "<br /><br />" . $lang->archivar_info_text_p2);
        $table->construct_row();
        $table->output($lang->archivar_title);
        
        $page->output_footer();
        exit;
    }
}
?>