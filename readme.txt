=== Builder Shortcode Extras - Useful Collection for WordPress to Save You Time ===
Contributors: daveshine, deckerweb, wpautobahn, toolbarextras
Donate link: https://www.paypal.me/deckerweb
Tags: shortcode, elementor, genesis, page builder, gutenberg, blocks, widgets, shortcodes, oceanwp, astra, generatepress, deckerweb
Requires at least: 4.7
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: 0.9.0
License: GPL-2.0-or-later
License URI: https://opensource.org/licenses/GPL-2.0

A collection of totally useful extra Shortcodes to make the life of Site Builders more easy.

== Description ==

A totally useful collection of helper Shortcodes to make your life as a Site Builder and non-coder way easier and save you time: Shortcodes for Last Updated Date/ Time, Post Counter, Versions, Post Info, Copyright and Footer Info, Users, Reusable Blocks (Gutenberg), Elementor Templates, Genesis Footer and even more.

These are Shortcodes you don't get anywhere else. These are smart and really helpful - perfect for Non-Coder Designers, for Non Techies, for Site Builders. Those small parts and strings that make your life easier, now available as Shortcodes. Use them anywhere in WordPress: in Gutenberg Block Editor, in Classic Editor, Widgets, Post Types, Page Builders (Widgets, Elements...) and layout/ hook post types from various (Premium) Themes.

**This is my personaly story, why this plugin here was born:** When building a site and for example you need the date when a post type item was last updated? You just need this value as a simple string to put in a page builder widget or somewhere else, right? So where do you find the code snippet for it? Or a plugin? And you need it now, easy and fast?  
I was there, I know exactly how it feels at 02.00 a.m. in the middle of night. So this Shortcode collection was born. Totally simple. No styles, no scripts. But included CSS classes so I can style it my own - or apply my own classes or those from my theme. And I can change the HTML wrapper tag. Problem solved. It only took 2 minutes: 1 minute of installing and activing this plugin, plus 1 minute of inserting the actual Shortcode. Problem solved. Now I can go to sleep. With peace of mind. And the presentation the next morning for my client will be a success. Oh yeah, I love it! ;-)

= ♥️ What the Plugin does? =
* Up to 30 useful helper Shortcodes
* Optional: Shortcode for Reusable Blocks - for Gutenberg Block Editor - reuse them everyhwere
* Optional: Shortcode for Elementor Templates - for Elementor free version!
* Optional: Shortcode for complete Footer Creds/ Copyright of Genesis 3.1 or higher (just use without hooks everywhere you can insert a Shortcode!)
* Totally user friendly!
* Use in Gutenberg Block Editor - works fine in these Core Blocks: Shortcode, Paragraph, List, Heading and everywhere else!
* Use in Classic Editor - Visual and Text/ HTML mode, as you were used to
* Use in Widgets - Text/ HTML widget is automatically prepared for Shortcodes with this plugin here!
* Use in Page Builders like *Elementor, Beaver Builder, SiteOrigin Page Builder, WPBakery Page Builder, Visual Composer Website Builder, Oxygen Builder, Brizy Page Builder, Thrive Architect* etc.
* Use in Theme Pro versions with special layout post types: *Astra Custom Layouts, OceanWP Library, GeneratePress Elements, Page Builder Framework Sections, Customify Hooks, Suki Blocks, Hestia Hooks, Genesis Blox, Genesis Simple Hooks*
* Optionally set a custom wrapper (`span`, `p`, `div`, `h`, or any other appropriate HTML5 element, like `article`, `section`, whatever)
* Optionally set a custom CSS class for the wrapper to allow for even better custom styling
* No frontend bloat: no scripts, no styles - nothing! :-)
* Lightweight, efficient
* Developer friendly: clean code, inline documentation, lots of filters available

