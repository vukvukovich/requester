<?php
/**
 * Class used to validate incomming data
 *
 * We can do here all sorts of validation and checks depending on how strict
 * we want to be for the incoming data.
 *
 * @since     1.0.0
 * @package   Requester
 */

/**
 * Plugin validator class
 */
class Requester_Data_Validator {
	/**
	 * JSON decoded payload
	 *
	 * @var obj|null
	 */
	private $payload;

	/**
	 * Constructor
	 *
	 * @param string $payload JSON payload.
	 * @return void
	 */
	public function __construct( $payload ) {
		$this->payload = json_decode( $payload );
	}

	/**
	 * Check for valid JSON decoded data.
	 *
	 * @return bool
	 */
	private function is_valid_json() {
		return null !== $this->payload;
	}

	/**
	 * Check for data title.
	 *
	 * @return bool
	 */
	private function is_title_valid() {
		return is_string( $this->payload->title );
	}

	/**
	 * Check headers structure.
	 *
	 * @return bool
	 */
	private function are_headers_valid() {
		$defaults = array(
			'ID',
			'First Name',
			'Last Name',
			'Email',
			'Date',
		);

		return $this->payload->data->headers === $defaults;
	}

	/**
	 * Check rows structure and data types.
	 *
	 * @return bool
	 */
	private function are_rows_valid() {
		if ( ! isset( $this->payload->data->rows ) && ! get_object_vars( $this->payload->data->rows ) ) {
			return false;
		}

		$defaults = array(
			'id',
			'fname',
			'lname',
			'email',
			'date',
		);

		foreach ( $this->payload->data->rows as $key => $value ) {
			$row_keys = array_keys( get_object_vars( $value ) );

			// Check if row structure is fine.
			if ( $row_keys !== $defaults ) {
				return false;
			}

			// Check for valid data types.
			foreach ( $row_keys as $key ) {
				if ( 'id' === $key || 'date' === $key ) {
					if ( ! is_int( $value->{$key} ) ) {
						return false;
					}
				} else {
					if ( ! is_string( $value->{$key} ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Validate json payload
	 *
	 * @return string|void
	 */
	public function validate() {
		if ( $this->is_valid_json() &&
			$this->is_title_valid() &&
			$this->are_headers_valid() &&
			$this->are_rows_valid() ) {
			return wp_json_encode( $this->payload, JSON_HEX_TAG | JSON_FORCE_OBJECT );
		}

		return wp_json_encode( array( 'error' => __( 'Invalid data.', 'requester' ) ) );
	}
}
