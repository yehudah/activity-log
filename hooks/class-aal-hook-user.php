<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AAL_Hook_User extends AAL_Hook_Base {

	public function hooks_wp_login( $user_login, $user ) {
		aal_insert_log( array(
			'action'      => 'logged_in',
			'object_type' => 'User',
			'user_id'     => $user->ID,
			'object_id'   => $user->ID,
			'object_name' => $user->user_nicename,
		) );
	}

	public function hooks_user_register( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		aal_insert_log( array(
			'action'      => 'created',
			'object_type' => 'User',
			'object_id'   => $user->ID,
			'object_name' => $user->user_nicename,
		) );
	}
	public function hooks_delete_user( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		aal_insert_log( array(
			'action'      => 'deleted',
			'object_type' => 'User',
			'object_id'   => $user->ID,
			'object_name' => $user->user_nicename,
		) );
	}

	public function hooks_wp_logout() {
		$user = wp_get_current_user();

		aal_insert_log( array(
			'action'      => 'logged_out',
			'object_type' => 'User',
			'user_id'     => $user->ID,
			'object_id'   => $user->ID,
			'object_name' => $user->user_nicename,
		) );
	}

	public function hooks_profile_update( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		aal_insert_log( array(
			'action'      => 'updated',
			'object_type' => 'User',
			'object_id'   => $user->ID,
			'object_name' => $user->user_nicename,
		) );
	}

	public function hooks_wrong_password( $username ) {
		aal_insert_log( array(
			'action'      => 'wrong_password',
			'object_type' => 'User',
			'user_id'     => 0,
			'object_id'   => 0,
			'object_name' => $username,
		) );
	}

	public function hooks_role_added( $user_id, $role ) {
		$source_user = wp_get_current_user();
		$target_user = new WP_User( $user_id );
		$caps = implode( ', ', array_keys( get_role( $role )->capabilities ) );

		aal_insert_log( array(
			'action'      => 'added',
			'object_type' => 'User',
			'object_id'   => $source_user->ID,
			'object_subtype' => "User: {$target_user->user_login} --> Role: {$role} --> Caps: {$caps}",
			'object_name' => 'Role Added',
		) );
	}

	public function hooks_role_update( $user_id, $role, $old_roles ) {
		$source_user = wp_get_current_user();
		$target_user = new WP_User( $user_id );

		aal_insert_log( array(
			'action'      => 'updated',
			'object_type' => 'User',
			'object_id'   => $source_user->ID,
			'object_subtype' => "User: {$target_user->user_login} --> Role: {$role}",
			'object_name' => 'Role Updated',
		) );
	}

	public function hooks_role_create( $display_name ) {
		$user = wp_get_current_user();

		aal_insert_log( array(
			'action'      => 'created',
			'object_type' => 'User',
			'object_id'   => $user->ID,
			'object_subtype' => $display_name,
			'object_name' => 'Role Created',
		) );
	}

	public function hooks_cap_create( $role, $cap ) {
		$user = wp_get_current_user();

		aal_insert_log( array(
			'action'      => 'created',
			'object_type' => 'User',
			'object_id'   => $user->ID,
			'object_subtype' => "{$cap} added to role {$role}",
			'object_name' => 'Cap Created',
		) );
	}

	public function __construct() {
		add_action( 'wp_login', array( &$this, 'hooks_wp_login' ), 10, 2 );
		add_action( 'wp_logout', array( &$this, 'hooks_wp_logout' ) );
		add_action( 'delete_user', array( &$this, 'hooks_delete_user' ) );
		add_action( 'user_register', array( &$this, 'hooks_user_register' ) );
		add_action( 'profile_update', array( &$this, 'hooks_profile_update' ) );
		add_filter( 'wp_login_failed', array( &$this, 'hooks_wrong_password' ) );
		add_action( 'add_user_role', array( $this, 'hooks_role_added' ), 10, 2 );
		add_action( 'set_user_role', array( $this, 'hooks_role_update' ), 10, 3 );
		add_action( 'aal_roles_add_role', array( $this, 'hooks_role_create' ) );
		add_action( 'aal_roles_add_cap', array( $this, 'hooks_cap_create' ), 10, 2 );

		parent::__construct();
	}

}