= 🚀 Typical Use Cases of this Plugin =
1️⃣ **First example:** You want a footer copyright with the current year as dynamic - so you set it once and forget. Our Shortcode `[bse-copyright]` does exactly that.  
2️⃣ **Second example:** You want to put a "Last updated" date under each single post or page - use our Shortcode `[bse-post-modified-date]`, set your custom date format, set a custom label and place it into a (footer) widget. Done.  
3️⃣ **Third example:** You are using Elementor free, *not* Pro, and want to display one of your templates via a Shortcode? Now you can! (with our Shortcode `[bse-elementor-template]`)
4️⃣ **Fourth example:** You want to display a Reusable Block via a Shortcode? Now you can! (with our Shortcode `[bse-wpblock]`)

= 🍰 Available Shortcodes: =
* **`[bse-version]`** -- attributes: wrapper, class, before, after, type, plugin, custom
* **`[bse-copyright]`** -- attributes: wrapper, class, before, after, copyright, first
* **`[bse-site-updated]`** -- attributes: wrapper, class, before, after, type, label_date, date_format, label_time, time_format, tooltip
* **`[bse-site-title]`** -- attributes: wrapper, class, before, after
* **`[bse-site-slogan]`** -- attributes: wrapper, class, before, after
* **`[bse-home-link]`** -- attributes: wrapper, class, before, after, text, target, rel
* **`[bse-loginout]`** -- attributes: wrapper, class, before, after, login_text, logout_text, login_target, logout_target, login_redirect, logout_redirect 
* **`[bse-user]`** -- attributes: wrapper, class, before, after, user_id, field, default
* **`[bse-post-count]`** -- attributes: wrapper, class, before, after, post_type, status
* **`[bse-post-date]`** -- attributes: wrapper, class, before, after, post_id, format, label, relative_depth
* **`[bse-post-time]`** -- attributes: wrapper, class, before, after, post_id, format, label
* **`[bse-post-modified-date]`** / **`[bse-item-last-updated]`** -- attributes: wrapper, class, before, after, post_id, format, label, relative_depth
* **`[bse-post-modified-time]`** -- attributes: wrapper, class, before, after, post_id, format, label
* **`[bse-post-author]`** -- attributes: wrapper, class, before, after
* **`[bse-post-author-link]`** -- attributes: wrapper, class, before, after, target, rel
* **`[bse-post-author-posts-link]`** -- attributes: wrapper, class, before, after, target, rel
* **`[bse-post-tags]`** -- attributes: wrapper, class, before, after, sep
* **`[bse-post-categories]`** -- attributes: wrapper, class, before, after, sep
* **`[bse-post-terms]`** -- attributes: wrapper, class, before, after, sep, taxonomy
* **`[bse-post-edit]`** (Post Edit Link) -- attributes: wrapper, class, before, after, label
* **`[bse-post-link]`** -- attributes: wrapper, class, before, after, id, slug, post_type, privacy, text, target, rel
* **`[bse-item-content]`** -- attributes: id, css
* **`[bse-nav-menu]`** -- attributes: wrapper, class, before, after, menu, container, container_class, container_id, menu_class, menu_id, fallback_cb, item_before, item_after, link_before, link_after, depth, walker, theme_location, items_wrap, item_spacing
* **`[bse-comment-form]`** -- attributes: wrapper, class, before, after, post_id, id_form, class_form, title_reply, title_reply_to, cancel_reply_link, label_submit

= 🍰 Available Integrations - Shortcodes =
* *Gutenberg Block Editor, Reusable Blocks:* **`[bse-wpblock]`** -- attributes: id
* *Elementor free version:* **`[bse-elementor-template]`** -- attributes: id, css
* *Astra Custom Layouts (via Astra Pro):* **`[bse-astra-layout]`** -- attributes: id
* *Genesis Framework v3.1.0 or higher:* **`[bse-genesis-footer]`** -- attributes: wrapper, class
* *Genesis Framework:* **`[bse-genesis-breadcrumbs]`** -- attributes: wrapper, class
* **Note:** All these integrations are optional, only if the supported plugin or theme is installed and currently active. For the Block Editor there is smart logic in place recognizing popular "disable" plugins like "Classic Editor", "Disable Gutenberg" amongst others.

