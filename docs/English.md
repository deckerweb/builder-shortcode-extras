# Builder Shortcode Extras

[English](https://github.com/deckerweb/builder-shortcode-extras/wiki/English) · [Deutsch](https://github.com/deckerweb/builder-shortcode-extras/wiki/Deutsch)

**Small dynamic helpers. Ready when you need them.**

Copyright, dates, links and reusable content without custom PHP. Includes searchable help and a compact generator directly in WordPress.

Version **1.3.1** · WordPress **6.7+** · PHP **8.1+**

## Quick start

1. Download the plugin ZIP from GitHub Releases.
2. Open Plugins → Add New → Upload Plugin and activate it.
3. Open Tools → Builder Shortcode Extras, choose a helper and copy its shortcode.
4. Paste it into a Shortcode block or supported builder field and check its output.

BSE saves no generator values and changes no content.

## Plugin page

![Shortcode help and generator](../assets-github/shortcode-tools-en.png)

## Examples

```text
[bse-copyright first="2019"]
[bse-post-date format="Y-m-d"]
[bse-post-modified-date label="Last updated: "]
[bse-post-link id="123" text="Read more"]
[bse-item-content id="123"]
```

## Shortcode reference


| Shortcode | Attributes | Prerequisite |
|---|---|---|
| `bse-nav-menu` | `before`, `after`, `class`, `wrapper`, `menu`, `container`, `container_class`, `container_id`, `menu_class`, `menu_id`, `fallback_cb`, `item_before`, `item_after`, `link_before`, `link_after`, `depth`, `walker`, `theme_location`, `items_wrap`, `item_spacing` | WordPress |
| `bse-item-content` | `id`, `css` | WordPress |
| `bse-comment-form` | `before`, `after`, `class`, `wrapper`, `post_id`, `id_form`, `class_form`, `title_reply`, `title_reply_to`, `cancel_reply_link`, `label_submit` | WordPress |
| `bse-elementor-template` | `id`, `css` | Elementor Free |
| `bse-genesis-footer` | `class`, `wrapper` | Genesis 3.1+ |
| `bse-genesis-breadcrumbs` | `class`, `wrapper` | Genesis 3.1+ |
| `bse-copyright` | `after`, `before`, `copyright`, `first`, `class`, `wrapper` | WordPress |
| `bse-site-title` | `after`, `before`, `class`, `wrapper` | WordPress |
| `bse-site-slogan` | `after`, `before`, `class`, `wrapper` | WordPress |
| `bse-home-link` | `after`, `before`, `text`, `target`, `rel`, `class`, `wrapper` | WordPress |
| `bse-loginout` | `after`, `before`, `login_text`, `logout_text`, `login_target`, `logout_target`, `login_redirect`, `logout_redirect`, `class`, `wrapper` | WordPress |
| `bse-site-updated` | `before`, `after`, `type`, `label_date`, `date_format`, `label_time`, `time_format`, `tooltip`, `class`, `wrapper` | WordPress |
| `bse-post-count` | `post_type`, `status`, `before`, `after`, `class`, `wrapper` | WordPress |
| `bse-post-date` | `after`, `before`, `post_id`, `format`, `label`, `relative_depth`, `class`, `wrapper` | WordPress |
| `bse-post-time` | `after`, `before`, `post_id`, `format`, `label`, `class`, `wrapper` | WordPress |
| `bse-post-modified-date` | `after`, `before`, `post_id`, `format`, `label`, `relative_depth`, `class`, `wrapper` | WordPress |
| `bse-item-last-updated` | `after`, `before`, `post_id`, `format`, `label`, `class`, `wrapper` | WordPress |
| `bse-post-modified-time` | `after`, `before`, `post_id`, `format`, `label`, `class`, `wrapper` | WordPress |
| `bse-post-author` | `after`, `before`, `class`, `wrapper` | WordPress |
| `bse-post-author-link` | `after`, `before`, `target`, `rel`, `class`, `wrapper` | WordPress |
| `bse-post-author-posts-link` | `after`, `before`, `target`, `rel`, `class`, `wrapper` | WordPress |
| `bse-post-tags` | `after`, `before`, `sep`, `class`, `wrapper` | WordPress |
| `bse-post-categories` | `sep`, `before`, `after`, `class`, `wrapper` | WordPress |
| `bse-post-terms` | `after`, `before`, `sep`, `taxonomy`, `class`, `wrapper` | WordPress |
| `bse-post-edit` | `after`, `before`, `label`, `class`, `wrapper` | WordPress |
| `bse-post-link` | `id`, `slug`, `post_type`, `privacy`, `text`, `target`, `rel`, `before`, `after`, `class`, `wrapper` | WordPress |
| `bse-user` | `user_id`, `field`, `default`, `before`, `after`, `class`, `wrapper` | WordPress |
| `bse-userid` |  | WordPress |
| `bse-email` |  | WordPress |
| `bse-login` |  | WordPress |
| `bse-displayname` |  | WordPress |
| `bse-firstname` |  | WordPress |
| `bse-lastname` |  | WordPress |
| `bse-version` | `type`, `plugin`, `constant`, `custom`, `before`, `after`, `class`, `wrapper` | WordPress |
| `bse-wpblock` | `id`, `css` | WordPress synced patterns |
| `bse-astra-layout` | `id`, `css` | Astra Pro custom layouts |

| `bse-post-title` | `post_id`, `wrapper`, `class`, `before`, `after`, `fallback` | WordPress |
| `bse-post-excerpt` | `post_id`, `words`, `wrapper`, `class`, `before`, `after`, `fallback` | WordPress |
| `bse-post-image` | `post_id`, `size`, `wrapper`, `class`, `before`, `after`, `fallback` | WordPress |
| `bse-reading-time` | `post_id`, `wpm`, `wrapper`, `class`, `before`, `after`, `fallback` | WordPress |

All BSE shortcodes support escaped `fallback` text when output is empty. Text helpers offer `wrapper="none"`; post date/time and site-modification helpers offer `semantic="true"` for the selected item’s datetime. Images and embedded content retain their markup.

## FAQ by topic

### Getting started

#### Do I need to configure the plugin?

No. Open Tools → Builder Shortcode Extras to find or generate a shortcode. The page does not save settings.

#### Where do I paste a shortcode?

Use a WordPress Shortcode block or a builder field that explicitly supports shortcodes. Arbitrary text, URL and HTML fields do not necessarily execute them.

#### What can the generator create?

The generator creates copyright, publication/modification dates, post links, embedded content, titles, excerpts, featured images and reading time. Other helpers remain in the searchable catalog.

#### Can I search in German?

Yes. The interface and task descriptions follow your WordPress admin language. Shortcode and attribute names remain unchanged.

#### How do I find a post or template ID?

Open the item in WordPress. Search for a title in the generator or use the number after post= in the editor URL. Supported template list tables also show shortcode examples.

### Output and integrations

#### Which post does a date refer to?

Leave post_id empty to use the current WordPress post. Specify an ID for another item. A page-builder preview may have a different context from the final page.

#### Can I use a relative date?

Choose Relative date in the generator or use format="relative". Relative output uses the existing elapsed-time helper.

#### Can I change the HTML and classes?

Most text helpers accept wrapper, class, before and after. Multiple classes are supported. Unsafe or unsupported wrapper tags fall back to span. Text helpers support wrapper="none"; embedding, menus, comment forms and images retain their own markup. fallback supplies escaped text when no value is available.

#### Does it load frontend CSS or JavaScript?

The help and generator load assets only on their own admin page. Embedded builder templates may load their builder’s own assets, and the comment form may rely on WordPress resources.

#### Why is embedded content empty?

Check the ID, publication status and required builder. Visitors cannot embed private, draft, trashed or password-protected content without the required access. Recursive references stop with empty output.

#### Are native blocks supported?

Yes. Embedded content is passed through native block rendering and shortcode processing. Full theme content filters are intentionally not applied.

#### Does Elementor need Pro?

The bse-elementor-template integration is registered with Elementor Free when Pro is inactive. The generic content helper can route Elementor-built items when Elementor is available.

#### Is Beaver Builder supported?

Yes. Items with _fl_builder_enabled use Beaver’s own saved-layout shortcode. Merely installing Beaver does not reroute ordinary posts. No additional Beaver features were added.

#### Is Genesis still supported?

Yes. The existing Genesis footer and breadcrumb helpers remain available with Genesis 3.1+. They are maintained as compatibility features.

### Updates, data and security

#### Are synced patterns supported with Classic Editor?

The bse-wpblock helper is available whenever WordPress registers the wp_block post type. A legacy extra Blocks menu is no longer added.

#### Why is there no live preview in the generator?

The generator constructs valid shortcode syntax locally. View the actual output on a test page with the intended post and builder context.

#### How does copying work without HTTPS?

The interface tries the browser clipboard, then a local copy fallback. If copying is unavailable, it selects the text for manual copying. Examples remain readable without JavaScript.

#### How do updates work?

The bundled deckerweb updater checks this public GitHub repository for newer stable releases through the regular WordPress update system. It does not automatically install prereleases or enable automatic updates. Test releases are installed manually.

#### What is the deckerweb Library?

The embedded deckerweb Library adds a curated catalog under Plugins → Add New → deckerweb. Its preferences live under Settings → deckerweb Library, or Network Settings on Multisite. Opening the catalog installs nothing. Installation and activation are separate actions.

#### Does the plugin send usage data?

BSE has no usage telemetry. Generator searches send the search term to the current WordPress website only. The updater requests GitHub release metadata. The Library uses its bundled catalog by default; online catalog updates require explicit configuration. Servers naturally see the requesting IP address.

#### What changed for existing installations?

Names, aliases and output filters remain. PHP 8.1 is now required. Invalid wrappers fall back to span, text is escaped more consistently, inaccessible embedded content is withheld, and wp-admin shortcode registration follows its opt-in filter.

#### Can I enable text helpers in wp-admin?

Developers can return true from bse/filter/shortcodes_in_admin. Content embedding, navigation, comment forms and builder/Genesis rendering remain excluded there. REST requests remain available for frontend rendering.

#### Is multisite supported?

BSE can be activated per website or network-wide. Content IDs belong to the current website. Website administrators use Tools → Builder Shortcode Extras; super administrators also have Network Settings → Builder Shortcode Extras. Library preferences are shared per network.

#### What are the requirements and support scope?

WordPress 6.7+ and PHP 8.1+. Library installation requires ZipArchive. Report reproducible issues on GitHub with plugin, WordPress, PHP and builder versions. Commercial builder adapters were checked with representative fixtures, not licensed builder installations.

#### What happens to data when I remove the plugin?

BSE saves no shortcode or generator settings. Deactivation preserves all data. Uninstall clears only its temporary updater cache and releases Library ownership. Other installed Library hosts, even inactive, protect shared data. The final host removes temporary Library data; preferences remain unless the Library deletion option was enabled. Posts, shortcodes, installed plugins and foreign data remain.

#### Can the version helper display credentials?

No. Shortcode attributes can select constants whose names end in VERSION or VER. Other constants require an explicit trusted PHP default filter. Do not use developer filters to expose installation secrets.

#### How do I report a security issue?

Use Security → Advisories → Report a vulnerability in this plugin’s GitHub repository. Include plugin, Library and environment versions plus a minimal reproduction, without credentials. Keep details out of public issues. See SECURITY.md.

#### How do the new content helpers work?

Use post_id to select readable content or omit it for the current frontend item. Excerpts use an explicit excerpt or stored text, with words between 1 and 500. Images use registered WordPress sizes and native responsive markup. Reading time counts stored prose without executing embedded shortcodes; wpm is limited to 50–1000. Builder-only markup and image-heavy articles can produce incomplete estimates.

#### How do fallback and semantic dates work?

fallback is escaped plain text for missing output, including inaccessible content; it never grants access. semantic="true" adds time/datetime markup to post publication/modification dates. wrapper="none" removes only the text helper’s outer wrapper; links and semantic time markup remain.

#### What does the content search send?

After two characters the generator searches the current website through authenticated WordPress AJAX. A nonce and administrator capability are required. At most ten readable title/ID results are returned. No search terms are sent to GitHub or external services; manual IDs remain available when search fails.

## Developers

```php
add_filter( 'bse/filter/shortcodes_in_admin', '__return_true' );
```

Existing `bse/filter/shortcode_defaults/*` and `bse/filter/shortcode/*` filters remain. PHP callbacks are trusted developer code and may return HTML. Menus use a safe default callback; custom callbacks are configured only through PHP defaults filters.

[Changelog](https://github.com/deckerweb/builder-shortcode-extras/wiki/CHANGELOG) · [Security](https://github.com/deckerweb/builder-shortcode-extras/security/advisories/new)
