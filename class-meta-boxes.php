<?php
/**
 * Add custom meta boxes to GF entries
 * Stores & display data around the API call to Fishbowl
 * Includes the JSON payload that was sent to Fishbowl, and the API response code & message
 */


namespace GF_Fishbowl;

class Meta_Boxes {

	public function __construct() {
		add_filter( 'gform_entry_detail_meta_boxes', [ $this, 'register_meta_box' ], 10, 3 );
	}

	public function register_meta_box( $meta_boxes, $entry, $form ) {

		$meta_boxes['fishbowl_meta_box'] = [
			'title'    => 'Fishbowl Meta Data',
			'context'  => 'side',
			'callback' => [ $this, 'add_fishbowl_meta_box' ],
		];

		return $meta_boxes;
	}

	public function add_fishbowl_meta_box( $args ) {
		$entry_id          = $args['entry']['id'];
		$response_code     = gform_get_meta( $entry_id, 'fishbowl_response_code' );
		$response_message  = gform_get_meta( $entry_id, 'fishbowl_response_message' );

		// Request data can be in two variables.
		$request_data      = gform_get_meta( $entry_id, 'fishbowl_request_data' );
		$request_data_json = gform_get_meta( $entry_id, 'fishbowl_request_data_json' );

		echo '<ul>';

		if ( ! empty( $response_code ) ) {
			printf(
				'<li><strong>Fishbowl Response Code:</strong> <br>%s</li>',
				esc_html( $response_code )
			);
		}

		if ( ! empty( $response_message ) ) {
			printf(
				'<li><strong>Fishbowl Response Message:</strong> <br>%s</li>',
				esc_html( $response_message )
			);
		}

		if ( ! empty( $request_data ) ) {
			// $request_data = (array) json_decode( $request_data );
			printf(
				'<li><strong>PHP Processing Data:</strong> <br><pre>%s</pre></li>',
				esc_html( print_r( $request_data, TRUE ) ) // phpcs:ignore
			);
		}

		if ( ! empty( $request_data_json ) ) {
			printf(
				'<li><strong>JSON Payload Sent to Fishbowl:</strong> <br><pre>%s</pre></li>',
				esc_html($request_data_json )
			);
		}

		echo '</ul>';
	}

}