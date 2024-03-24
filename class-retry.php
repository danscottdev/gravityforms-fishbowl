<?php
/**
 * Helper function to re-send failed submissions to the Fishbowl API.
 * Occasionally submissions can fail due to timeout errors. This could also cover cases where Fishbowl may be down, or other issues.
 *
 * This will run once per hour, as defined via cron job in the plugin activation hook.
 * Will check the most recent 200 GF entries, find all entries with Fishbowl Status 'http_request_failed' and attempt to re-send them to the Fishbowl API.
 */

namespace GF_Fishbowl;

class Retry {

	public function __construct() {
		add_action( 'init', [ $this, 'registerTestEndpoint' ] );
		add_action('twb_fishbowl_retry_api', 'retry_failed_submissions');
	}

	public function retry_failed_submissions() {

		// Fetch failed submissions
		$failures = $this->fetch_failed_submissions();

		error_log("failures:");
		error_log( print_r( $failures, true ) );


		// Re-run API request for all failed entries
		foreach ($failures as $entry_id) {

			error_log("attempt resend for " . $entry_id);

			$entry = \GFAPI::get_entry($entry_id);
			error_log('Entry ID: ' . $entry['id']);

			// Get form object for the entry_id's form
			$form = \GFAPI::get_form($entry['form_id']);
			error_log('Form ID: ' . $form['id']);

			$result = Integration::submit_to_fishbowl($entry, $form);
		}

	}

	public function fetch_failed_submissions() {

		$form_ids = \GFFormsModel::get_form_ids();
		$fishbowl_forms = [];

		foreach ($form_ids as $form_id) {
			$form = \GFAPI::get_form( $form_id );
			$field = Utils::get_field_id( $form, 'Fishbowl Status', 'label' );
			if ($field) {
				$fishbowl_forms[] = array(
					'formId' => $form_id,
					'fieldId' => $field,
				);
			}
		}

		// error_log("fishbowl forms:");
		// error_log( print_r( $fishbowl_forms, true ) );

		if (!$fishbowl_forms) {
			return;
		}

		$failures = [];

		foreach ( $fishbowl_forms as $form ){
			$search_criteria = array(
				'status'        => 'active',
				'field_filters' => array(
					'mode' => 'any',
					array(
						'key'   => $form['fieldId'],
						'value' => 'Error: http_request_failed '
					),
				)
			);

			$paging = array( 'offset' => 0, 'page_size' => 5 );
			$entries = \GFAPI::get_entry_ids($form['formId'], $search_criteria, null, $paging);
			array_push($failures, ...$entries);
		}

		// error_log("failures:");
		// error_log( print_r( $failures, true ) );

		return $failures;
	}


	/**
	 * For testing purposes, can manually trigger fetchEvents() via endpoint:
	 * {{url}}/wp-json/toms_fishbowl/v1/test
	 * Should return 'true' on successful call
	 */
	public function registerTestEndpoint() {
		register_rest_route( 'toms_fishbowl/v1', '/test', array(
			'methods' => 'GET',
			'callback' => function() {
				error_log("manual test via endpoint:");
				$this->retry_failed_submissions();
				return true;
			}
		) );
	}

}
