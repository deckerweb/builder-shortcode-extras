# Prüfung von 1.2.0

Stand: 01.10.2026. Die Prüfungen liefen in getrennten lokalen WordPress-Installationen mit SQLite und PHP 8.4.5.

- WordPress 6.7 und 7.1.2: jeweils 62 Regressionen bestanden, einschließlich ausgewählter Beitrags-IDs, relativer Datumsangaben, mehrerer CSS-Klassen, Ausgabegrenzen, nativer Blockausgabe, rekursiver Einbettung, Zugriffsschutz, Integrationsdaten und Generatorumfang.
- Beide WordPress-Versionen: getrennte Website-/Netzwerk-Admin-Kontexte, Opt-in, Zugriffsrechte und Admin-Seite geprüft.
- Beide WordPress-Versionen: repräsentative Elementor-/Beaver-Adapter, CSS-Flag und Wiederherstellung nach einer Adapterausnahme bestanden.
- Beide WordPress-Versionen: Updater-Metadaten, Plugin-Zuordnung, Grafiken, Release-Texte, Vorabversionen, fremde Paket-URLs, Paketidentität, Anforderungen und Versionsabgleich mit lokalen Testdaten bestanden.
- Browser: alle fünf Generatorfälle, Feldwechsel, validierte IDs, Beschriftungsabstände, Kopieren, lokale Dokumentation, Suche, Changelog, Escape und Fokus geprüft. Keine JavaScript-Laufzeitfehler.
- Englisch, Deutsch und formelles Deutsch geprüft; deutsche Aufgabenbegriffe sind suchbar. Mobile Darstellung ohne horizontales Überlaufen. Beispiele sind ohne JavaScript lesbar.
- PHP-Syntax und JavaScript-Syntax bestanden. Deutsche PO-Dateien mit msgfmt geprüft; POT aktualisiert.
- Die eingebettete Library 0.2.0 und der gemeinsame Updater v2 wurden unverändert aus den lokalen deckerweb-Referenzen übernommen. Der Adapter ist pluginspezifisch.

## Grenzen

PHP 8.0 wurde in dieser Sitzung nicht als Laufzeit ausgeführt; die Library setzt PHP 8.0 voraus. Der vorbereitete GitHub-Workflow prüft Syntax unter PHP 8.0 und 8.4, ist aber noch nicht auf GitHub gelaufen. Die Datenbanktests ersetzen keine MySQL-/MariaDB-Matrix. Kommerzielle Elementor-, Beaver-, Astra- und Genesis-Installationen sowie ein vollständiges Multisite-Netzwerk wurden nicht ausgeführt. Ein echtes Fernupdate und die Installation anderer Library-Plugins gehören nicht zu diesen BSE-Tests.

Die ZIP-Installation und Aktivierung über WordPress Plugin_Upgrader in einer frischen WordPress-6.7-Umgebung bestanden. Auch aus dem installierten ZIP bestanden alle 62 Regressionen.

## Freigegebenes Release 1.2.0

Nach der Freigabe wurden PHP-/JavaScript-Syntax, die jeweils 62 Regressionen, Website-/Netzwerk-Admin-Kontexte, Builder-Adapter und Updater-Prüfungen mit Version 1.2.0 erneut erfolgreich ausgeführt. Die Release-Dokumentationsprüfung kontrolliert Versionsstände, übersetzte deutsche Changelog-Prefixe, zweisprachige FAQ und synchrone Wiki-Inhalte. Die früheren Browser-Prüfungen gelten für die vom Autor getestete Oberfläche; für die Versionierung und Dokumentationskorrekturen wurde kein erneuter vollständiger Browserlauf benötigt.

Das fertige 1.2.0-ZIP wurde erneut über WordPress Plugin_Upgrader installiert und aktiviert; anschließend bestanden alle 62 Regressionen aus dem installierten Paket. Der lokale Changelog wurde zusätzlich unter de_DE und de_DE_formal auf alle vier deutschen Prefixe und auf das Fehlen englischer Prefixe geprüft.
