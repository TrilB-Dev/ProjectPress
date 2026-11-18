<?php
namespace ProjectPress\Admin\Manager\ProjectManager;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class Tags {
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'projectpress_projects_taxonomies' ) ) { wp_die( esc_html__( 'You are not authorised to manage project tags.', 'projectpress' ) ); }
		$taxonomy = 'projectpress_tag';
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		echo '<div class="card card-body"><h2 class="h4">' . esc_html__( 'Project Tags', 'projectpress' ) . '</h2>';
		if ( is_wp_error( $terms ) ) { echo '<p>' . esc_html( $terms->get_error_message() ) . '</p>'; } elseif ( empty( $terms ) ) { echo '<p>' . esc_html__( 'No project tags have been created.', 'projectpress' ) . '</p>'; } else { echo '<ul class="list-group list-group-flush">'; foreach ( $terms as $term ) { echo '<li class="list-group-item d-flex justify-content-between"><span>' . esc_html( $term->name ) . '</span><span class="text-secondary">' . esc_html( (string) $term->count ) . '</span></li>'; } echo '</ul>'; }
		echo '</div>';
	}
}
