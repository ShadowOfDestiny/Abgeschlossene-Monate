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
$l['archivar_page_title'] = "Automatischer Archivar - Bestätigung";
$l['archivar_page_header'] = "Automatischer Szenen-Archivar";
$l['archivar_info_text_p1'] = "Dieses Plugin hat keine eigene Konfigurationsseite. Es wird automatisch aktiv, wenn Du unter <strong>Konfiguration -> Einstellungen -> Inplay-Kalender</strong> einen Monat aus der Liste entfernst.";
$l['archivar_info_text_p2'] = "Stelle sicher, dass unter <strong>Konfiguration -> Plugins</strong> der 'Automatischer Szenen-Archivar' aktiviert ist und unter <strong>Konfiguration -> Einstellungen -> Automatischer Szenen-Archivar</strong> das korrekte 'Archiv für Abgeschlossene Monate' ausgewählt ist.";

// Bestätigungsseite
$l['archivar_confirm_title'] = "Szenen für den Monat '{1}' archivieren?";
$l['archivar_confirm_p1'] = "Der Monat <strong>{1}</strong> wurde aus dem Inplay-Kalender entfernt. Die folgenden Szenen spielen in diesem Monat und können nun in das Forum für abgeschlossene Monate verschoben werden.";
$l['archivar_confirm_p2'] = "Bitte überprüfe die Liste. Mit einem Klick auf \"Ja\" werden alle unten gelisteten Szenen unwiderruflich in das konfigurierte Archiv verschoben.";
$l['archivar_confirm_button_yes'] = "Ja, diese Szenen jetzt verschieben";
$l['archivar_confirm_button_no'] = "Nein, abbrechen";

// Erfolgs- und Fehlermeldungen
$l['archivar_success_moved'] = "{1} Szenen wurden erfolgreich in das Archiv für abgeschlossene Monate verschoben.";
$l['archivar_error_no_tids'] = "Fehler: Keine Thread-IDs zur Verschiebung übermittelt.";
$l['archivar_error_no_fid'] = "Fehler: Es wurde kein 'Archiv für abgeschlossene Monate' in den Plugin-Einstellungen konfiguriert.";

// Dies am Ende der Datei hinzufügen
$l['archivar_inplay_forums'] = "Zu durchsuchende Inplay-Bereiche";
$l['archivar_inplay_forums_desc'] = "Wähle die Foren aus, die das Plugin nach Szenen durchsuchen soll. Nur Szenen aus diesen Bereichen werden für die Verschiebung berücksichtigt. Wähle hier deine aktiven Inplay-Foren aus.";
?>