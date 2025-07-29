<?php
/**
 * Sprachdatei für den Automatischen Szenen-Archivar v1.1
 */

$l['archivar_title'] = "Automatischer Szenen-Archivar";

// Plugin-Informationen
$l['archivar_name'] = "Automatischer Szenen-Archivar";
$l['archivar_description'] = "Überwacht den Inplay-Kalender und automatisiert die Archivierung von Szenen, wenn ein Monat entfernt wird.";

// Einstellungen
$l['archivar_settings_title'] = "Automatischer Szenen-Archivar";
$l['archivar_settings_description'] = "Einstellungen für den Automatischen Szenen-Archivar.";
$l['archivar_enabled'] = "Archivierungs-Automatik aktivieren?";
$l['archivar_enabled_desc'] = "Wenn auf \"Ja\" gesetzt, wird das Plugin aktiv und überwacht den Inplay-Kalender.";
$l['archivar_completed_months_fid'] = "Archiv für 'Abgeschlossene Monate'";
$l['archivar_completed_months_fid_desc'] = "Wähle das Forum aus, in das Szenen verschoben werden sollen, deren Monat aus dem Kalender entfernt wurde. Dies ist ein Zwischenarchiv, um zu signalisieren, dass keine neuen Szenen eröffnet werden sollen.";

// Admin-CP Seite
$l['archivar_module_name'] = "Automatischer Archivar";
$l['archivar_page_header'] = "Automatischer Szenen-Archivar";
$l['archivar_info_text_p1'] = "Dieses Plugin hat keine eigene Konfigurationsseite. Es wird automatisch aktiv, wenn Du unter <strong>Konfiguration -> Einstellungen -> Inplay-Kalender</strong> einen Monat aus der Liste entfernst.";
$l['archivar_info_text_p2'] = "Stelle sicher, dass unter <strong>Konfiguration -> Plugins</strong> der 'Automatischer Szenen-Archivar' aktiviert ist und unter <strong>Konfiguration -> Einstellungen -> Automatischer Szenen-Archivar</strong> das korrekte 'Archiv für Abgeschlossene Monate' ausgewählt ist.";

// Erfolgs- und Fehlermeldungen
$l['archivar_success_moved'] = "{1} Szenen wurden erfolgreich in das Archiv für abgeschlossene Monate verschoben.";
$l['archivar_error_no_tids'] = "Fehler: Keine Thread-IDs zur Verschiebung übermittelt.";
$l['archivar_error_no_fid'] = "Fehler: Es wurde kein 'Archiv für abgeschlossene Monate' in den Plugin-Einstellungen konfiguriert.";

// Dies am Ende der Datei hinzufügen
$l['archivar_inplay_forums'] = "Zu durchsuchende Inplay-Bereiche";
$l['archivar_inplay_forums_desc'] = "Wähle die Foren aus, die das Plugin nach Szenen durchsuchen soll. Nur Szenen aus diesen Bereichen werden für die Verschiebung berücksichtigt. Wähle hier deine aktiven Inplay-Foren aus.";

// Neue Sprachvariablen für das Formular
$l['archivar_form_title'] = "Szenen für einen Monat archivieren";
$l['archivar_form_month_label'] = "Zu archivierender Monat";
$l['archivar_form_month_desc'] = "Gib den Namen oder die Nummer des Monats ein (z.B. 'Januar', '01' oder 'january').";

// Neue Sprachvariablen für Nachrichten
$l['archivar_error_invalid_month'] = "Ungültige Monatseingabe. Bitte versuche es erneut.";
$l['archivar_error_no_inplay_fids'] = "Es wurden keine Inplay-Foren in den Einstellungen festgelegt.";
$l['archivar_info_no_threads_found'] = "Es wurden keine zu archivierenden Szenen für den Monat '{1}' gefunden.";
$l['archivar_success_moved'] = "{1} Szenen für den Monat '{2}' wurden erfolgreich archiviert.";

// Bestehende anpassen (optional, aber empfohlen)
$l['archivar_error_no_fid'] = "Kein Archiv-Forum in den Einstellungen festgelegt. Bitte überprüfe die Plugin-Einstellungen.";

// Neue Sprachvariablen für die Bestätigungsseite
$l['archivar_confirm_breadcrumb'] = "Bestätigung";
$l['archivar_page_title'] = "Archivierung bestätigen";
$l['archivar_confirm_title'] = "Szenen für den Monat '{1}' archivieren?";
$l['archivar_confirm_p1'] = "Die folgenden Szenen wurden für den Monat <strong>{1}</strong> gefunden. Sie werden in das Archiv für abgeschlossene Monate verschoben.";
$l['archivar_confirm_threads_list'] = "Gefundene Szenen:";
$l['archivar_confirm_p2'] = "Bist du sicher, dass du diese Szenen verschieben möchtest?";
$l['archivar_confirm_button_yes'] = "Ja, verschieben";
$l['archivar_confirm_button_no'] = "Nein, abbrechen";

// Ändere den Text des Submit-Buttons im ursprünglichen Formular, damit es klarer ist
$l['archivar_form_submit_button'] = "Szenen suchen";

?>