= 👍 Recommended Time Saver =
Try [**Toolbar Extras**](https://toolbarextras.com/) my other plugin for Site Builders and admins: Building sites with Elementor? [**Your work will get easier & faster with Toolbar Extras.**](https://wordpress.org/plugins/toolbar-extras/) With extended plugin & theme support baked right in. Of course, "Builder Shortcode Extras" is integrated as well :)

* [Plugin Page here on WordPress.org](https://wordpress.org/plugins/toolbar-extras/)
* [Plugin's own website toolbarextras.com](https://toolbarextras.com/)
* In your WordPress admin dashboard search for `toolbar extras` in the plugin installer ;-)

= 🌎 Translations =
* 🇺🇸 English (United States) - `en_US` = default, always included
* 🇩🇪 [German (informal, default)](https://translate.wordpress.org/locale/de/default/wp-plugins/builder-shortcode-extras) - `de_DE` - always included
* 🇩🇪 [German (formal)](https://translate.wordpress.org/locale/de/formal/wp-plugins/builder-shortcode-extras) - `de_DE_formal` - always included
* `.pot` file (`builder-shortcode-extras.pot`) for translators is always included in the plugin's 'languages' folder :)

= 👍 Be a Contributor =
If you want to translate, [go to the Translation Portal at translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/builder-shortcode-extras).

You can also contribute code-wise via our [Builder Shortcode Extras GitHub Repository](https://github.com/deckerweb/builder-shortcode-extras) - and see where you can help.

= 📝 Documentation and Support =
* If you have any more questions, visit our support on the [Plugin's Forum](https://wordpress.org/support/plugin/builder-shortcode-extras).

= ⚡ Liked Builder Shortcode Extras? =
* **Rate us 5 ⭐ stars** on [WordPress.org](https://wordpress.org/support/plugin/builder-shortcode-extras/reviews/?filter=5/#new-post) :)
* Join our [**Facebook User Community Support Group** 💬](https://www.facebook.com/groups/deckerweb.wordpress.plugins/)
* Like 👍 our [**Facebook Info Page for Deckerweb Plugins**](https://www.facebook.com/deckerweb.wordpress.plugins/)
* [**Subscribe to my Newsletter for insider info on this plugin** 💯](https://eepurl.com/gbAUUn), plus tutorials and more stuff on deckerweb WordPress plugins - join a thriving community of site builders!
* [**Become a Patron** 💜](https://www.patreon.com/deckerweb) and support ongoing development, maintenance and support of this plugin

= ☕ This Plugin ... =
* ... scratches my own itch!
* ... is *Quality Made in Germany*
* ... was created with love (plus some coffee) :-) - [if you like it consider donating](https://www.paypal.me/deckerweb)


== Installation ==

= Minimum Requirements =

* WordPress version 4.7 or higher
* PHP version 5.6.20 or higher
* MySQL version 5.0 or higher

= We Recommend Your Host Supports at least: =

* PHP version 7.2 or higher
* MySQL version 5.6 or higher / OR MariaDB 10.1 or higher
* HTTPS support

= Installation =

1. Install using the WordPress built-in Plugin installer (via **Plugins > Add New** - search for `builder shortcode extras`), or extract the ZIP file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. There are no settings whatsover. Just start using the Shortcodes everywhere.
4. Now enjoy displaying special stuff without coding and saving some time ;-)


== Frequently Asked Questions ==

= How does styling work? =
The plugin itself loads NO styles or scripts for the Shortcodes! It only adds very minimal markup by default (HTML span tag), with one specific class for the Shortcode. It's **completely up to you** to style these default classes (if you want) or add your own. -- In most cases the Shortcodes put out only simple strings - so, when used within regular text paragraphs you won't see any difference compared to the text around. But, if you need some special styling you can always add it and tweak your styles.


= What Does the Gutenberg Block Editor Shortcode do? =
It can display any Reusable Block from WordPress Core Reusable Blocks (post type: `wp_block`) via a Shortcode. Just copy the Shortcode for a certain Reusable Block from the post type list table - look for the "Blocks" left-hand admin menu. This will make the Reusable Blocks way more useful and you can use them everyhwere - without coding!


= What Does the Astra Layout Shortcode do? =
It can display any Astra Custom Layout via a Shortcode. Just copy the Shortcode for a certain Astra Custom Layout from the post type list table - look for the "Custom Layouts" under the left-hand "Appearance" admin menu. This will make those Custom Layouts even more useful and you can use them everyhwere - without coding! This Shortcode feature comes in handy especially for "Hook" type Shortcodes: you don't need to specify a Hook if used via Shortcode. This makes the Astra Custom Layouts more independent from Hooks and gives you even more possibilities.


= What Does the Elementor Template Shortcode do? =
It displays the a template from Elementor "Saved Templates" (former "My Templates") library via a Shortcode. It is the same thing Elementor Pro does - but our plugin brings this feature to Elementor free version. This will make Elementor free version way more useful as this brings so many new possibilities to work with Templates!


= What are the Genesis Shortcodes doing? =
**Genesis Footer Shortcode** - It takes the whole content block of your Genesis Footer settings from the Customizer - since Genesis 3.1.0 or higher - and displays it wherever you want. That way it becomes totally easy to customize your footer info text even if you want to show it somewhere else than the default location. It is easier than to use hooks or code for that.  

**Genesis Breadcrumbs Shortcode** - It takes the whole content block of Breadcrumbs for a current item/view and displays it wherever you want via Shortcode. This could be useful in some situations. It can make you a bit more independent from hooks and coding work. Please note: At the time being it is not possible to tweak the Breadcrumb arguments via the Shortcode!


= Can I use these Shortcodes in my Page Builder? =
Yes, of course! Everywhere a Shortcode is working in your Page Builder, our plugin's Shortcodes will also work. For more info on that you might also have a look in the documentation of your favorite Page Builder or similar plugin about Shortcode support for that tool.


= Does this Plugin work with Gutenberg / WordPress 5.0+ / Block Editor? =
Yes, of course! - The plugin is fully compatible with Gutenberg Block Editor: you can use Shortcodes almost everywhere within the Block Editor, not just in the so-called "Shortcode" block but also in Paragraph, List, Heading, whereever you can enter regular text, most likely Shortcodes will also be rendered. And, for our type of Shortcodes this makes total sense, for example to display a "last updated date", a post counter, a version number, some user info field, whatever. Just try it out.


= Does this Plugin work with Classic Editor and even ClassicPress? =
Yes, this plugin works with the Classic Editor plugin perfectly fine.

By default the plugin is also fully compatible with ClassicPress. I will try my best to also have my plugin work perfectly in ClassicPress, the fork of WordPress without Gutenberg. It should already be fully compatible but I will follow all events closely to adjust compat if needed.


= Will this Plugin slow down my site? =
Absolutely not. Since the plugin is NOT loading any scripts or styles all is really lightweight. A no brainer to install and use.


= Does the Plugin work with Multisite? =
Yes, it works fine in Multisite, you could even activate it Network-wide. You can use the Shortcodes in each sub site like you would in a regular (single) install.


= Will other third-party Plugins be supported? =
Only if it makes sense, like the current Elementor Template Shortcode for example. Remember: we don't want to load any scripts or styles. So, if a potential Shortcode fits our philosophy and could be totally useful, then the chance of adding it to this plugin gets very high! ;-)

If you have any suggestion for such a Shortcode, please feel free to [**open a new GitHub Issue**](https://github.com/deckerweb/builder-shortcode-extras/issues) in our development repository and tell us all about it. Thanks in advance!


= More info on Translations? =

* English - default, always included
* German (de_DE): Deutsch - immer dabei! :-)
* For custom and update-safe language files please upload them to `/wp-content/languages/builder-shortcode-extras/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that as well, just use a language file like `builder-shortcode-extras-en_US.mo/.po` to achieve that (for creating one see the following tools).

**Easy WordPress.org plugin translation platform with GlotPress platform:** [**Translate "Builder Shortcode Extras"...**](https://translate.wordpress.org/projects/wp-plugins/builder-shortcode-extras)

*Note:* All my plugins are internationalized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating and validating I recommend the awesome ["Poedit Editor"](https://www.poedit.net/), which works fine on Windows, macOS and Linux.


== Screenshots ==

1. Use the Shortcodes in Gutenberg Block Editor: Shortcode Block, Paragraph Block, List Block, Heading Block etc.

2. The Shortcodes from Block Editor rendered on the Frontend

3. Shortcode for each Reusable Block to insert & display anywhere (especially outside of the Block Editor...)

4. Use the Shortcodes in Classic Editor as well: Visual Mode or Text Mode (HTML) - doesn't matter, will work

5. Insert a Shortcode into Text Widget or Custom HTML Widget

6. For Elementor Free Version: Shortcode for each Saved Template!

7. Insert a Shortcode into Elementor Page Builder: Shortcode Widget

8. Insert a Shortcode into Elementor Text Editor Widget

9. Insert a Shortcode into Elementor Heading widget (will be rendered fully on the frontend, of course!)

10. For Astra Pro: Shortcode for each Astra Custom Layout - great for reusage


== Changelog ==

= ⚡ 0.9.0 - 2019-09-09 =
* New: Beta release of the plugin on [its public GitHub repository](https://github.com/deckerweb/builder-shortcode-extras)


== Upgrade Notice =

= 0.9.0 =
Beta plugin release on GitHub.com


== ☺️ Donate ==
Enjoy using *Builder Shortcode Extras*? **[Please consider making a donation](https://www.paypal.me/deckerweb)** - every donation helps to support the project's continued development, maintenance and support.
**Thank you very much in advance for your support!**


== 👏 Credits ==
Credit where credit is due. The following code, classes and libraries were used for this plugin, all licensed under the GPL. Note: Credit is also referenced in the code doc block inline where used.

* Class "DDWlib Plugin Installer Recommendations" (PLIR) by David Decker - DECKERWEB (GPLv2 or later)
* Class "Astra Notices" by Brainstorm Force (GPLv2 or later)
* Some Shortcode functions are based on the Post & Footer Shortcodes by the Genesis Framework from StudioPress (GPLv2 or later)


== Additional Info ==
**Idea Behind / Philosophy:** Just a smart and lightweight plugin for all the Non-Coder Site Builders out there needing various info values and elements to place via a plain simple Shortcode. Just making their daily work and life just a little easier.


== Last but not least ==
**Special Thanks go out to my family for allowing me to do such spare time projects (aka free plugins) and supporting me in every possible way!**


== 🔆 My Other Plugins ==
* [**Toolbar Extras for Elementor - WordPress Admin Bar Enhanced**](https://wordpress.org/plugins/toolbar-extras/)
* [**Toolbar Extras for Give Donations (GiveWP) - Add-On plugin**](https://wordpress.org/plugins/toolbar-extras-givewp/)
* [**Toolbar Extras for MainWP Dashboard - Add-On plugin**](https://wordpress.org/plugins/toolbar-extras-mainwp/)
* [**Toolbar Extras for Oxygen Builder - Add-On plugin**](https://wordpress.org/plugins/toolbar-extras-oxygen/)
* [**Polylang Connect for Elementor – Language Switcher & Template Tweaks**](https://wordpress.org/plugins/connect-polylang-elementor/)
* [**Builder Template Categories - for WordPress Page Builders**](https://wordpress.org/plugins/builder-template-categories/)
* [**Simple Download Manager for WP Document Revisions**](https://wordpress.org/plugins/wpdr-simple-downloads/)
* [Genesis What's New Info](https://wordpress.org/plugins/genesis-whats-new-info/)
* [Genesis Layout Extras](https://wordpress.org/plugins/genesis-layout-extras/)
* [Genesis Widgetized Not Found & 404](https://wordpress.org/plugins/genesis-widgetized-notfound/)
* [Genesis Extra Settings Transporter](https://wordpress.org/plugins/genesis-extra-settings-transporter/)
* [Genesis Widgetized Footer](https://wordpress.org/plugins/genesis-widgetized-footer/)
* [Genesis Widgetized Archive](https://wordpress.org/plugins/genesis-widgetized-archive/)
* [Multisite Toolbar Additions](https://wordpress.org/plugins/multisite-toolbar-additions/)
* [Cleaner Plugin Installer](https://wordpress.org/plugins/cleaner-plugin-installer/)
* [*My plugins newsletter*](https://eepurl.com/gbAUUn)
