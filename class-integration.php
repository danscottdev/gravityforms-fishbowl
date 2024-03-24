<?php
/**
 * Manage the API integration between GravityForms and Fishbowl
 *
 * Upon a form submission to GravityForms, parse the data and submit it to the FishBowl API endpoint
 * JSON payload will consist of a mix of user-entered form data and hardcoded account data for client's Fishbowl account.
 *
 * Fishbowl API Documentation: https://fishbowl-api.readme.io/reference/subscription (note: very sparse, not very helpful)
 * Fishbowl technical point of contact: Brandon Flude <brandon@fishbowl.com>
 *
 * Mission-critical dependency: GravityForms fields MUST be labeled correctly: "First Name", "Email Address", etc, as that is how this plugin parses that data.
 * Ideally this would be more robust, but have to skip in the interest of time. Other alternative would be to lookup based on field ID but that could be problematic
 * given we're working with 20+ diferent forms.
 *
 */

namespace GF_Fishbowl;

class Integration {

	public function __construct() {
		add_action( 'gform_after_submission', array( $this, 'submit_to_fishbowl' ), 10, 2 );
	}

	public static function submit_to_fishbowl( $entry, $form ) {

		error_log("Submitting API..." . $entry['id']);

		// error_log( print_r( $entry, true ) );

		Utils::init_fishbowl_status($entry, $form);

		// If Fishbowl integration is not enabled, exit
		if ( ! get_option( 'fishbowl_integration_is_enabled' ) ) {
			return;
		}

		// Fetch API account info from the admin settings page
		$source       = get_option( 'fishbowl_integration_source' );
		$brand_uuid   = get_option( 'fishbowl_brand_uuid' );
		$brand_schema = get_option( 'fishbowl_brand_schema' );
		$list_uuid    = get_option( 'fishbowl_list_uuid' );

		// User-entered form data
		$firstName   = Utils::get_field_value( $form, $entry, 'First Name', 'label' );
		$lastName    = Utils::get_field_value( $form, $entry, 'Last Name', 'label' );
		$email       = Utils::get_field_value( $form, $entry, 'Email Address', 'label' );
		$phoneNumber = Utils::get_field_value( $form, $entry, 'Phone Number', 'label' ) ?: '0000000000';
		$location    = Utils::get_field_value( $form, $entry, 'Location', 'label' );
		$birthday    = Utils::get_field_value( $form, $entry, 'Birthday', 'label' ) ?: '0000-00-00';
		$sport       = Utils::get_field_value( $form, $entry, 'Favorite Sport', 'label' );
		$options     = Utils::get_field_value( $form, $entry, 'Options', 'label' ); // email & SMS opt-in, true/false


		$options_field_id = Utils::get_field_id( $form, 'Options', 'label' );

		if ( 'yes' == $entry[$options_field_id . '.1'] ) {
			$options_value = true;
		} else {
			$options_value = false;
		}

		if ( 'yes-sms' == $entry[$options_field_id . '.2'] ) {
			$options_value_sms = true;
		} else {
			$options_value_sms = false;
		}

		// Parse out birthday values
		$birthdayMonth = substr($birthday, 5, 2);
		$birthdayDay   = substr($birthday, 8, 2);
		$birthdayYear  = substr($birthday, 0, 4);

		$location_id = Utils::get_location_id( $location );
		$sport_id    = Utils::get_sport_id( $sport );

		$zipCode = Utils::get_field_value( $form, $entry, 'Zipcode', 'label' ) ?: '00000';


		// Parse entry values into array
		$data = array(
			"firstName"     => $firstName,
			"lastName"      => $lastName,
			"email"         => $email,
			"phoneNumber"   => $phoneNumber,
			"zipCode"       => $zipCode,
			"location"      => $location_id,
			"birthdayMonth" => (int) $birthdayMonth, // per Fishbowl API specs, only birthdayMonth is an int
			"birthdayDay"   => $birthdayDay,
			"birthdayYear"  => $birthdayYear,
			"favoriteSport" => $sport_id,
			"receiveEmails" => (bool) $options_value,
			"receiveSms"    => (bool) $options_value_sms,
			"source"        => $source,
			"brandUUID"     => $brand_uuid,
			"brandSchemaId" => $brand_schema,
			"listUuid"      => $list_uuid

		);

		$dataJson = json_encode($data);

		gform_update_meta( $entry['id'], 'fishbowl_request_data_json', $dataJson );
		gform_update_meta( $entry['id'], 'fishbowl_request_data', $data );

		// create POST request to external API endpoint
		$response = wp_remote_post( 'https://api.fishbowl.com/api/external/subscription', array(
		    'method' => 'POST',
		    'headers' => array(
				'Accept' => 'application/json',
		        'Content-Type' => 'application/json'
		    ),
		    'body' => $dataJson,
			'timeout' => 10
		) );

		if ( is_wp_error( $response ) ) {
		    $response_code = $response->get_error_code();
		    $response_body = $response->get_error_message();
		} else {
		    $response_code = wp_remote_retrieve_response_code( $response );
		    $response_body = wp_remote_retrieve_body( $response );
		}

		error_log( $response_code );
		error_log( $response_body );
		gform_update_meta( $entry['id'], 'fishbowl_response_code', $response_code );
		gform_update_meta( $entry['id'], 'fishbowl_response_message', $response_body );

		if ( 'Error: http_request_failed' === $response_code ) {
			Utils::update_fishbowl_status("Timeout Error", $entry, $form);
		} else {
			Utils::update_fishbowl_status($response_code, $entry, $form);
		}

	}

}
