<?php
namespace ProjectPress\Admin\Manager\ProjectManager;
use ProjectPress\Includes\Pages\Taxonomies;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class Tags extends Classifications { public function __construct() { parent::__construct( Taxonomies::PROJECT_TAG, __( 'Project Tags', 'projectpress' ) ); } }
