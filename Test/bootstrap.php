<?php

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR ); }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ): string { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( '__' ) ) { function __( $text, $domain = null ): string { return (string) $text; } }
if ( ! function_exists( 'sanitize_title' ) ) { function sanitize_title( $title, $fallback_title = '', $context = 'save' ): string { $slug = strtolower( trim( preg_replace( '/[^a-z0-9]+/', '-', (string) $title ), '-' ) ); return '' !== $slug ? $slug : (string) $fallback_title; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ): string { return trim( strip_tags( (string) $text ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $text ): string { return trim( (string) $text ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ): int { return abs( (int) $value ); } }
if ( ! function_exists( 'apply_filters' ) ) { function apply_filters( $hook, $value, ...$args ) { return $value; } }
if ( ! function_exists( 'do_action' ) ) { function do_action( $hook, ...$args ): void { $GLOBALS['projectpress_test_actions'][] = array( $hook, $args ); } }
if ( ! function_exists( 'current_time' ) ) { function current_time( $type = 'mysql', $gmt = false ) { return '2026-11-18 14:28:45'; } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id(): int { return (int) ( $GLOBALS['projectpress_test_user_id'] ?? 1 ); } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ): bool { return in_array( $capability, $GLOBALS['projectpress_test_caps'] ?? array( 'manage_options' ), true ); } }
if ( ! function_exists( 'user_can' ) ) { function user_can( $user_id, $capability ): bool { return current_user_can( $capability ); } }
if ( ! function_exists( 'wp_unique_id' ) ) { function wp_unique_id(): int { static $id = 100; return ++$id; } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $thing ): bool { return $thing instanceof \WP_Error; } }
if ( ! function_exists( 'post_type_exists' ) ) { function post_type_exists( $post_type ): bool { return isset( $GLOBALS['projectpress_test_post_types'][ (string) $post_type ] ); } }
if ( ! function_exists( 'register_post_type' ) ) { function register_post_type( $post_type, $args = array() ) { $GLOBALS['projectpress_test_post_types'][ (string) $post_type ] = $args; return true; } }
if ( ! function_exists( 'register_post_meta' ) ) { function register_post_meta( $post_type, $meta_key, $args = array() ) { $GLOBALS['projectpress_test_post_meta'][ (string) $post_type ][ (string) $meta_key ] = $args; return true; } }
$GLOBALS['projectpress_test_post_types'] = $GLOBALS['projectpress_test_post_types'] ?? array();
$GLOBALS['projectpress_test_post_meta'] = $GLOBALS['projectpress_test_post_meta'] ?? array();
require_once dirname( __DIR__ ) . '/vendor/autoload.php';
