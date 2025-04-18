# Builder Shortcode Extras – WordPress Shortcodes Collection to Save You Time 

A collection of totally useful extra Shortcodes to make the life of Site Builders more easy.

![Builder Shortcode Extras plugin banner](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/refs/heads/master/assets/banner-1544x500.png)

---

[Support Project](#support-the-project) | [Installation](#installation) | [Updates](#updates) | [Description](#description) | [FAQ](#frequently-asked-questions) | [Screenshots](#screenshots) | [Changelog](#changelog) | [Plugin Scope / Disclaimer](#plugin-scope--disclaimer)

---

## Support the Project

If you find this project helpful, consider showing your support by buying me a coffee! Your contribution helps me keep developing and improving this plugin.

Enjoying the plugin? Feel free to treat me to a cup of coffee ☕🙂 through the following options:

- [![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/W7W81BNTZE)
- [Buy me a coffee](https://buymeacoffee.com/daveshine)
- [PayPal donation](https://paypal.me/deckerweb)
- [Join my **newsletter** for DECKERWEB WordPress Plugins](https://eepurl.com/gbAUUn)

---

## Installation 

**Quick Install**
1. **Download ZIP:** [**builder-shortcode-extras.zip**](https://github.com/deckerweb/builder-shortcode-extras/releases/latest/download/builder-shortcode-extras.zip)
2. Upload via WordPress Plugins > Add New > Upload Plugin
3. There are no settings whatsover. Just start using the Shortcodes everywhere.
4. Now enjoy displaying special stuff without coding and saving some time ;-)


### Requirements 

* WordPress version 6.7 or higher
* PHP version 7.4 or higher
* MySQL version 5.6 or higher

---

## Updates 

1) Alternative 1: Just download a new [ZIP file](https://github.com/deckerweb/builder-shortcode-extras/releases/latest/download/builder-shortcode-extras.zip) (see above), upload and override existing version. Done.

2) Alternative 2: Use the (free) [**_Git Updater_ plugin**](https://git-updater.com/) and get updates automatically.

3) Alternative 3: Upcoming! – In future I will built-in our own deckerweb updater. This is currently being worked on for my plugins. Stay tuned!

---

## Description 

A totally useful collection of helper Shortcodes to make your life as a Site Builder and non-coder way easier and save you time: Shortcodes for Last Updated Date/ Time, Post Counter, Versions, Post Info, Copyright and Footer Info, Users, Reusable Blocks (Gutenberg), Elementor Templates, Genesis Footer and even more.

These are Shortcodes you don't get anywhere else. These are smart and really helpful - perfect for Non-Coder Designers, for Non Techies, for Site Builders. Those small parts and strings that make your life easier, now available as Shortcodes. Use them anywhere in WordPress: in Gutenberg Block Editor, in Classic Editor, Widgets, Post Types, Page Builders (Widgets, Elements...) and layout/ hook post types from various (Premium) Themes.

**This is my personaly story, why this plugin here was born:** When building a site and for example you need the date when a post type item was last updated? You just need this value as a simple string to put in a page builder widget or somewhere else, right? So where do you find the code snippet for it? Or a plugin? And you need it now, easy and fast?  
I was there, I know exactly how it feels at 02.00 a.m. in the middle of night. So this Shortcode collection was born. Totally simple. No styles, no scripts. But included CSS classes so I can style it my own - or apply my own classes or those from my theme. And I can change the HTML wrapper tag. Problem solved. It only took 2 minutes: 1 minute of installing and activing this plugin, plus 1 minute of inserting the actual Shortcode. Problem solved. Now I can go to sleep. With peace of mind. And the presentation the next morning for my client will be a success. Oh yeah, I love it! ;-)


### ♥️ What the Plugin does? 
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


### 🚀 Typical Use Cases of this Plugin 
1️⃣ **First example:** You want a footer copyright with the current year as dynamic - so you set it once and forget. Our Shortcode `[bse-copyright]` does exactly that.  
2️⃣ **Second example:** You want to put a "Last updated" date under each single post or page - use our Shortcode `[bse-post-modified-date]`, set your custom date format, set a custom label and place it into a (footer) widget. Done.  
3️⃣ **Third example:** You are using Elementor free, *not* Pro, and want to display one of your templates via a Shortcode? Now you can! (with our Shortcode `[bse-elementor-template]`)  
4️⃣ **Fourth example:** You want to display a Reusable Block via a Shortcode? Now you can! (with our Shortcode `[bse-wpblock]`)


### 🍰 Available Shortcodes: 
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


### 🍰 Available Integrations - Shortcodes: 
* *Gutenberg Block Editor, Reusable Blocks:* **`[bse-wpblock]`** -- attributes: id
* *Elementor free version:* **`[bse-elementor-template]`** -- attributes: id, css
* *Astra Custom Layouts (via Astra Pro):* **`[bse-astra-layout]`** -- attributes: id
* *Genesis Framework v3.1.0 or higher:* **`[bse-genesis-footer]`** -- attributes: wrapper, class
* *Genesis Framework:* **`[bse-genesis-breadcrumbs]`** -- attributes: wrapper, class
* **Note:** All these integrations are optional, only if the supported plugin or theme is installed and currently active. For the Block Editor there is smart logic in place recognizing popular "disable" plugins like "Classic Editor", "Disable Gutenberg" amongst others.

---

## Frequently Asked Questions 


### How does styling work? 
The plugin itself loads NO styles or scripts for the Shortcodes! It only adds very minimal markup by default (HTML span tag), with one specific class for the Shortcode. It's **completely up to you** to style these default classes (if you want) or add your own. -- In most cases the Shortcodes put out only simple strings - so, when used within regular text paragraphs you won't see any difference compared to the text around. But, if you need some special styling you can always add it and tweak your styles.



### What does the Gutenberg Block Editor Shortcode do? 
It can display any Reusable Block from WordPress Core Reusable Blocks (post type: `wp_block`) via a Shortcode. Just copy the Shortcode for a certain Reusable Block from the post type list table - look for the "Blocks" left-hand admin menu. This will make the Reusable Blocks way more useful and you can use them everyhwere - without coding!



### What does the Astra Layout Shortcode do? 
It can display any Astra Custom Layout via a Shortcode. Just copy the Shortcode for a certain Astra Custom Layout from the post type list table - look for the "Custom Layouts" under the left-hand "Appearance" admin menu. This will make those Custom Layouts even more useful and you can use them everyhwere - without coding! This Shortcode feature comes in handy especially for "Hook" type Shortcodes: you don't need to specify a Hook if used via Shortcode. This makes the Astra Custom Layouts more independent from Hooks and gives you even more possibilities.



### What does the Elementor Template Shortcode do? 
It displays the a template from Elementor "Saved Templates" (former "My Templates") library via a Shortcode. It is the same thing Elementor Pro does - but our plugin brings this feature to Elementor free version. This will make Elementor free version way more useful as this brings so many new possibilities to work with Templates!



### What are the two Genesis Shortcodes doing? 
**Genesis Footer Shortcode** - It takes the whole content block of your Genesis Footer settings from the Customizer - since Genesis 3.1.0 or higher - and displays it wherever you want. That way it becomes totally easy to customize your footer info text even if you want to show it somewhere else than the default location. It is easier than to use hooks or code for that.  

**Genesis Breadcrumbs Shortcode** - It takes the whole content block of Breadcrumbs for a current item/view and displays it wherever you want via Shortcode. This could be useful in some situations. It can make you a bit more independent from hooks and coding work. Please note: At the time being it is not possible to tweak the Breadcrumb arguments via the Shortcode!



### Can I use these Shortcodes in my Page Builder? 
Yes, of course! Everywhere a Shortcode is working in your Page Builder, our plugin's Shortcodes will also work. For more info on that you might also have a look in the documentation of your favorite Page Builder or similar plugin about Shortcode support for that tool.



### Does this Plugin work with Gutenberg / WordPress 5.0+ / Block Editor? 
Yes, of course! - The plugin is fully compatible with Gutenberg Block Editor: you can use Shortcodes almost everywhere within the Block Editor, not just in the so-called "Shortcode" block but also in Paragraph, List, Heading, whereever you can enter regular text, most likely Shortcodes will also be rendered. And, for our type of Shortcodes this makes total sense, for example to display a "last updated date", a post counter, a version number, some user info field, whatever. Just try it out.



### Does this Plugin work with Classic Editor and even ClassicPress? 
Yes, this plugin works with the Classic Editor plugin perfectly fine.

By default the plugin is also fully compatible with ClassicPress. I will try my best to also have my plugin work perfectly in ClassicPress, the fork of WordPress without Gutenberg. It should already be fully compatible but I will follow all events closely to adjust compat if needed.



### Will this Plugin slow down my site? 
Absolutely not. Since the plugin is NOT loading any scripts or styles all is really lightweight. A no brainer to install and use.



### Does the Plugin work with Multisite? 
Yes, it works fine in Multisite, you could even activate it Network-wide. You can use the Shortcodes in each sub site like you would in a regular (single) install.



### Will other third-party Plugins be supported? 
Only if it makes sense, like the current Elementor Template Shortcode for example. Remember: we don't want to load any scripts or styles. So, if a potential Shortcode fits our philosophy and could be totally useful, then the chance of adding it to this plugin gets very high! ;-)

If you have any suggestion for such a Shortcode, please feel free to [**open a new GitHub Issue**](https://github.com/deckerweb/builder-shortcode-extras/issues) in our development repository and tell us all about it. Thanks in advance!

---

## Screenshots 

### 1. Use the Shortcodes in Gutenberg Block Editor: Shortcode Block, Paragraph Block, List Block, Heading Block etc.
![Use the Shortcodes in Gutenberg Block Editor: Shortcode Block, Paragraph Block, List Block, Heading Block etc.](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-1.png)


### 2. The Shortcodes from Block Editor rendered on the Frontend
![The Shortcodes from Block Editor rendered on the Frontend](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-2.png)


### 3. Shortcode for each Reusable Block to insert & display anywhere (especially outside of the Block Editor...)
![Shortcode for each Reusable Block to insert & display anywhere (especially outside of the Block Editor...)](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-3.png)


### 4. Use the Shortcodes in Classic Editor as well: Visual Mode or Text Mode (HTML) - doesn't matter, will work
![Use the Shortcodes in Classic Editor as well: Visual Mode or Text Mode (HTML) - doesn't matter, will work](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-4.png)


### 5. Insert a Shortcode into Text Widget or Custom HTML Widget
![Insert a Shortcode into Text Widget or Custom HTML Widget](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-5.png)


### 6. For Elementor Free Version: Shortcode for each Saved Template!
![For Elementor Free Version: Shortcode for each Saved Template!](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-6.png)


### 7. Insert a Shortcode into Elementor Page Builder: Shortcode Widget
![Insert a Shortcode into Elementor Page Builder: Shortcode Widget](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-7.png)


### 8. Insert a Shortcode into Elementor Text Editor Widget
![Insert a Shortcode into Elementor Text Editor Widget](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-8.png)


### 9. Insert a Shortcode into Elementor Heading widget (will be rendered fully on the frontend, of course!)
![Insert a Shortcode into Elementor Heading widget (will be rendered fully on the frontend, of course!)](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-9.png)


### 10. For Astra Pro: Shortcode for each Astra Custom Layout - great for reusage
![For Astra Pro: Shortcode for each Astra Custom Layout - great for reusage](https://raw.githubusercontent.com/deckerweb/builder-shortcode-extras/master/assets-repos/wordpress-org/screenshot-10.png)

---

## Changelog 

### 🎉 v1.2.0 – 2025-04-??
* Improved: Better compatibility for [_Git Updater_](https://git-updater.com/) integration
* Change: Re-added translations loader as translations display is otherwise not optimal for non-.org hosted plugins
* Update: `.pot` file, plus packaged German translations, now including new `l10n.php` files!

### 🎉 v1.1.0 – 2025-3-15
* Bring back the plugin to a workable state
* Removed all wordpress.org stuff
* Removed the _DDWlib Plugin Installer Recommendations_ library got removed, it is no longer wanted/supported anyways
* Removed own translations loader; WordPress now does it all itself; the packaged translations remain :)
* **Note:** No longer in the .org plugin repo available – thanks to Matt... (I've taken it out myself as I have no longer interest in WordPress.org repo strategy) – just install it yourself via ZIP file, see above under "Installation"


### 🎉 1.0.0 - 2019-09-10 
* *Plugin launch. Everything's new!*
* New: 25 general Shortcodes
* New: 5 Shortcodes for Integrations


### ⚡ 0.9.0 - 2019-09-09 
* New: Beta release of the plugin on [its public GitHub repository](https://github.com/deckerweb/builder-shortcode-extras)


## ☺️ Donate 
Enjoy using *Builder Shortcode Extras*? **[Please consider making a donation](https://www.paypal.me/deckerweb)** - every donation helps to support the project's continued development, maintenance and support.
**Thank you very much in advance for your support!**

---

## Plugin Scope / Disclaimer

This plugin comes as is.

_Disclaimer 1:_ So far I will support the plugin for breaking errors to keep it working. Otherwise support will be very limited. Also, it will NEVER be released to WordPress.org Plugin Repository for a lot of reasons (ah, thanks, Matt!).

_Disclaimer 2:_ All of the above might change. I do all this stuff only in my spare time.

_Most of all:_ Have fun building great sites!!! ;-)

---

Icon used in promo graphics: [© Tabler Icons by Paweł Kuna](https://tabler.io/icons)

Readme & Plugin Copyright: © 2019–2025, David Decker – DECKERWEB.de