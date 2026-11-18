<?php
/**
 * High-level facade for the ProjectPress project domain.
 *
 * @package ProjectPress\Includes\Project
 */
namespace ProjectPress\Includes\Project;

use ProjectPress\Includes\Project\Core\ProjectContext;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectQuery;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use ProjectPress\Includes\Project\Core\ProjectService;
use ProjectPress\Includes\Project\Core\ProjectValidator;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectManager {
	private static ?self $instance = null;
	private ProjectRepository $repository;
	private ProjectService $service;
	private ProjectContext $context;

	private function __construct() {
		$permissions = new ProjectPermissions();
		$this->repository = new ProjectRepository();
		$this->service = new ProjectService( $this->repository, new ProjectValidator(), $permissions );
		$this->context = new ProjectContext( $this->repository, $permissions );
	}

	public static function get_instance(): self { return self::$instance ??= new self(); }
	public function repository(): ProjectRepository { return $this->repository; }
	public function service(): ProjectService { return $this->service; }
	public function context(): ProjectContext { return $this->context; }
	public function find( int $id ) { return $this->repository->find( $id ); }
	public function query( array $arguments = array() ): array { return $this->repository->query( new ProjectQuery( $arguments ) ); }
	public function create( array $data ) { return $this->service->create( $data ); }
	public function resolve( $identifier ) { return $this->context->resolve( $identifier ); }
}
