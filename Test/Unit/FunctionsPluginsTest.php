<?php

declare( strict_types=1 );

namespace {
    if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $value ) { return is_string( $value ) ? stripslashes( $value ) : $value; } }
    if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ): string { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
    if ( ! function_exists( '__' ) ) { function __( $text, $domain = null ): string { return (string) $text; } }
    if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $text ): string { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); } }
    if ( ! function_exists( 'esc_html' ) ) { function esc_html( $text ): string { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); } }
    if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $nonce, $action ) { return true; } }
    if ( ! function_exists( 'wp_send_json_success' ) ) { function wp_send_json_success( $data=null, $status_code=200 ) { $GLOBALS['projectpress_test_json_response']=array('success'=>true,'data'=>$data,'status_code'=>$status_code); return $data; } }
    if ( ! function_exists( 'wp_send_json_error' ) ) { function wp_send_json_error( $data=null, $status_code=400 ) { $GLOBALS['projectpress_test_json_response']=array('success'=>false,'data'=>$data,'status_code'=>$status_code); return $data; } }
    if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ): bool { return true; } }
    if ( ! function_exists( 'maybe_serialize' ) ) { function maybe_serialize( $data ) { return serialize( $data ); } }
    if ( ! function_exists( 'maybe_unserialize' ) ) { function maybe_unserialize( $data ) { return is_string( $data ) ? @unserialize( $data ) : $data; } }
    if ( ! function_exists( 'current_time' ) ) { function current_time( $type='mysql' ) { return '2026-01-01 00:00:00'; } }
    if ( ! class_exists( 'wpdb' ) ) { class wpdb { public $prefix='wp_'; public function prepare($query,...$args){return$query;} public function replace($table,$data,$format=null){$GLOBALS['projectpress_settings_store'][$table]=$data;return true;} public function get_var($query){return null;} public function delete($table,$where,$format=null){return true;} } }
}

namespace ProjectPress\Tests\Unit {
    use PHPUnit\Framework\TestCase;
    use ProjectPress\Includes\Functions\Admin\FunctionsPlugins;
    use ProjectPress\Includes\Plugins\PluginInterface;
    use ProjectPress\Includes\Plugins\Plugins;
    use ProjectPress\Includes\Plugins\SettingsPageProviderInterface;

    final class FunctionsPluginsTest extends TestCase {
        protected function setUp(): void { parent::setUp(); $_POST=array(); $GLOBALS['projectpress_test_json_response']=null; $GLOBALS['projectpress_settings_store']=array(); $GLOBALS['wpdb']=new \wpdb(); }

        private function plugin( string $slug ): PluginInterface {
            return new class( $slug ) implements PluginInterface, SettingsPageProviderInterface {
                private string $slug; public function __construct(string $slug){$this->slug=$slug;} public function get_slug():string{return$this->slug;} public function get_name():string{return'Example Plugin';} public function get_version():string{return'1.0.0';} public function get_author():string{return'ProjectPress';} public function get_author_uri():string{return'https://<scrubbed-url-domain>';} public function get_description():string{return'Example ProjectPress extension';} public function get_uri():string{return'https://<scrubbed-url-domain>';} public function get_license():string{return'GPL-2.0-or-later';} public function is_active():bool{return true;} public function init():void{} public function get_settings_page():array{return array('slug'=>'example','label'=>'Example Plugin','title'=>'Example Plugin settings','fields'=>array(array('key'=>'enabled')));} public function sanitize_settings($input):array{return is_array($input)?$input:array();}
            };
        }

        public function testSavePluginSettingsPersistsToProjectPressSettingsTable():void{Plugins::register_plugin($this->plugin('example-plugin'));$_POST=array('slug'=>'example-plugin','settings'=>array('enabled'=>true,'label'=>'Alpha'),'nonce'=>'valid-nonce');(new FunctionsPlugins())->save_plugin_settings();$table=\ProjectPress\Includes\Core\WP\Database::table_name('settings');$this->assertArrayHasKey($table,$GLOBALS['projectpress_settings_store']);$this->assertSame('projectpress_example',$GLOBALS['projectpress_settings_store'][$table]['setting_group']);$this->assertSame(array('enabled'=>true,'label'=>'Alpha'),maybe_unserialize($GLOBALS['projectpress_settings_store'][$table]['setting_value']));}
        public function testSavePluginSettingsAcceptsCurrentAjaxNonceAction():void{Plugins::register_plugin($this->plugin('ajax-plugin'));$_POST=array('slug'=>'ajax-plugin','settings'=>array('mode'=>'safe'),'nonce'=>'valid-nonce','action'=>'projectpress_save_plugin_settings');(new FunctionsPlugins())->save_plugin_settings();$table=\ProjectPress\Includes\Core\WP\Database::table_name('settings');$this->assertSame(array('mode'=>'safe'),maybe_unserialize($GLOBALS['projectpress_settings_store'][$table]['setting_value']));}
    }
}
