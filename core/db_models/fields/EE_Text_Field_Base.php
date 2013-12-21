<?php
require_once( EE_MODELS . 'fields/EE_Model_Field_Base.php' );
/**
 * Text_Fields is a base class for any fields which are have text value. (Exception: foreign and private key fields. Wish PHP had multiple-inheritance for this...)
 */
abstract class EE_Text_Field_Base extends EE_Model_Field_Base{
	function get_wpdb_data_type(){
		return '%s';
	}

	function prepare_for_get( $value_of_field_on_model_object ) {
		return is_string($value_of_field_on_model_object) ? stripslashes( $value_of_field_on_model_object ) : $value_of_field_on_model_object;
	}
}