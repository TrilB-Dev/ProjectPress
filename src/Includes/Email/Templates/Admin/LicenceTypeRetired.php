<?php
/**
 * Admin email template for retired licence types.
 *
 * @package ProjectPress
 */

namespace ProjectPress\Includes\Email\Templates\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LicenceTypeRetired extends EmailTemplate {
	/** @var string */
	protected string $template_name = 'LicenceTypeRetired';

	public function render( array $context = array() ): string {
		$values = array_merge( $this->context, $context );
		$name   = $this->text( $values['name'] ?? __( 'Licence type', 'projectpress' ) );
		if ( ! empty( $values['retired'] ) ) {
			$message = __( 'retired', 'projectpress' );
		} else {
			$message = __( 'reactivated', 'projectpress' );
		}

		/* translators: %s is the licence type status label. */
		$status_message = sprintf( __( 'This licence type has been %s.', 'projectpress' ), $message );

		$html = '<h2 style="margin: 0 0 12px;">' . esc_html__( 'Licence type status changed', 'projectpress' ) . '</h2>
            <p style="margin: 0 0 12px;">' . esc_html( $status_message ) . '</p>
            <p style="margin: 0;"><strong>' . esc_html__( 'Name', 'projectpress' ) . ':</strong> ' . esc_html( $name ) . '</p>';

		return $this->wrap( $html );
	}
}



