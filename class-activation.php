<?php
/**
 * When plugin is activated, loop through all forms and add a new field
 * Field is set to administrative-view only (will not render on frontend)
 * This field will hold the "Fishbowl Integration Status" for individual entries.
 *
 * This is not mission-critical for the integration, but adds a helpful layer of visibility to any entries that may not have posted.
 *
 * This field will persist if the plugin is deactivated. Ideally it would be removed when the plugin is deactivated, but skipping that in the interest of time.
 */

namespace GF_Fishbowl;

class Activation {

	public static function activate() {

		$forms = \GFAPI::get_forms();

		foreach ( $forms as $form ) {

			// DEV TESTING: Only add to form ID 27 for now
			// if ( $form['id'] !== 27 ) {
			// 	error_log("Form ID is not 27");
			// 	continue;
			// }

			// Check if current form already has a "fishbowl status" field. If so we can skip to the next form.
			$has_field = false;
			foreach ( $form['fields'] as $field ) {
				if ( strtolower( $field->label ) === 'fishbowl status' ) {
					// error_log("Field already exists");
					$has_field = true;
					break;
				}
			}

			if ( ! $has_field ) {

				$new_field_id = \GFFormsModel::get_next_field_id( $form['fields'] );

				$properties['id']        = $new_field_id;
				$properties['type']      = 'text';
				$properties['label']     = 'Fishbowl Status';
				$properties['adminOnly'] = true;

				$field = \GF_Fields::create( $properties );
				$form['fields'][] = $field;
				\GFAPI::update_form( $form );

			}
		}
	}
}
