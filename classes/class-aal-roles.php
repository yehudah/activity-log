<?php

class AAL_Roles extends WP_Roles {

	public function __construct( $site_id = null ) {
		parent::__construct( $site_id );
	}

	public function add_role( $role, $display_name, $capabilities = array() ) {
		$result = parent::add_role( $role, $display_name, $capabilities );

		if ( $result instanceof WP_Role ) {
			do_action( 'aal_roles_add_role', $display_name );
		}

		return $result;
	}

	public function add_cap( $role, $cap, $grant = true ) {
		parent::add_cap( $role, $cap, $grant );

		do_action( 'aal_roles_add_cap', $role, $cap );
	}

}