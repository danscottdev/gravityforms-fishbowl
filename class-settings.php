<?php
/**
 * Manage admin settings -- allow wp-admin to edit various settings, should the need arise
 * Adds custom menu page to WP-Admin -> Settings -> Fishbowl Integration
 */
namespace GF_Fishbowl;

class Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_fishbowl_menu' ) );
		add_action( 'admin_init', [ $this, 'add_fishbowl_settings' ] );
	}

	public function add_fishbowl_menu() {
		add_options_page(
			'Fishbowl Integration',
			'Fishbowl Integration',
			'manage_options',
			'fishbowl-integration',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// WP Native functionality to add 'admin messages' to top of page, for success/error feedback.
		// When settings are saved this pops up twice. Not sure why. Not broken though. Investigate more if time allows.
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'fishbowl_messages', 'fishbowl_message', __( 'Settings Saved', 'fishbowl' ), 'updated' );
		}

		settings_errors( 'fishbowl_messages' );

		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">

				<?php
					settings_fields( 'fishbowl-option-group' );
					do_settings_sections( 'fishbowl-option-group' );
					submit_button( 'Save Fishbowl Settings' );
				?>

			</form>

			<hr/>

			Click here to <a href="https://app.delightable.com" target="_blank">go to Fishbowl/Delightable dashboard</a>
		</div>

		<?php

	}


	public function add_fishbowl_settings() {

		register_setting(
			'fishbowl-option-group',
			'fishbowl_integration_is_enabled'
		);
		register_setting(
			'fishbowl-option-group',
			'fishbowl_integration_source'
		);
		register_setting(
			'fishbowl-option-group',
			'fishbowl_brand_uuid'
		);
		register_setting(
			'fishbowl-option-group',
			'fishbowl_brand_schema'
		);
		register_setting(
			'fishbowl-option-group',
			'fishbowl_list_uuid'
		);

		add_settings_section(
			'fishbowl-section',
			'Fishbowl Integration Settings Section',
			null,
			'fishbowl-option-group'
		);

		add_settings_field(
			'fishbowl_integration_is_enabled',
			'Enable GravityForms -> Fishbowl Integration?',
			[ $this, 'fishbowl_integration_is_enabled_option' ],
			'fishbowl-option-group',
			'fishbowl-section',
		);

		add_settings_field(
			'fishbowl_integration_source',
			'Fishbowl User Source',
			[ $this, 'fishbowl_source_option' ],
			'fishbowl-option-group',
			'fishbowl-section',
		);

		add_settings_field(
			'fishbowl_brand_uuid',
			'Fishbowl Brand UUID',
			[ $this, 'fishbowl_brand_uuid_option' ],
			'fishbowl-option-group',
			'fishbowl-section',
		);

		add_settings_field(
			'fishbowl_brand_schema',
			'Fishbowl Brand Schema ID',
			[ $this, 'fishbowl_brand_schema_option' ],
			'fishbowl-option-group',
			'fishbowl-section',
		);
		add_settings_field(
			'fishbowl_list_uuid',
			'Fishbowl Brand Schema ID',
			[ $this, 'fishbowl_list_uuid_option' ],
			'fishbowl-option-group',
			'fishbowl-section',
		);

	}

	public function fishbowl_integration_is_enabled_option() {
		?>
			<input type="checkbox" name="fishbowl_integration_is_enabled" value="1" <?php checked( 1, get_option( 'fishbowl_integration_is_enabled' ), true ); ?> />
		<?php
	}

	public function fishbowl_source_option() {
		?>
			<input name="fishbowl_integration_source" value="<?php echo esc_attr( get_option( 'fishbowl_integration_source' ) ); ?>" /><br/>
			<em>Fishbowl User Source - provided by Fishbowl technical contact. Default: <strong>Tom's Watch Bar Signup</strong></em>
		<?php
	}

	public function fishbowl_brand_uuid_option() {
		?>
			<input name="fishbowl_brand_uuid" value="<?php echo esc_attr( get_option( 'fishbowl_brand_uuid' ) ); ?>" /><br/>
			<em>Fishbowl Brand UUID - provided by Fishbowl technical contact. Default: <strong>955eeda9-470a-45c3-bbc9-44a323dac6e7</strong></em>
		<?php
	}

	public function fishbowl_brand_schema_option() {
		?>
			<input name="fishbowl_brand_schema" value="<?php echo esc_attr( get_option( 'fishbowl_brand_schema' ) ); ?>" /><br/>
			<em>Fishbowl Brand Schema ID - provided by Fishbowl technical contact. Default: <strong>3</strong></em>
		<?php
	}

	public function fishbowl_list_uuid_option() {
		?>
			<input name="fishbowl_list_uuid" value="<?php echo esc_attr( get_option( 'fishbowl_list_uuid' ) ); ?>" /><br/>
			<em>Fishbowl List UUID - provided by Fishbowl technical contact. Default: <strong>fa4d963f-45b0-49a5-aa24-01d80bf9e035</strong></em>
		<?php
	}
}
