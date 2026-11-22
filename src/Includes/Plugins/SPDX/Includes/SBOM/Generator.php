<?php
/** Basic SPDX document generator. @package ProjectPress */
namespace ProjectPress\Includes\Plugins\SPDX\Includes\SBOM;
if(!defined('ABSPATH')){exit;}
final class Generator{
 public function generate($project):array{$id=method_exists($project,'get_id')?$project->get_id():0;$name=method_exists($project,'get_name')?$project->get_name():__('Project','projectpress');$key=method_exists($project,'get_key')?$project->get_key():(string)$id;return array('spdxVersion'=>'SPDX-2.3','dataLicense'=>'CC0-1.0','SPDXID'=>'SPDXRef-DOCUMENT','name'=>sanitize_text_field($name).' SBOM','documentNamespace'=>home_url('/projectpress/spdx/'.rawurlencode((string)$key).'/'.wp_generate_uuid4()),'creationInfo'=>array('created'=>gmdate('Y-m-d\TH:i:s\Z'),'creators'=>array('Tool: ProjectPress SPDX-0.1.0')),'packages'=>array(array('name'=>sanitize_text_field($name),'SPDXID'=>'SPDXRef-Project-'.absint($id),'downloadLocation'=>'NOASSERTION','filesAnalyzed'=>false,'licenseConcluded'=>'NOASSERTION','licenseDeclared'=>'NOASSERTION','copyrightText'=>'NOASSERTION')));}
}
