# Builder Shortcode Extras

**Small dynamic helpers. Ready when you need them.**

Copyright, dates, links and reusable content without custom PHP. Includes searchable help and a compact generator directly in WordPress.

Version **1.2.0** · WordPress **6.7+** · PHP **8.0+** · GPL-2.0-or-later

## Contents

- [Quick start](#quick-start)
- [Examples](#examples)
- [Shortcode reference](#shortcode-reference)
- [FAQ](#faq)

## Quick start

1. Upload the test ZIP through Plugins → Add New → Upload Plugin.
2. Activate it and open Tools → Builder Shortcode Extras.
3. Find a helper or configure one of the five use cases.
4. Copy the shortcode into a Shortcode block or a supported builder field.
5. Check the actual output on your test page.

There are no additional BSE settings. The page does not edit content or save generator values.

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

## FAQ

### Do I need to configure the plugin?

No. Open Tools → Builder Shortcode Extras to find or generate a shortcode. The page does not save settings.

### Where do I paste a shortcode?

Use a WordPress Shortcode block or a builder field that explicitly supports shortcodes. Arbitrary text, URL and HTML fields do not necessarily execute them.

### What can the generator create?

Copyright, publication date, last-updated date, a post/page link and embedded content. Other helpers remain available in the searchable catalog.

### Can I search in German?

Yes. The interface and task descriptions follow your WordPress admin language. Shortcode and attribute names remain unchanged.

### How do I find a post or template ID?

Open the item in WordPress. The number after post= in the editor URL is its ID. Supported template list tables also show shortcode examples.

### Which post does a date refer to?

Leave post_id empty to use the current WordPress post. Specify an ID for another item. A page-builder preview may have a different context from the final page.

### Can I use a relative date?

Choose Relative date in the generator or use format="relative". Relative output uses the existing elapsed-time helper.

### Can I change the HTML and classes?

Most text helpers accept wrapper, class, before and after. Multiple classes are supported. Unsafe or unsupported wrapper tags fall back to span. This version does not add a wrapper-free mode.

### Does it load frontend CSS or JavaScript?

The help and generator load assets only on their own admin page. Embedded builder templates may load their builder’s own assets, and the comment form may rely on WordPress resources.

### Why is embedded content empty?

Check the ID, publication status and required builder. Visitors cannot embed private, draft, trashed or password-protected content without the required access. Recursive references stop with empty output.

### Are native blocks supported?

Yes. Embedded content is passed through native block rendering and shortcode processing. Full theme content filters are intentionally not applied.

### Does Elementor need Pro?

The bse-elementor-template integration is registered with Elementor Free when Pro is inactive. The generic content helper can route Elementor-built items when Elementor is available.

### Is Beaver Builder supported?

Yes. Items with _fl_builder_enabled use Beaver’s own saved-layout shortcode. Merely installing Beaver does not reroute ordinary posts. No additional Beaver features were added.

### Is Genesis still supported?

Yes. The existing Genesis footer and breadcrumb helpers remain available with Genesis 3.1+. They are maintained as compatibility features.

### Are synced patterns supported with Classic Editor?

The bse-wpblock helper is available whenever WordPress registers the wp_block post type. A legacy extra Blocks menu is no longer added.

### Why is there no live preview in the generator?

This first generator constructs valid shortcode syntax locally. View the actual output on a test page with the intended post and builder context.

### How does copying work without HTTPS?

The interface tries the browser clipboard, then a local copy fallback. If copying is unavailable, it selects the text for manual copying. Examples remain readable without JavaScript.

### How do updates work?

The bundled deckerweb updater checks this public GitHub repository for newer stable releases through the regular WordPress update system. It does not automatically install prereleases or enable automatic updates. Test releases are installed manually.

### What is the deckerweb Library?

It adds a deckerweb tab to Plugins → Add New. It is bundled, shares one runtime with other deckerweb plugins, and can be hidden in Settings → deckerweb Library. Its bundled curated catalog installs approved releases; BSE RC1 is not newly published or added to that catalog.

### Does the plugin send usage data?

BSE has no usage telemetry. The updater requests GitHub release metadata. The Library uses its bundled catalog by default; online catalog updates require explicit configuration. Servers naturally see the requesting IP address.

### What changed for existing installations?

Names, aliases and output filters remain. PHP 8.0 is now required. Invalid wrappers fall back to span, text is escaped more consistently, inaccessible embedded content is withheld, and wp-admin shortcode registration follows its opt-in filter.

### Can I enable text helpers in wp-admin?

Developers can return true from bse/filter/shortcodes_in_admin. Content embedding, navigation, comment forms and builder/Genesis rendering remain excluded there. REST requests remain available for frontend rendering.

### Is multisite supported?

BSE can be network-activated. Each site has its own content context; the tools page is available to authorized site administrators. The shared Library has its own network settings. A network-admin generator was not added.

### What are the requirements and support scope?

WordPress 6.7+ and PHP 8.0+. Library installation requires ZipArchive. Report reproducible issues on GitHub with plugin, WordPress, PHP and builder versions. Commercial builder adapters were checked with representative fixtures, not licensed builder installations.

## Developers

```php
add_filter( 'bse/filter/shortcodes_in_admin', '__return_true' );
```

Existing `bse/filter/shortcode_defaults/*` and `bse/filter/shortcode/*` filters remain. Filter output is trusted developer code and may return HTML. Registration files remain grouped by topic under `includes/shortcodes/`.

[Changelog](CHANGELOG.md) · [Test guide](TESTING-de.md)
