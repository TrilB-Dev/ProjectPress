<?php
namespace ProjectPress\Admin\Manager\ProjectManager;
use ProjectPress\Includes\Pages\Taxonomies;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class Types extends Classifications { public function __construct() { parent::__construct( Taxonomies::PROJECT_TYPE, __( 'Project Types', 'projectpress' ) ); } }
