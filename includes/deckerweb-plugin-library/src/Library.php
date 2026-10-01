<?php
namespace Deckerweb\PluginLibrary\V0_2_0;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** WordPress-native, embedded catalog. Settings are shared across host plugins. */
final class Library {
	const VERSION = '0.2.0';
	const OPTION = 'deckerweb_library_settings_v1';
	const MANAGED = 'deckerweb_library_installed_v1';
	private array $chosen;
	private array $hosts;
	private Catalog $catalog;
	private bool $network = false;
	public function __construct( array $chosen, array $hosts ) {
		$this->chosen = $chosen; $this->hosts = $hosts;
		$this->catalog = new Catalog( $chosen['dir'] );
	}

	/** English source and a small built-in German dictionary, independent of host domains. */
	public static function t( string $en, string $de ): string {
		return str_starts_with( determine_locale(), 'de' ) ? $de : $en;
	}
	public static function settings(): array {
		$saved = get_site_option( self::OPTION, [] );
		$saved = is_array( $saved ) ? $saved : [];
		return [ 'enabled' => ! isset( $saved['enabled'] ) || ! empty( $saved['enabled'] ), 'online' => ! empty( $saved['online'] ), 'catalog_url' => is_string( $saved['catalog_url'] ?? null ) ? $saved['catalog_url'] : '' ];
	}
	public function register(): void {
		add_filter( 'install_plugins_tabs', [ $this, 'tabs' ] );
		add_action( 'install_plugins_deckerweb', [ $this, 'render' ] );
		add_action( 'admin_notices', [ $this, 'notice' ] );
		add_action( 'network_admin_notices', [ $this, 'notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		add_action( 'admin_menu', [ $this, 'menu' ] );
		add_action( 'network_admin_menu', [ $this, 'menu' ] );
		add_action( 'admin_post_dwl_preferences', [ $this, 'preferences' ] );
		add_action( 'admin_post_dwl_dismiss', [ $this, 'dismiss' ] );
		add_action( 'admin_post_dwl_install', [ $this, 'install' ] );
		add_action( 'admin_post_dwl_activate', [ $this, 'activate' ] );
		add_action( 'admin_post_dwl_refresh', [ $this, 'refresh' ] );
		add_action( 'activate_plugin', [ $this, 'activation_guard' ], 0, 2 );
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'updates' ] );
		add_filter( 'upgrader_pre_download', [ $this, 'update_download' ], 10, 4 );
		foreach ( $this->hosts as $host ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $host['host'] ), [ $this, 'links' ] );
			add_filter( 'network_admin_plugin_action_links_' . plugin_basename( $host['host'] ), [ $this, 'links' ] );
		}
	}
	public function links( array $links ): array {
		if ( current_user_can( 'install_plugins' ) ) { $links['deckerweb_library'] = '<a href="' . esc_url( self_admin_url( 'plugin-install.php?tab=deckerweb' ) ) . '">' . esc_html( self::t( 'More by deckerweb', 'Mehr von deckerweb' ) ) . '</a>'; }
		return $links;
	}
	public function tabs( array $tabs ): array {
		if ( self::settings()['enabled'] && current_user_can( 'install_plugins' ) ) { $tabs['deckerweb'] = 'deckerweb'; }
		return $tabs;
	}
	public function assets( string $hook ): void {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		if ( $hook === 'plugin-install.php' && ( $_GET['tab'] ?? '' ) === 'deckerweb' ) {
			wp_enqueue_style( 'deckerweb-plugin-library', plugins_url( 'assets/library.css', $this->chosen['dir'] . '/bootstrap.php' ), [], self::VERSION );
		}
	}
	public function notice(): void {
		global $pagenow;
		if ( $pagenow !== 'plugin-install.php' || ( $_GET['tab'] ?? '' ) === 'deckerweb' || ! self::settings()['enabled'] || ! current_user_can( 'install_plugins' ) || get_user_meta( get_current_user_id(), 'deckerweb_library_intro_seen_v1', true ) ) { return; }
		// Once per administrator, not once per request and not once per host plugin.
		update_user_meta( get_current_user_id(), 'deckerweb_library_intro_seen_v1', 1 );
		echo '<div class="notice notice-info"><p><strong>' . esc_html( self::t( 'Discover more plugins by deckerweb', 'Weitere Plugins von deckerweb entdecken' ) ) . '</strong><br>' . esc_html( self::t( 'You already use one of my plugins. Find selected additions in the deckerweb tab.', 'Du nutzt bereits ein Plugin von mir. Ausgewählte Ergänzungen findest du im deckerweb-Tab.' ) ) . ' <a href="' . esc_url( self_admin_url( 'plugin-install.php?tab=deckerweb' ) ) . '">' . esc_html( self::t( 'View plugins', 'Plugins ansehen' ) ) . '</a></p>';
		$this->form( 'dismiss', '', self::t( 'Dismiss permanently', 'Dauerhaft schließen' ), 'button-link', [ 'location' => 'installer' ] );
		echo '<p></p></div>';
	}
	public function menu(): void {
		$cap = is_network_admin() ? 'manage_network_options' : 'manage_options';
		if ( is_multisite() && ! is_network_admin() ) { return; }
		add_submenu_page( is_network_admin() ? 'settings.php' : 'options-general.php', 'deckerweb Library', 'deckerweb Library', $cap, 'deckerweb-library', [ $this, 'settings_page' ] );
	}
	private function catalog_url(): string { return ( $this->network ? network_admin_url( 'plugin-install.php?tab=deckerweb' ) : self_admin_url( 'plugin-install.php?tab=deckerweb' ) ); }
	private function settings_url(): string { return is_multisite() ? network_admin_url( 'settings.php?page=deckerweb-library' ) : admin_url( 'options-general.php?page=deckerweb-library' ); }
	private function action_url( string $action, string $slug = '' ): string {
		return add_query_arg( [ 'action' => 'dwl_' . $action, 'slug' => $slug, 'network' => ( is_network_admin() || $this->network ) ? '1' : '0' ], admin_url( 'admin-post.php' ) );
	}
	private function form( string $action, string $slug, string $label, string $class = 'button', array $extra = [] ): void {
		echo '<form method="post" action="' . esc_url( $this->action_url( $action, $slug ) ) . '" class="dwl-action">';
		wp_nonce_field( 'dwl_' . $action . '_' . $slug );
		foreach ( $extra as $key => $value ) { echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">'; }
		echo '<button type="submit" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</button></form>';
	}
	private static function text( array $entry, string $key ): string {
		return str_starts_with( determine_locale(), 'de' ) && isset( $entry[$key . '_de'] ) ? $entry[$key . '_de'] : $entry[$key];
	}

	private function icon( array $entry ): string {
		// Only bundled PNG files; catalog metadata never triggers remote image requests.
		$file = $entry['icon'] ?? '';
		if ( $file && is_file( $this->chosen['dir'] . '/' . $file ) ) {
			return '<img src="' . esc_url( plugins_url( $file, $this->chosen['dir'] . '/bootstrap.php' ) ) . '" alt="" width="54" height="54">';
		}
		return '<span class="dwl-icon-fallback" style="background-color:' . esc_attr( $entry['icon_background'] ?? '#f0f3f6' ) . '">' . esc_html( $entry['icon_label'] ?? 'DW' ) . '</span>';
	}
	public function render(): void {
		if ( ! current_user_can( 'install_plugins' ) ) { wp_die( esc_html( self::t( 'Permission denied.', 'Keine Berechtigung.' ) ), '', [ 'response' => 403 ] ); }
		if ( ! self::settings()['enabled'] ) {
			echo '<p>' . esc_html( self::t( 'The catalog is hidden.', 'Der Katalog ist ausgeblendet.' ) ) . ' <a href="' . esc_url( $this->settings_url() ) . '">' . esc_html( self::t( 'Library settings', 'Library-Einstellungen' ) ) . '</a></p>'; return;
		}
		update_user_meta( get_current_user_id(), 'deckerweb_library_intro_seen_v1', 1 );
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$entries = $this->catalog->entries();
		if ( is_wp_error( $entries ) ) { echo '<div class="notice notice-error"><p>' . esc_html( $entries->get_error_message() ) . '</p></div>'; return; }
		$plugins = get_plugins();
		$search = isset( $_GET['dwl_search'] ) && is_string( $_GET['dwl_search'] ) ? sanitize_text_field( wp_unslash( $_GET['dwl_search'] ) ) : '';
		$compatible = ! empty( $_GET['dwl_compatible'] );
		echo '<div id="deckerweb-library"><div class="dwl-heading"><div><h2>' . esc_html( self::t( 'Small plugins. Practical improvements.', 'Kleine Plugins. Konkrete Verbesserungen.' ) ) . '</h2><p>' . esc_html( self::t( 'Selected by deckerweb · Installed from GitHub releases', 'Von deckerweb ausgewählt · Installation aus GitHub-Releases' ) ) . '</p></div><a href="' . esc_url( $this->settings_url() ) . '">' . esc_html( self::t( 'Library settings', 'Library-Einstellungen' ) ) . '</a></div>';
		if ( isset( $_GET['dwl_done'] ) ) { echo '<div class="notice notice-success inline"><p>' . esc_html( self::t( 'Done. The plugin is active.', 'Erledigt. Das Plugin ist aktiv.' ) ) . '</p></div>'; }
		if ( $this->catalog->status === 'offline' ) { echo '<div class="notice notice-warning inline"><p>' . esc_html( self::t( 'The online catalog is unavailable. Showing cached or bundled entries; online approval is checked again before installation.', 'Der Online-Katalog ist nicht erreichbar. Angezeigt wird der gespeicherte oder mitgelieferte Katalog; vor der Installation wird die Online-Freigabe erneut geprüft.' ) ) . '</p></div>'; }
		echo '<form method="get" class="dwl-filter"><input type="hidden" name="tab" value="deckerweb"><label>' . esc_html( self::t( 'Search this catalog', 'Diesen Katalog durchsuchen' ) ) . ' <input type="search" name="dwl_search" value="' . esc_attr( $search ) . '"></label><label><input type="checkbox" name="dwl_compatible" value="1" ' . checked( $compatible, true, false ) . '> ' . esc_html( self::t( 'Compatible additions only', 'Nur passende Ergänzungen' ) ) . '</label><button class="button">' . esc_html( self::t( 'Filter', 'Filtern' ) ) . '</button></form>';
		echo '<div class="dwl-grid">'; $count = 0;
		foreach ( $entries as $entry ) {
			$name = self::text( $entry, 'name' ); $description = self::text( $entry, 'description' );
			$issues = Requirements::check( $entry, $plugins );
			if ( $search !== '' && stripos( remove_accents( $name . ' ' . $description ), remove_accents( $search ) ) === false ) { continue; }
			if ( $compatible && $issues ) { continue; }
			$count++;
			$installed = isset( $plugins[$entry['plugin_file']] );
			$active = $installed && ( is_network_admin() ? is_plugin_active_for_network( $entry['plugin_file'] ) : is_plugin_active( $entry['plugin_file'] ) );
			echo '<article class="dwl-card"><div class="dwl-body"><div class="dwl-title"><div class="dwl-icon" aria-hidden="true">' . $this->icon( $entry ) . '</div><div><h3>' . esc_html( $name ) . '</h3><span class="dwl-meta">deckerweb · ' . esc_html( $entry['category'] ?? 'WordPress' ) . '</span></div></div><p>' . esc_html( $description ) . '</p>';

			if ( isset( $entry['github_stars'], $entry['stars_checked_at'] ) ) {
				echo '<p class="dwl-stars"><a href="' . esc_url( 'https://github.com/' . $entry['repository'] . '/stargazers' ) . '" target="_blank" rel="noopener noreferrer"><span aria-hidden="true">★</span> ' . esc_html( number_format_i18n( $entry['github_stars'] ) . ' GitHub Stars' ) . '</a> <span class="dwl-meta">' . esc_html( self::t( 'As of ', 'Stand: ' ) . $entry['stars_checked_at'] ) . '</span></p>';
			}
			if ( $active ) { echo '<span class="dwl-active">' . esc_html( self::t( 'Already active', 'Bereits aktiv' ) ) . '</span>'; }
			elseif ( $issues ) { echo '<button class="button" disabled>' . esc_html( $installed ? self::t( 'Activate', 'Aktivieren' ) : self::t( 'Install now', 'Jetzt installieren' ) ) . '</button>'; }
			elseif ( $installed && current_user_can( 'activate_plugin', $entry['plugin_file'] ) ) { $this->form( 'activate', $entry['slug'], is_network_admin() ? self::t( 'Network activate', 'Netzwerkweit aktivieren' ) : self::t( 'Activate', 'Aktivieren' ), 'button button-primary' ); }
			elseif ( ! $installed ) { $this->form( 'install', $entry['slug'], self::t( 'Install now', 'Jetzt installieren' ), 'button' ); }
			else { echo '<span>' . esc_html( self::t( 'Installed', 'Installiert' ) ) . '</span>'; }
			if ( $issues ) { echo '<div class="dwl-requirements"><ul>'; foreach ( $issues as $issue ) { echo '<li>' . esc_html( $issue ) . '</li>'; } echo '</ul><a href="' . esc_url( self_admin_url( 'plugins.php' ) ) . '">' . esc_html( self::t( 'Manage installed plugins', 'Installierte Plugins verwalten' ) ) . '</a></div>'; }
			echo '<details class="dwl-details"><summary>' . esc_html( self::t( 'Details & requirements', 'Details & Voraussetzungen' ) ) . '</summary><p>WordPress ≥ ' . esc_html( $entry['requires_wp'] ) . ' · PHP ≥ ' . esc_html( $entry['requires_php'] ) . '</p>';
			foreach ( $entry['dependencies'] as $dep ) { echo '<p>' . esc_html( $dep['name'] . ( $dep['min_version'] !== '' ? ' ≥ ' . $dep['min_version'] : '' ) ) . ( isset( $dep['url'] ) ? ' · <a href="' . esc_url( $dep['url'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( self::t( 'Product website', 'Produktwebsite' ) ) . '</a>' : '' ) . '</p>'; }
			echo '<p><a href="' . esc_url( 'https://github.com/' . $entry['repository'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( self::t( 'Repository & documentation', 'Repository & Dokumentation' ) ) . '</a></p><p class="dwl-meta">' . esc_html( self::t( 'Release checksum is verified before installation. Activation is a separate step.', 'Vor der Installation wird die Release-Prüfsumme geprüft. Die Aktivierung erfolgt separat.' ) ) . '</p></details></div><footer><span>' . esc_html( self::t( 'Source: GitHub', 'Quelle: GitHub' ) ) . '</span><span>' . esc_html( $installed ? self::t( 'Installed: ', 'Installiert: ' ) . $plugins[$entry['plugin_file']]['Version'] : 'v' . $entry['version'] ) . '</span></footer></article>';
		}
		echo '</div>';
		if ( ! $count ) { echo '<p>' . esc_html( self::t( 'No approved plugins match this selection.', 'Keine freigegebenen Plugins passen zu dieser Auswahl.' ) ) . '</p>'; }
		echo '<div class="dwl-bottom"><span>' . esc_html( self::t( 'Library', 'Library' ) ) . ' ' . esc_html( self::VERSION ) . ' · ' . esc_html( self::t( 'Only approved releases', 'Nur freigegebene Releases' ) ) . '</span>';
		$this->form( 'preferences', '', self::t( 'Hide catalog', 'Katalog ausblenden' ), 'button-link', [ 'enabled' => '0', 'hide_only' => '1' ] );
		if ( self::settings()['online'] ) { $this->form( 'refresh', '', self::t( 'Refresh catalog', 'Katalog aktualisieren' ), 'button-link' ); }
		echo '</div></div>';
	}

	public function settings_page(): void {
		$cap = is_multisite() ? 'manage_network_options' : 'manage_options';
		if ( ! current_user_can( $cap ) ) { wp_die( 'Permission denied.', '', [ 'response' => 403 ] ); }
		$s = self::settings();
		echo '<div class="wrap"><h1>deckerweb Library</h1>';
		if ( isset( $_GET['saved'] ) ) { echo '<div class="notice notice-success"><p>' . esc_html( self::t( 'Library settings saved.', 'Library-Einstellungen gespeichert.' ) ) . '</p></div>'; }
		echo '<form method="post" action="' . esc_url( $this->action_url( 'preferences' ) ) . '">'; wp_nonce_field( 'dwl_preferences_' );
		echo '<table class="form-table" role="presentation"><tr><th scope="row">' . esc_html( self::t( 'Visibility', 'Sichtbarkeit' ) ) . '</th><td><label><input type="checkbox" name="enabled" value="1" ' . checked( $s['enabled'], true, false ) . '> ' . esc_html( self::t( 'Show deckerweb under Add Plugins', 'deckerweb unter „Plugins hinzufügen“ anzeigen' ) ) . '</label></td></tr>';
		echo '<tr><th scope="row">' . esc_html( self::t( 'Online catalog', 'Online-Katalog' ) ) . '</th><td><label><input type="checkbox" name="online" value="1" ' . checked( $s['online'], true, false ) . '> ' . esc_html( self::t( 'Retrieve approved catalog updates online', 'Katalogfreigaben online aktualisieren' ) ) . '</label><p class="description">' . esc_html( self::t( 'Optional. The bundled catalog works immediately. Online requests are cached for 12 hours. No site URL, plugin list or usage data is sent.', 'Optional. Der mitgelieferte Katalog funktioniert sofort. Online-Anfragen werden 12 Stunden gespeichert. Website-URL, Plugin-Liste und Nutzungsdaten werden nicht übertragen.' ) ) . '</p><p><label for="dwl-catalog-url">' . esc_html( self::t( 'Catalog URL', 'Katalog-URL' ) ) . '</label><br><input type="url" id="dwl-catalog-url" name="catalog_url" value="' . esc_attr( $s['catalog_url'] ) . '" class="regular-text" placeholder="https://…/catalog.json"></p><p class="description">' . esc_html( self::t( 'Only HTTPS on deckerweb.de or raw.githubusercontent.com/deckerweb. The server receives the requesting IP address. GitHub is contacted when installing a release.', 'Nur HTTPS auf deckerweb.de oder raw.githubusercontent.com/deckerweb. Der Server sieht die anfragende IP-Adresse. Beim Installieren eines Releases wird GitHub kontaktiert.' ) ) . '</p></td></tr></table>';
		submit_button( self::t( 'Save settings', 'Einstellungen speichern' ) ); echo '</form><p><a href="' . esc_url( self_admin_url( 'plugin-install.php?tab=deckerweb' ) ) . '">' . esc_html( self::t( 'Open catalog', 'Katalog öffnen' ) ) . '</a></p></div>';
	}
	private function authorize( string $action, string $cap, string $slug = '' ): void {
		if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'POST' ) { wp_die( 'POST required.', '', [ 'response' => 405 ] ); }
		if ( ! current_user_can( $cap ) ) { wp_die( 'Permission denied.', '', [ 'response' => 403 ] ); }
		$this->network = is_multisite() && ( $_REQUEST['network'] ?? '' ) === '1';
		if ( $this->network && ! current_user_can( 'manage_network_plugins' ) ) { wp_die( 'Network permission denied.', '', [ 'response' => 403 ] ); }
		check_admin_referer( 'dwl_' . $action . '_' . $slug );
	}
	private function slug(): string { return isset( $_REQUEST['slug'] ) && is_string( $_REQUEST['slug'] ) ? sanitize_key( wp_unslash( $_REQUEST['slug'] ) ) : ''; }
	private function entry( string $slug, bool $fresh = true ): array {
		if ( ! self::settings()['enabled'] ) { wp_die( esc_html( self::t( 'The catalog is disabled.', 'Der Katalog ist deaktiviert.' ) ), '', [ 'response' => 403 ] ); }
		$entries = $this->catalog->entries( $fresh );
		if ( is_wp_error( $entries ) ) { wp_die( esc_html( $entries->get_error_message() ) ); }
		if ( ! isset( $entries[$slug] ) ) { wp_die( esc_html( self::t( 'This release is not approved.', 'Dieses Release ist nicht freigegeben.' ) ), '', [ 'response' => 404 ] ); }
		return $entries[$slug];
	}
	private function enforce_requirements( array $entry, ?bool $network = null ): void {
		$issues = Requirements::check( $entry, null, $network ?? $this->network );
		if ( $issues ) { wp_die( esc_html( implode( ' ', $issues ) ) ); }
	}
	public function preferences(): void {
		$this->authorize( 'preferences', is_multisite() ? 'manage_network_options' : 'manage_options' );
		$s = self::settings();
		if ( ! empty( $_POST['hide_only'] ) ) { $s['enabled'] = false; }
		else {
			$url = isset( $_POST['catalog_url'] ) && is_string( $_POST['catalog_url'] ) ? trim( wp_unslash( $_POST['catalog_url'] ) ) : '';
			if ( $url !== '' && ! Catalog::trusted_source( $url ) ) { wp_die( esc_html( self::t( 'The catalog URL is not an allowed first-party HTTPS endpoint.', 'Die Katalog-URL ist keine erlaubte eigene HTTPS-Quelle.' ) ) ); }
			if ( ! empty( $_POST['online'] ) && $url === '' ) { wp_die( esc_html( self::t( 'Enter a catalog URL first.', 'Bitte zuerst eine Katalog-URL eintragen.' ) ) ); }
			$s = [ 'enabled' => ! empty( $_POST['enabled'] ), 'online' => ! empty( $_POST['online'] ), 'catalog_url' => $url ];
		}
		update_site_option( self::OPTION, $s );
		wp_safe_redirect( add_query_arg( 'saved', '1', $this->settings_url() ) ); exit;
	}
	public function dismiss(): void {
		$this->authorize( 'dismiss', 'install_plugins' );
		update_user_meta( get_current_user_id(), 'deckerweb_library_intro_seen_v1', 1 );
		wp_safe_redirect( $this->network ? network_admin_url( 'plugin-install.php' ) : admin_url( 'plugin-install.php' ) ); exit;
	}
	public function refresh(): void {
		$this->authorize( 'refresh', 'install_plugins' );
		$result = $this->catalog->entries( true );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ) ); }
		wp_safe_redirect( $this->catalog_url() ); exit;
	}
	public function install(): void {
		$slug = $this->slug(); $this->authorize( 'install', 'install_plugins', $slug );
		$entry = $this->entry( $slug ); $this->enforce_requirements( $entry );
		if ( is_dir( WP_PLUGIN_DIR . '/' . $entry['slug'] ) ) { wp_die( esc_html( self::t( 'This plugin directory already exists. Use the installed plugin; it will not be overwritten.', 'Dieses Plugin-Verzeichnis existiert bereits. Bitte das installierte Plugin verwenden; es wird nicht überschrieben.' ) ) ); }
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$title = self::t( 'Install deckerweb plugin', 'deckerweb-Plugin installieren' );
		if ( $this->network && ! defined( 'WP_NETWORK_ADMIN' ) ) { define( 'WP_NETWORK_ADMIN', true ); }
		// admin-post.php has no initialized sidebar/menu. Use WordPress's native
		// standalone upgrader chrome instead of including admin-header prematurely.
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		$GLOBALS['hook_suffix'] = 'deckerweb-library-install';
		set_current_screen( $this->network ? 'plugin-install-network' : 'plugin-install' );
		wp_enqueue_style( 'common' ); wp_enqueue_style( 'forms' );
		wp_enqueue_style( 'deckerweb-plugin-library', plugins_url( 'assets/library.css', $this->chosen['dir'] . '/bootstrap.php' ), [], self::VERSION );
		$body_class = static fn( string $classes ): string => $classes . ' dwl-install';
		add_filter( 'admin_body_class', $body_class );
		iframe_header( esc_html( $title ) );
		remove_filter( 'admin_body_class', $body_class );
		echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1>';
		$url = wp_nonce_url( $this->action_url( 'install', $slug ), 'dwl_install_' . $slug );
		$credentials = request_filesystem_credentials( $url, '', false, WP_PLUGIN_DIR );
		if ( $credentials === false ) { echo '</div>'; iframe_footer(); return; }
		if ( ! WP_Filesystem( $credentials, WP_PLUGIN_DIR ) ) { request_filesystem_credentials( $url, '', true, WP_PLUGIN_DIR ); echo '</div>'; iframe_footer(); return; }
		$package = Package::download( $entry );
		if ( is_wp_error( $package ) ) { echo '<div class="notice notice-error"><p>' . esc_html( $package->get_error_message() ) . '</p></div>'; }
		else {
			$skin = new class( [ 'type' => 'web', 'url' => $url, 'nonce' => 'dwl_install_' . $slug, 'title' => $title, 'api' => (object) [ 'name' => $entry['name'], 'slug' => $entry['slug'], 'version' => $entry['version'] ] ] ) extends \Plugin_Installer_Skin {
				// The standalone page already owns its heading and wrapper.
				public function header() {}
				public function footer() {}
			};
			$upgrader = new \Plugin_Upgrader( $skin );
			$actions = function( array $links, $api, string $file ) use ( $entry ): array {
				unset( $links['activate_plugin'], $links['network_activate'] );
				if ( $file === $entry['plugin_file'] && ! Requirements::check( $entry, null, $this->network ) && current_user_can( 'activate_plugin', $file ) ) {
					ob_start(); $this->form( 'activate', $entry['slug'], $this->network ? self::t( 'Network activate', 'Netzwerkweit aktivieren' ) : self::t( 'Activate', 'Aktivieren' ), 'button button-primary' ); $links['dwl_activate'] = ob_get_clean();
				}
				$links['dwl_catalog'] = '<a href="' . esc_url( $this->catalog_url() ) . '">' . esc_html( self::t( 'Back to deckerweb', 'Zurück zu deckerweb' ) ) . '</a>';
				return $links;
			};
			add_filter( 'install_plugin_complete_actions', $actions, 10, 3 );
			try { $result = $upgrader->install( $package, [ 'clear_update_cache' => false, 'overwrite_package' => false ] ); }
			finally { remove_filter( 'install_plugin_complete_actions', $actions, 10 ); wp_delete_file( $package ); }
			if ( $result === true ) {
				$managed = get_site_option( self::MANAGED, [] ); $managed = is_array( $managed ) ? $managed : [];
				$managed[$entry['plugin_file']] = $entry['repository']; update_site_option( self::MANAGED, $managed );
			}
		}
		echo '</div>'; iframe_footer();
	}
	public function activate(): void {
		$slug = $this->slug(); $this->authorize( 'activate', 'activate_plugins', $slug );
		$entry = $this->entry( $slug );
		if ( ! current_user_can( 'activate_plugin', $entry['plugin_file'] ) ) { wp_die( 'Permission denied.', '', [ 'response' => 403 ] ); }
		$this->enforce_requirements( $entry );
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! is_file( WP_PLUGIN_DIR . '/' . $entry['plugin_file'] ) ) { wp_die( 'Plugin is not installed.' ); }
		$result = activate_plugin( $entry['plugin_file'], '', $this->network, false );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ) ); }
		wp_safe_redirect( add_query_arg( 'dwl_done', '1', $this->catalog_url() ) ); exit;
	}
	/** Cover activation from the ordinary Plugins page too, while this library is loaded. */
	public function activation_guard( string $file, bool $network ): void {
		$managed = get_site_option( self::MANAGED, [] );
		$bundled = json_decode( (string) file_get_contents( $this->chosen['dir'] . '/catalog.json' ), true );
		$known = array_column( $bundled['plugins'] ?? [], 'plugin_file' );
		if ( ! in_array( $file, $known, true ) && ! isset( $managed[$file] ) ) { return; }
		// Hiding discovery must never remove runtime dependency protection.
		$entries = $this->catalog->entries();
		if ( is_wp_error( $entries ) ) { return; }
		foreach ( $entries as $entry ) { if ( $entry['plugin_file'] === $file ) { $this->enforce_requirements( $entry, $network ); return; } }
	}
	/** Only installed-through-library plugins lacking their own updater are managed here. */
	public function updates( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) { return $transient; }
		$managed = get_site_option( self::MANAGED, [] );
		if ( ! is_array( $managed ) || ! $managed ) { return $transient; }
		$entries = $this->catalog->entries();
		if ( is_wp_error( $entries ) ) { return $transient; }
		require_once ABSPATH . 'wp-admin/includes/plugin.php'; $plugins = get_plugins();
		foreach ( $entries as $e ) {
			$file = $e['plugin_file'];
			if ( ! isset( $managed[$file], $plugins[$file], $transient->checked[$file] ) || $managed[$file] !== $e['repository'] || ! empty( $plugins[$file]['UpdateURI'] ) || isset( $transient->response[$file] ) || ! version_compare( $e['version'], $transient->checked[$file], '>' ) ) { continue; }
			$transient->response[$file] = (object) [ 'id' => 'https://github.com/' . $e['repository'], 'slug' => $e['slug'], 'plugin' => $file, 'new_version' => $e['version'], 'url' => 'https://github.com/' . $e['repository'], 'package' => $e['download_url'], 'requires' => $e['requires_wp'], 'requires_php' => $e['requires_php'], 'dwl_managed' => true ];
		}
		return $transient;
	}
	/** Native update downloads retain the same hash, archive and dependency checks. */
	public function update_download( $reply, string $package, $upgrader, array $extra = [] ) {
		$file = $extra['plugin'] ?? '';
		$managed = get_site_option( self::MANAGED, [] );
		if ( ! is_string( $file ) || ! is_array( $managed ) || ! isset( $managed[$file] ) ) { return $reply; }
		$entries = $this->catalog->entries( true );
		if ( is_wp_error( $entries ) ) { return $entries; }
		foreach ( $entries as $entry ) {
			if ( $entry['plugin_file'] !== $file || $entry['repository'] !== $managed[$file] ) { continue; }
			// A native updater owned by the plugin must remain in control of its releases.
			$plugins = get_plugins();
			if ( ! empty( $plugins[$file]['UpdateURI'] ) ) { return $reply; }
			if ( $package !== $entry['download_url'] ) { return new \WP_Error( 'dwl_release_changed', self::t( 'The approved release changed. Refresh updates first.', 'Das freigegebene Release hat sich geändert. Bitte zuerst die Updates aktualisieren.' ) ); }
			$issues = Requirements::check( $entry, null, is_plugin_active_for_network( $file ) );
			if ( $issues ) { return new \WP_Error( 'dwl_requirements', implode( ' ', $issues ) ); }
			return Package::download( $entry );
		}
		return new \WP_Error( 'dwl_withdrawn', self::t( 'This release is no longer approved.', 'Dieses Release ist nicht mehr freigegeben.' ) );
	}
}
