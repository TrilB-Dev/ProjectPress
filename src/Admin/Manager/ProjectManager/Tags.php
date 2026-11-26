<?php
/**
 * Project tag administration.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\ProjectManager
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Includes\Pages\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tags extends Classifications {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			Taxonomies::PROJECT_TAG,
			__( 'Project Tags', 'projectpress' )
		);
	}
}
