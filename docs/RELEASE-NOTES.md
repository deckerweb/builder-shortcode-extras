# Builder Shortcode Extras 1.2.0

## Deutsch

WordPress 6.7+ · PHP 8.0+

- **Neu:** Durchsuchbare Shortcode-Hilfe mit Kopierfunktion und lokalem Generator für Copyright, Veröffentlichungsdatum, Änderungsdatum, Beitragslinks und Inhaltseinbettung.
- **Neu:** deckerweb Plugin Library 0.2.0 und gemeinsamer deckerweb GitHub Release Updater v2 eingebunden.
- **Verbessert:** Einheitliche Wrapper-Prüfung, mehrere CSS-Klassen, boolesche Attribute und bereinigte Textausgabe; Shortcode-Namen, Aliase und Ausgabefilter bleiben erhalten.
- **Verbessert:** Native Blöcke werden gerendert; Builder werden passend zum Inhalt gewählt; direkte und indirekte rekursive Einbettung wird verhindert.
- **Verbessert:** Header/Footer nach Brand Admin Schemes, lokaler Changelog-Dialog, englische/deutsche Dokumentation und aktualisierte deutsche Übersetzungen.
- **Verbessert:** Drei originale skalierbare Icon-/Banner-Entwürfe; Variante A mit dem violetten Hintergrund von B ist ausgewählt.
- **Behoben:** Admin-Registrierung, Rückgabe bereinigter Integrationsdaten und versehentlich registrierter Platzhalter korrigiert.
- **Behoben:** Veröffentlichungsdatum berücksichtigt die Beitrags-ID; Slug-Links werden korrekt aufgelöst; ungültige IDs und unbekannte Zählstatus werden behandelt.
- **Behoben:** Nicht definierte Versionskonstanten, Datenbankversionsabfrage sowie Escaping von Benutzerwerten, Ersatztext, Datumsbeschriftungen, Linktext und Tooltip korrigiert.
- **Behoben:** Website-Änderungsdatum verwendet die WordPress-Zeitzone und liefert bei fehlenden veröffentlichten Inhalten kein falsches Datum.
- **Sonstiges:** Benötigt WordPress 6.7+ und PHP 8.0+; PHP 8.0 wird von der Library vorausgesetzt. Die Helfer-Oberfläche lädt keine Frontend-Ressourcen.
- **Sonstiges:** Genesis und Beaver bleiben erhalten; doppelter Astra-Loader, altes Blocks-Menü und sachfremde Werbe-/Core-Admin-Eingriffe entfernt.


## English

WordPress 6.7+ · PHP 8.0+

- **New:** Searchable shortcode help with copy buttons and a local generator for copyright, publication date, modification date, post links and embedded content.
- **New:** Embedded deckerweb Plugin Library 0.2.0 and shared deckerweb GitHub Release Updater v2.
- **Improved:** Unified wrapper validation, multiple CSS classes, boolean attributes and escaped text boundaries; existing shortcode names, aliases and output filters remain.
- **Improved:** Native block rendering, builder-specific routing and guarded content embedding with direct/indirect recursion protection.
- **Improved:** Brand Admin Schemes-style header/footer, local changelog dialog, English/German documentation and German/formal German translations.
- **Improved:** Three original scalable icon/banner concepts; selected concept A uses the purple background from concept B.
- **Fixed:** Corrected admin registration policy, persistent integration normalization and placeholder integration registration.
- **Fixed:** Publication dates honor explicit post IDs; slug links resolve correctly; missing IDs and unknown post-count statuses are handled.
- **Fixed:** Undefined version constants, database version lookup, user/fallback escaping, date labels, link text and tooltip escaping.
- **Fixed:** Site-updated dates use the WordPress timezone and return no false date when no published item exists.
- **Misc:** Requires WordPress 6.7+ and PHP 8.0+; the library requires PHP 8.0. No frontend assets are added by the helper UI.
- **Misc:** Retains Genesis and Beaver compatibility, removes the duplicate Astra loader, legacy Blocks menu and unrelated promotion/Core admin overrides.


[English guide & FAQ](https://github.com/deckerweb/builder-shortcode-extras/blob/master/docs/English.md) · [Deutsche Anleitung & FAQ](https://github.com/deckerweb/builder-shortcode-extras/blob/master/docs/Deutsch.md)
