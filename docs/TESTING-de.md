# Builder Shortcode Extras 1.2.0 testen

## Installation

1. PHP 8.0+ und WordPress 6.7+ prüfen.
2. Das Test-ZIP in einer Testwebsite hochladen und das vorhandene Plugin ersetzen.
3. Werkzeuge → Builder Shortcode Extras öffnen.
4. Einstellungen werden von BSE nicht gespeichert; vorhandene Shortcodes bleiben erhalten.

## Aufgaben

- Copyright mit erstem Jahr erzeugen, kopieren und auf einer Testseite ausgeben.
- Veröffentlichungsdatum ohne ID und mit der ID eines anderen Beitrags vergleichen.
- Änderungsdatum mit relativer und absoluter Ausgabe testen.
- Einen Link mit Beitrags-ID und benutzerdefiniertem Linktext erzeugen.
- Einen veröffentlichten Beitrag und eine synchronisierte Vorlage einbetten.
- Mit „Datum“, „Autor“, „Copyright“ und einem Attributnamen in der Hilfe suchen.
- Zwei CSS-Klassen einsetzen; beide müssen erhalten bleiben.
- Kopierfunktion, manuelle Kopiermöglichkeit, Tastaturbedienung und kleinen Bildschirm prüfen.
- Changelog-Dialog öffnen und mit Escape schließen.
- deckerweb-Tab unter Plugins → Installieren und Library-Ausblendung prüfen.
- Optional: Elementor Free, Elementor CSS, Beaver-Inhalte, Astra Pro und Genesis auf lizenzierten Installationen prüfen.

## Automatisierte Regressionen

Nur in einer separaten, wegwerfbaren WordPress-Installation ausführen; die Tests legen Inhalte an und löschen diese wieder.

```sh
BSE_WP_TEST_ROOT=/absolute/path/to/test-wordpress php tests/regression.php
BSE_WP_TEST_ROOT=/absolute/path/to/test-wordpress php tests/admin-context.php
BSE_WP_TEST_ROOT=/absolute/path/to/test-wordpress BSE_TEST_ADMIN_CONTEXT=network php tests/admin-context.php
BSE_WP_TEST_ROOT=/absolute/path/to/test-wordpress php tests/builder-adapters.php
BSE_WP_TEST_ROOT=/absolute/path/to/test-wordpress php tests/updater.php
```

Die Builder-Tests verwenden repräsentative Adapter, keine kommerziellen Builder-Pakete. Netzwerk-Admin-Tests prüfen den Anfragekontext; sie ersetzen keinen vollständigen Multisite-End-to-End-Test. Der Updater wird mit lokalen Release-/Paketdaten getestet; ein echtes Fernupdate ist für RC1 nicht erfolgt.

## Grafikentscheidung

Drei Entwürfe sind separat beigefügt. Variante A mit dem violetten Hintergrund von B ist ausgewählt. Alle Varianten enthalten SVG, PNG-Icons und englische/deutsche Banner. Die Entscheidung verändert die Plugin-Funktionen nicht.
