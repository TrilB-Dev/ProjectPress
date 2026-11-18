<?php
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Core\WP\Database;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class ProjectFieldRepository {
	public function all(): array { global $wpdb; return (array) $wpdb->get_results( 'SELECT * FROM ' . Database::table_name( 'project_fields' ) . ' ORDER BY sort_order ASC, field_name ASC', ARRAY_A ); }
	public function create( array $data ) { global $wpdb; $types = array( 'text', 'textarea', 'integer', 'decimal', 'boolean', 'date', 'datetime', 'select', 'multi_select', 'user', 'url', 'email' ); $type = sanitize_key( $data['type'] ?? 'text' ); if ( ! in_array( $type, $types, true ) ) { return false; } $row = array( 'field_key' => sanitize_key( $data['key'] ?? $data['name'] ?? '' ), 'field_name' => sanitize_text_field( $data['name'] ?? '' ), 'field_description' => sanitize_textarea_field( $data['description'] ?? '' ), 'field_type' => $type, 'configuration' => wp_json_encode( is_array( $data['configuration'] ?? null ) ? $data['configuration'] : array() ), 'is_required' => empty( $data['required'] ) ? 0 : 1, 'is_enabled' => empty( $data['enabled'] ) ? 0 : 1, 'sort_order' => absint( $data['sort_order'] ?? 0 ), 'created_by' => get_current_user_id(), 'created_at' => current_time( 'mysql', true ), 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql', true ) ); if ( '' === $row['field_key'] || '' === $row['field_name'] ) { return false; } return false === $wpdb->insert( Database::table_name( 'project_fields' ), $row ) ? false : (int) $wpdb->insert_id; }
}
