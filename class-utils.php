<?php
/**
 * Utility functions to help use GravityForms
 */
namespace GF_Fishbowl;

class Utils {

	/**
	 * Utility Function to help us fetch GravityForms field values from an $entry
	 * So that we don't need to hard-code field IDs when trying to fetch those values.
	 * Prevents functionality from breaking if form field IDs ever change. Also allows us to work across multiple forms easily.
	 */
	public static function get_field_value( $form, $entry, $value, $key = 'label' ) {
		foreach ( $form['fields'] as $field ) {
			if ( strtolower( $field->$key ) === strtolower( $value ) ) {
				$field_id = $field->id;
			}
			if ( isset( $field_id ) ) {
				return $entry[ $field_id ];
			}
		}
		return false;
	}

	/**
	 * Utility Function to help us fetch GravityForms field IDs from an $entry
	 * So that we don't need to hard-code field IDs when trying to fetch those values.
	 * Prevents functionality from breaking if form field IDs ever change. Also allows us to work across multiple forms easily.
	 */
	public static function get_field_id( $form, $value, $key = 'label' ) {
		foreach ( $form['fields'] as $field ) {
			if ( strtolower( $field->$key ) === strtolower( $value ) ) {
				return $field->id;
			}
		}
		return false;
	}

	/**
	 * On initial form submission, initialize the 'Fishbowl Status' field to "Not Sent"
	 */
	public static function init_fishbowl_status( $entry, $form ) {
		$status_field_id = self::get_field_id( $form, 'Fishbowl Status', 'label' );
		if ( false === $status_field_id || empty( $status_field_id ) || 0 === $status_field_id ) {
			return;
		}
		\GFAPI::update_entry_field( $entry['id'], $status_field_id, "Not Sent" );
	}

	// Upon API response, update the 'Fishbowl Status' field to "Sent"
	public static function update_fishbowl_status( $response, $entry, $form ) {
		$status_field_id = self::get_field_id( $form, 'Fishbowl Status', 'label' );
		if ( false === $status_field_id || empty( $status_field_id ) || 0 === $status_field_id ) {
			return;
		}
		if ("200" == $response) {
			$message = "Success";
		} else {
			$message = "Error: " . $response;
		}
		\GFAPI::update_entry_field( $entry['id'], $status_field_id, $message );
	}

	/**
	 * Lookup function to map user-entered "location" field to the Fishbowl location ID
	 * Ideally would like to have the ID mapping saved via the settings page, but this is quicker for now
	 */
	public static function get_location_id( $location ) {

		$locations = array(
			'Coors Field'       => 110,
			'Capital One Arena' => 31,
			'Las Vegas'         => 32,
			'ilani'             => 33,
			'Mohegan Sun'       => 34,
			'National Harbor'   => 35,
			'Los Angeles'       => 36,
			'Minneapolis'       => 37,
			'Pittsburgh'        => 38,
			'Navy Yard'         => 39,
			'Houston'           => 40,
			'Sacramento'        => 41,
			'Orlando'           => 870,
		);

		return $locations[ $location ] ?? 0;
	}

	/**
	 * Lookup function to map 'favorite sport' form values to the Fishbowl UUIDs for them
	 */
	public static function get_sport_id( $sport ) {

		$sports = array(
			'Basketball' => 'bd84833c-dbd2-4766-8c3b-a22141c439e2',
			'Hockey'     => 'addcdea3-e9d6-4de3-9045-29112d0c1604',
			'Football'   => '1379f02e-5d49-4186-909b-45c87203032f',
			'Baseball'   => 'fbd0682b-81d1-4811-9c14-de748d3665e2',
			'MMA'        => 'a00103d5-6dfe-431e-9b3d-a6e28e97e99a',
			'Boxing'     => 'd2d37f7b-3165-49ac-8349-d4765c3797fd',
			'Golf'       => 'eb30bd68-a195-4dbb-9f98-7cfa4d6a7cc8',
			'Tennis'     => '6110cbc3-22cd-4bd1-8461-97d1b95aa7ea',
			'Soccer'     => '98226635-1ded-4a04-9f56-b2cd159e1915',
		);

		return $sports[ $sport ] ?? '';

	}

}
