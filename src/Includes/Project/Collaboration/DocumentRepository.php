<?php
/**
 * DocumentRepository for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

		if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * DocumentRepository implementation.
 *
 * @since 1.0.0
 */
final class DocumentRepository {
	/**
	 * for_project.
	 *
	 * @param int $project_id project_id parameter.
	 *
	 * @return array for_project result.
	 *
	 * @since 1.0.0
	 */
	public function for_project( int $project_id ): array {
		$posts = get_posts(
			array(
				'post_type'      => 'pp_document',
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => '_pp_project_id',
				'meta_value'     => absint( $project_id ),
			)
		);
		return array_map( array( $this, 'hydrate' ), $posts );
	}
	/**
	 * find.
	 *
	 * @param int $post_id post_id parameter.
	 *
	 * @return ?Document find result.
	 *
	 * @since 1.0.0
	 */
	public function find( int $post_id ): ?Document {
		$post = get_post( absint( $post_id ) );
		return $post && 'pp_document' === $post->post_type ? $this->hydrate( $post ) : null;
	}
	/**
	 * create.
	 *
	 * @param int $project_id project_id parameter.
	 * @param array $data data parameter.
	 *
	 * @return mixed create result.
	 *
	 * @since 1.0.0
	 */
	public function create( int $project_id, array $data ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'pp_document',
				'post_status'  => 'publish',
				'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
				'post_content' => wp_kses_post( $data['content'] ?? '' ),
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		update_post_meta( $id, '_pp_project_id', absint( $project_id ) );
		update_post_meta( $id, '_pp_document_status', sanitize_key( $data['status'] ?? 'draft' ) );
		return $this->find( (int) $id );
	}
	/**
	 * delete.
	 *
	 * @param int $post_id post_id parameter.
	 *
	 * @return bool delete result.
	 *
	 * @since 1.0.0
	 */
	public function delete( int $post_id ): bool {
		return false !== wp_delete_post( absint( $post_id ), true );
	}
	/**
	 * hydrate.
	 *
	 * @param mixed $post post parameter.
	 *
	 * @return Document hydrate result.
	 *
	 * @since 1.0.0
	 */
	private function hydrate( $post ): Document {
		return new Document(
			array(
				'id'         => (int) $post->ID,
				'project_id' => absint( get_post_meta( $post->ID, '_pp_project_id', true ) ),
				'title'      => (string) $post->post_title,
				'content'    => (string) $post->post_content,
				'status'     => (string) get_post_meta( $post->ID, '_pp_document_status', true ),
				'created_at' => (string) $post->post_date_gmt,
			)
		);
	}
}
