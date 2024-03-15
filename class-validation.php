<?php
/**
 * Add custom validation to the back-end of the forms.
 * - Check user's DOB and ensure the user is 21 or older. Fishbowl's API will auto-reject any leads who are under (14?)
 *
 * NOTE: Front-end validation happens separately via javascript. This is an extra layer on the back-end to check before sending to Fishbowl.
 */

namespace GF_Fishbowl;

class Validation {

	public function __construct() {
		add_filter( 'gform_field_validation_27', [$this, 'is_user_old_enough'], 10, 5 );
	}


	public function is_user_old_enough( $result, $value, $form, $field, $entry ) {

		$min_age = 21;

		if ( 'Birthday' !== $field->label ) {
			return $result;
		}

		if ( $result['is_valid'] ) {

			if ( is_array( $value ) ) {
				$value = array_values( $value );
			}

			$date_value = \GFFormsModel::prepare_date( $field->dateFormat, $value );

			$today = new \DateTime();
			$diff  = $today->diff( new \DateTime( $date_value ) );
			$age   = $diff->y;

			if ( $age < $min_age ) {
				$result['is_valid'] = false;
				$result['message']  = 'Sorry, you must be at least ' . $min_age . ' years old.';
			}

		}

		return $result;
	}

}
