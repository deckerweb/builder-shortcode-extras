# Builder Shortcode Extras

![Builder Shortcode Extras](assets-github/banner-de.png)

**Kleine dynamische Helfer. Schnell eingesetzt.**

Copyright, Datumsangaben, Links und wiederverwendbare Inhalte ohne eigenes PHP. Mit einer durchsuchbaren Hilfe und einem kompakten Generator direkt in WordPress.

Version **1.2.1** · WordPress **6.7+** · PHP **8.0+** · GPL-2.0-or-later

[English](README.md) · [Anleitung und FAQ](https://github.com/deckerweb/builder-shortcode-extras/wiki/Deutsch) · [Download](https://github.com/deckerweb/builder-shortcode-extras/releases/latest)

## Inhalt

- [Auf einen Blick](#auf-einen-blick)
- [Installation und erste Schritte](#installation-und-erste-schritte)
- [Helfer für den Alltag](#helfer-für-den-alltag)
- [Beispiele](#beispiele)
- [Updates und Library](#updates-und-library)
- [Daten und Multisite](#daten-und-multisite)
- [FAQ](#faq)
- [Screenshots](#screenshots)
- [Changelog](#changelog)
- [Autor und Zweck](#autor-und-zweck)
- [Support und Sicherheit](#support-und-sicherheit)
- [Lizenz und Herkunft](#lizenz-und-herkunft)

## Auf einen Blick

- Durchsuchbare Shortcode-Hilfe direkt unter Werkzeuge → Builder Shortcode Extras.
- Fünf Generatorfälle: Copyright, Veröffentlichungs-/Änderungsdatum, Beitragslink und Inhaltseinbettung.
- Native Blöcke, synchronisierte Vorlagen und optionale Elementor-, Beaver-, Astra- und Genesis-Integrationen.
- Lokale Ressourcen, deutsche Du-/Sie-Texte und WordPress-Updates über GitHub.

## Installation und erste Schritte

1. Lade das Plugin-ZIP aus den GitHub-Releases herunter.
2. Öffne Plugins → Installieren → Plugin hochladen und aktiviere es.
3. Öffne Werkzeuge → Builder Shortcode Extras, wähle einen Helfer und kopiere den Shortcode.
4. Füge ihn in einen Shortcode-Block oder ein geeignetes Builder-Feld ein und prüfe die Ausgabe.

BSE speichert keine Generatorwerte und verändert keine Inhalte.

## Helfer für den Alltag

Copyright, Datumsangaben, Benutzerinformationen, Beitragslinks, Taxonomiebegriffe, Menüs und Kommentarformulare lassen sich gezielt einfügen. Viele Texthelfer unterstützen eigene CSS-Klassen und geprüfte HTML-Wrapper. Inhaltseinbettungen beachten Zugriffsrechte und unterbinden rekursive Verweise. Optionale Integrationen setzen den jeweiligen Builder beziehungsweise das Theme voraus.

## Beispiele

```text
[bse-copyright first="2019"]
[bse-post-date format="Y-m-d"]
[bse-post-modified-date label="Last updated: "]
[bse-post-link id="123" text="Read more"]
[bse-item-content id="123"]
```

## Updates und Library

Der deckerweb Updater 2.1.0 prüft stabile GitHub-Releases über die normale WordPress-Updateverwaltung. Er aktiviert keine automatischen Updates. Die eingebettete deckerweb Library 0.8.1 ergänzt einen optionalen Katalog unter Plugins → Installieren → deckerweb. Ihre Online-Katalogabfrage ist standardmäßig ausgeschaltet. Installation und Aktivierung sind getrennte Entscheidungen. GitHub sieht bei Metadaten- oder Paketabrufen die anfragende IP-Adresse; BSE übermittelt keine Nutzungsdaten, Website-Adresse oder Benutzerkennungen.

## Daten und Multisite

BSE kann je Website oder netzwerkweit aktiviert werden. Inhalts-IDs gelten für die aktuelle Website. Website-Administratoren verwenden Werkzeuge → Builder Shortcode Extras; Super-Administratoren zusätzlich Netzwerkeinstellungen → Builder Shortcode Extras. Die Library-Optionen gelten je Netzwerk. BSE speichert keine Einstellungen. Deaktivierung erhält Daten; Deinstallation bereinigt den eigenen Updatecache. Library-Daten bleiben erhalten, solange ein weiterer Host installiert ist. Beim letzten Host werden temporäre Daten bereinigt; Optionen bleiben standardmäßig erhalten. Inhalte und installierte Plugins werden nicht gelöscht.

## FAQ

### Muss ich das Plugin konfigurieren?

Nein. Öffne Werkzeuge → Builder Shortcode Extras, um einen Shortcode zu finden oder zu erstellen. Die Seite speichert keine Einstellungen.

### Wo füge ich einen Shortcode ein?

In einen WordPress-Shortcode-Block oder ein Builder-Feld mit ausdrücklicher Shortcode-Unterstützung. Beliebige Text-, URL- und HTML-Felder führen Shortcodes nicht automatisch aus.

### Was kann der Generator erstellen?

Copyright, Veröffentlichungsdatum, Änderungsdatum, einen Beitrags-/Seiten-Link und Inhaltseinbettungen. Weitere Helfer findest du in der durchsuchbaren Übersicht.

### Kann ich HTML und Klassen ändern?

Die meisten Texthelfer unterstützen wrapper, class, before und after. Mehrere Klassen sind möglich. Unsichere oder nicht unterstützte Wrapper werden durch span ersetzt. Diese Version ergänzt keinen Modus ohne Wrapper.

### Funktionieren synchronisierte Vorlagen mit Classic Editor?

bse-wpblock ist verfügbar, sobald WordPress den Inhaltstyp wp_block registriert. Das zusätzliche alte Blocks-Menü entfällt.

### Was passiert beim Entfernen mit den Daten?

BSE speichert keine Shortcode- oder Generator-Einstellungen. Deaktivierung erhält alle Daten. Deinstallation entfernt nur den temporären BSE-Updater-Cache und gibt die Library-Zuständigkeit frei. Andere installierte Library-Hosts schützen gemeinsame Daten, auch wenn sie deaktiviert sind. Der letzte Host entfernt temporäre Library-Daten; Optionen bleiben erhalten, außer die Löschoption der Library war aktiviert. Beiträge, Shortcodes, installierte Plugins und fremde Daten bleiben erhalten.

### Wie melde ich eine Sicherheitslücke?

Verwende Security → Advisories → Report a vulnerability im GitHub-Repository dieses Plugins. Nenne Plugin-, Library- und Umgebungsversionen und eine minimale Reproduktion ohne Zugangsdaten. Poste Sicherheitsdetails nicht in öffentlichen Issues. Siehe SECURITY.md.

[Alle Fragen nach Themen](https://github.com/deckerweb/builder-shortcode-extras/wiki/Deutsch#fragen-nach-themen).

## Screenshots

![Hilfe und Shortcode-Generator](assets-github/shortcode-tools-de.png)

## Changelog

## 1.2.1 — 08.10.2026

- **Verbessert:** Weniger Aufwand im Frontend: Update- und Katalogkomponenten laden nur bei Bedarf; wiederholte Abfragen des Website-Änderungsdatums verwenden einen gemeinsamen Cache pro Anfrage.
- **Verbessert:** Verständlichere lokale Hilfe, zugängliche Changelog-Kennzeichnung und abgestimmte englische, deutsche und formell deutsche Dokumentation.
- **Behoben:** Versions-Shortcodes geben keine beliebigen Installationskonstanten mehr aus; Menü-Shortcodes können keine beliebigen PHP-Callbacks aus Inhalten aufrufen.
- **Behoben:** Links, Datumsangaben und Kommentarformulare zu privaten Inhalten beachten Zugriffsrechte; Beschriftungen, Taxonomie-Trennzeichen und Menüformatierung werden sicher verarbeitet.
- **Behoben:** Netzwerkverwaltung verwendet passende Berechtigungen und Einstellungen-Links; gemeinsame Deinstallation schützt andere Plugins und gemeinsam genutzte Library-Daten.
- **Behoben:** Update-Pakete werden auf angebotene Version, Plugin-Identität und tatsächliche WordPress-/PHP-Anforderungen geprüft, auch bei Sammelupdates.
- **Sonstiges:** deckerweb Library 0.8.1 und Updater 2.1.0 eingebunden. Bestehende Shortcode-Namen, Attribute, Entwicklerfilter und optionale Integrationen bleiben verfügbar.
- **Sonstiges:** Zweisprachige Readmes, FAQ, Grafiken und vertraulicher Sicherheitsmeldeweg aktualisiert. Voraussetzungen bleiben WordPress 6.7+ und PHP 8.0+.

## 1.2.0 — 01.10.2026

- **Neu:** Durchsuchbare Shortcode-Hilfe mit Kopierfunktion und lokalem Generator für Copyright, Veröffentlichungsdatum, Änderungsdatum, Beitragslinks und Inhaltseinbettung.
- **Neu:** deckerweb Plugin Library 0.2.0 und gemeinsamer deckerweb GitHub Release Updater v2 eingebunden.
- **Verbessert:** Einheitliche Wrapper-Prüfung, mehrere CSS-Klassen, boolesche Attribute und bereinigte Textausgabe; Shortcode-Namen, Aliase und Ausgabefilter bleiben erhalten.
- **Verbessert:** Native Blöcke werden gerendert; Builder werden passend zum Inhalt gewählt; direkte und indirekte rekursive Einbettung wird verhindert.
- **Verbessert:** Lokaler Änderungsverlauf und Dokumentation sowie deutsche und formell deutsche Übersetzungen.
- **Behoben:** Admin-Registrierung, Rückgabe bereinigter Integrationsdaten und versehentlich registrierter Platzhalter korrigiert.
- **Behoben:** Veröffentlichungsdatum berücksichtigt die Beitrags-ID; Slug-Links werden korrekt aufgelöst; ungültige IDs und unbekannte Zählstatus werden behandelt.
- **Behoben:** Nicht definierte Versionskonstanten, Datenbankversionsabfrage sowie Escaping von Benutzerwerten, Ersatztext, Datumsbeschriftungen, Linktext und Tooltip korrigiert.
- **Behoben:** Website-Änderungsdatum verwendet die WordPress-Zeitzone und liefert bei fehlenden veröffentlichten Inhalten kein falsches Datum.
- **Sonstiges:** Benötigt WordPress 6.7+ und PHP 8.0+; PHP 8.0 wird von der Library vorausgesetzt. Die Helfer-Oberfläche lädt keine Frontend-Ressourcen.
- **Sonstiges:** Genesis und Beaver bleiben erhalten; doppelter Astra-Loader, altes Blocks-Menü und sachfremde Werbe-/Core-Admin-Eingriffe entfernt.

## 1.1.0 — 2025-03-15

- **Verbessert:** Shortcode-Kompatibilität wiederhergestellt.
- **Sonstiges:** Verteilung von WordPress.org zu GitHub verlagert und alten DDWlib-Empfehlungsinstaller entfernt.
- **Sonstiges:** Eigenen Übersetzungsloader entfernt; mitgelieferte Übersetzungen beibehalten.

## 1.0.0 — 2019-09-10

- **Neu:** Erste Veröffentlichung mit 25 allgemeinen Helfern und 5 Integrations-Shortcodes.

## 0.9.0 — 2019-09-09

- **Neu:** Erste öffentliche Beta auf GitHub.

## Autor und Zweck

David Decker – DECKERWEB entwickelt und veröffentlicht dieses Plugin seit 2019. Es konzentriert sich auf kleine dynamische Helfer und wird direkt über GitHub verteilt. Die Builder-Integrationen sind optional; eine allgemeine Zertifizierung kommerzieller Builder wird nicht behauptet.

## Support und Sicherheit

[Issues](https://github.com/deckerweb/builder-shortcode-extras/issues) · [Discussions](https://github.com/deckerweb/builder-shortcode-extras/discussions) · [Security](https://github.com/deckerweb/builder-shortcode-extras/security/advisories/new)

Sicherheitslücken bitte vertraulich melden. [Meldeweg](SECURITY.md). Unterstützung: [Ko-fi](https://ko-fi.com/deckerweb) · [Buy Me a Coffee](https://buymeacoffee.com/daveshine) · [PayPal](https://paypal.me/deckerweb).

## Lizenz und Herkunft

© 2019–2026 David Decker – DECKERWEB. [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

Mitgelieferte deckerweb Library und Updater: David Decker – DECKERWEB, GPL-2.0-or-later. Die BSE-Grafiken sind originale Vektorarbeiten; es werden keine Schriftdateien mitgeliefert. WordPress- und Builder-Namen gehören ihren jeweiligen Rechteinhabern.
