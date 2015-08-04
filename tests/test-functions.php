<?php

class BA_EAS_Tests_Functions extends WP_UnitTestCase {

	private $single_user = null;
	private $single_user_id = null;

	public function setUp() {
		parent::setUp();

		$this->eas = ba_eas();

		$this->single_user = array(
			'user_login'   => 'mastersplinter',
			'user_pass'    => '1234',
			'user_email'   => 'mastersplinter@example.com',
			'display_name' => 'Master Splinter',
			'nickname'     => 'Sensei',
			'first_name'   => 'Master',
			'last_name'    => 'Splinter',
		);

		$this->single_user_id = $this->factory->user->create( $this->single_user );
	}

	public function tearDown() {
		parent::tearDown();

		$this->eas->author_base = 'author';
		$this->eas->role_slugs  = ba_eas_tests_slugs( 'default' );
	}

	/**
	 * @covers ::ba_eas_do_auto_update
	 */
	function test_ba_eas_do_auto_update() {

		// True tests
		add_filter( 'ba_eas_do_auto_update', '__return_true' );
		$this->assertTrue( ba_eas_do_auto_update() );
		remove_filter( 'ba_eas_do_auto_update', '__return_true', 10 );

		// False tests
		add_filter( 'ba_eas_do_auto_update', '__return_false' );
		$this->assertFalse( ba_eas_do_auto_update() );
		remove_filter( 'ba_eas_do_auto_update', '__return_false', 10 );
	}

	/**
	 * @covers ::ba_eas_auto_update_user_nicename
	 */
	function test_ba_eas_auto_update_user_nicename() {
		// No user id
		$this->assertFalse( ba_eas_auto_update_user_nicename( false ) );

		// No auto update
		add_filter( 'ba_eas_do_auto_update', '__return_false' );
		$this->assertFalse( ba_eas_auto_update_user_nicename( 1 ) );
		remove_filter( 'ba_eas_do_auto_update', '__return_false', 10 );

		// We need below tests to think auto update is on
		add_filter( 'ba_eas_do_auto_update', '__return_true' );

		// Invalid user
		$this->assertFalse( ba_eas_auto_update_user_nicename( 1337 ) );

		// Update using username
		add_filter( 'ba_eas_auto_update_user_nicename_structure', '__return_empty_string' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'mastersplinter', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', '__return_empty_string', 10 );

		// Update using username
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_username' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'mastersplinter', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_username', 10 );

		// Update using nickname
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_nickname' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'sensei', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_displayname', 10 );

		// Update using displayname
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_displayname' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'master-splinter', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_displayname', 10 );

		// Update using firstname
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_firstname' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'master', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_firstname', 10 );

		// Update using lastname
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_lastname' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'splinter', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_lastname', 10 );

		// Update using firstlast
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_firstlast' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'master-splinter', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_firstlast', 10 );

		// Update using lastfirst
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_lastfirst' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'splinter-master', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_nicename_return_lastfirst', 10 );

		// Update using random string as structure, shouldn't update, so
		// user_nicename should be same as previous test ('splinter-master')
		add_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_return_sentence' );
		$user_id  = ba_eas_auto_update_user_nicename( $this->single_user_id );
		$this->assertTrue( 0 < $user_id );
		$user     = get_userdata( $user_id );
		$this->assertEquals( 'splinter-master', $user->user_nicename );
		remove_filter( 'ba_eas_auto_update_user_nicename_structure', 'ba_eas_tests_return_sentence', 10 );

		remove_filter( 'ba_eas_do_auto_update', '__return_true', 10 );
	}

	/**
	 * @covers ::ba_eas_auto_update_user_nicename_single
	 */
	function test_ba_eas_auto_update_user_nicename_single() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers ::ba_eas_auto_update_user_nicename_bulk
	 */
	function test_ba_eas_auto_update_user_nicename_bulk() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers ::ba_eas_do_role_based_author_base
	 */
	function test_ba_eas_do_role_based_author_base() {

		// True tests
		add_filter( 'ba_eas_do_role_based_author_base', '__return_true' );
		$this->assertTrue( ba_eas_do_role_based_author_base() );
		remove_filter( 'ba_eas_do_role_based_author_base', '__return_true', 10 );

		// False tests
		add_filter( 'ba_eas_do_role_based_author_base', '__return_false' );
		$this->assertFalse( ba_eas_do_role_based_author_base() );
		remove_filter( 'ba_eas_do_role_based_author_base', '__return_false', 10 );
	}

	/**
	 * @covers ::ba_eas_author_link
	 */
	function test_ba_eas_author_link() {
		$author_link            = 'http://example.com/author/mastersplinter/';
		$role_based_author_link = 'http://example.com/%ba_eas_author_role%/mastersplinter/';
		$author_link_author     = 'http://example.com/author/mastersplinter/';
		$author_link_ninja      = 'http://example.com/ninja/mastersplinter/';
		$author_link_subscriber = 'http://example.com/subscriber/mastersplinter/';

		add_filter( 'ba_eas_do_role_based_author_base', '__return_false' );

		// Test role-based author base disabled
		$link = ba_eas_author_link( $author_link, $this->single_user_id );
		$this->assertEquals( $author_link, $link );

		remove_filter( 'ba_eas_do_role_based_author_base', '__return_false', 10 );

		add_filter( 'ba_eas_do_role_based_author_base', '__return_true' );

		// Test role-based author based enabled, but no EAS author base
		$link = ba_eas_author_link( $author_link, $this->single_user_id );
		$this->assertEquals( $author_link, $link );

		// Test role-based author based enabled, user is subscriber
		$link = ba_eas_author_link( $role_based_author_link, $this->single_user_id );
		$this->assertEquals( $author_link_subscriber, $link );

		// Test role-based author based enabled, role slug doesn't exist
		$this->eas->role_slugs = array();
		$link = ba_eas_author_link( $role_based_author_link, $this->single_user_id );
		$this->assertEquals( $author_link_author, $link );

		// Test role-based author based enabled, role slug doesn't exist, custom author base
		$this->eas->author_base = 'ninja';
		$link = ba_eas_author_link( $role_based_author_link, $this->single_user_id );
		$this->assertEquals( $author_link_ninja, $link );

		remove_filter( 'ba_eas_do_role_based_author_base', '__return_true', 10 );
	}

	/**
	 * @covers ::ba_eas_template_include
	 */
	function test_ba_eas_template_include() {

		add_filter( 'ba_eas_do_role_based_author_base', '__return_false' );
		$this->assertEquals( 'no-role-based', ba_eas_template_include( 'no-role-based' ) );
		remove_filter( 'ba_eas_do_role_based_author_base', '__return_false', 10 );

		add_filter( 'ba_eas_do_role_based_author_base', '__return_true' );

		$this->assertEquals( 'no-WP_User', ba_eas_template_include( 'no-WP_User' ) );

		$GLOBALS['wp_query']->queried_object = get_userdata( $this->single_user_id );
		$this->assertEquals( 'author-mastersplinter.php', ba_eas_template_include( 'author-mastersplinter.php' ) );
		$this->assertEquals( "author-{$this->single_user_id}.php", ba_eas_template_include( "author-{$this->single_user_id}.php" ) );

		$role_template         = TEMPLATEPATH . '/' . 'author-subscriber.php';
		$role_slug_template    = TEMPLATEPATH . '/' . 'author-deshi.php';
		$this->eas->role_slugs = ba_eas_tests_slugs( 'custom' );

		file_put_contents( $role_template, '<?php' );
		$this->assertEquals( $role_template, ba_eas_template_include( 'author-subscriber.php' ) );
		@unlink( $role_template );

		/*
		 * Creating and loading both files fails. Individually they work, but
		 * for some reason you can't test for both instances. Need to investigate.
		 */
		/*
		file_put_contents( $role_slug_template, '<?php' );
		$this->assertEquals( $role_slug_template, ba_eas_template_include( 'author-deshi.php' ) );
		@unlink( $role_slug_template );
		*/

		$GLOBALS['wp_query']->queried_object = null;

		remove_filter( 'ba_eas_do_role_based_author_base', '__return_true', 10 );
	}

	/**
	 * @covers ::ba_eas_flush_rewrite_rules
	 */
	function test_ba_eas_flush_rewrite_rules() {
		update_option( 'rewrite_rules', 'test' );
		$this->assertEquals( 'test', get_option( 'rewrite_rules' ) );

		ba_eas_flush_rewrite_rules();
		$this->assertFalse( get_option( 'rewrite_rules' ) );
	}

	/**
	 * @covers ::ba_eas_author_rewrite_rules
	 */
	function test_ba_eas_author_rewrite_rules() {

		$test = array(
			'with_name_1'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]&feed=$matches[3]',
			'without_name_1' => 'index.php?ba_eas_author_role=$matches[1]&feed=$matches[2]',
			'with_name_2'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]',
			'with_name_3'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]&paged=$matches[3]',
			'without_name_2' => 'index.php?ba_eas_author_role=$matches[1]&paged=$matches[2]',
		);

		$expected = array(
			'with_name_1'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]&feed=$matches[3]',
			'with_name_2'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]',
			'with_name_3'    => 'index.php?ba_eas_author_role=$matches[1]&author_name=$matches[2]&paged=$matches[3]',
		);

		add_filter( 'ba_eas_do_role_based_author_base', '__return_false' );
		$this->assertEquals( $test, ba_eas_author_rewrite_rules( $test ) );
		remove_filter( 'ba_eas_do_role_based_author_base', '__return_false', 10 );

		add_filter( 'ba_eas_do_role_based_author_base', '__return_true' );
		$this->assertEquals( $expected, ba_eas_author_rewrite_rules( $test ) );
		remove_filter( 'ba_eas_do_role_based_author_base', '__return_true', 10 );
	}

	/**
	 * @covers ::ba_eas_get_editable_roles
	 */
	function test_ba_eas_get_editable_roles() {

		// Test with empty $wp_roles global
		global $wp_roles;
		$wp_roles = array();
		$this->assertEquals( ba_eas_tests_roles( 'default' ), ba_eas_get_editable_roles() );

		// Test default WP roles
		$this->assertEquals( ba_eas_tests_roles( 'default' ), ba_eas_get_editable_roles() );

		// Test with extra role
		add_filter( 'editable_roles', 'ba_eas_tests_roles_extra' );
		$this->assertEquals( ba_eas_tests_roles( 'extra' ), ba_eas_get_editable_roles() );
		remove_filter( 'editable_roles', 'ba_eas_tests_roles_extra', 10 );
	}

	/**
	 * @covers ::ba_eas_get_default_role_slugs
	 */
	function test_ba_eas_get_default_role_slugs() {

		// Test with empty $wp_roles global
		global $wp_roles;
		$wp_roles = array();
		$this->assertEquals( ba_eas_tests_slugs( 'default' ), ba_eas_get_default_role_slugs() );

		// Test default WP roles
		$this->assertEquals( ba_eas_tests_slugs( 'default' ), ba_eas_get_default_role_slugs() );

		// Test with extra role
		add_filter( 'editable_roles', 'ba_eas_tests_roles_extra' );
		$this->assertEquals( ba_eas_tests_slugs( 'extra' ), ba_eas_get_default_role_slugs() );
		remove_filter( 'editable_roles', 'ba_eas_tests_roles_extra', 10 );
	}

	/**
	 * @covers ::array_replace_recursive
	 */
	function test_array_replace_recursive() {
		$base = array(
			'test-1' => array(
				'test-1a' => 'test-1a',
			),
			'test-2' => 'test-2',
		);

		$replacements = array(
			'test-1' => array(
				'test-1a' => 'new-test-1a',
				'test-1b' => 'test-1b',
			),
			'test-3' => 'test-3',
		);

		$expected = array(
			'test-1' => array(
				'test-1a' => 'new-test-1a',
				'test-1b' => 'test-1b',
			),
			'test-2' => 'test-2',
			'test-3' => 'test-3',
		);

		$this->assertEquals( $expected, array_replace_recursive( $base, $replacements ) );
	}

	/**
	 * @covers ::ba_eas_update_nicename_cache
	 */
	function test_ba_eas_update_nicename_cache() {

		$this->assertEquals( $this->single_user_id, wp_cache_get( 'mastersplinter', 'userslugs' ) );

		$this->assertNull( ba_eas_update_nicename_cache( null ) );

		$user = get_userdata( $this->single_user_id );
		wp_update_user( array( 'ID' => $this->single_user_id, 'user_nicename' => 'master-splinter' ) );
		ba_eas_update_nicename_cache( $this->single_user_id, $user );
		$this->assertNotEquals( $this->single_user_id, wp_cache_get( 'mastersplinter', 'userslugs' ) );
		$this->assertEquals( $this->single_user_id, wp_cache_get( 'master-splinter', 'userslugs' ) );

		$user = get_userdata( $this->single_user_id );
		wp_update_user( array( 'ID' => $this->single_user_id, 'user_nicename' => 'mastersplinter' ) );
		ba_eas_update_nicename_cache( false, $user );
		$this->assertNotEquals( $this->single_user_id, (int) wp_cache_get( 'master-splinter', 'userslugs' ) );
		$this->assertEquals( $this->single_user_id, (int) wp_cache_get( 'mastersplinter', 'userslugs' ) );

		$user = get_userdata( $this->single_user_id );
		ba_eas_update_nicename_cache( $this->single_user_id, $user, 'splintermaster' );
		$this->assertNotEquals( $this->single_user_id, wp_cache_get( 'mastersplinter', 'userslugs' ) );
		$this->assertEquals( $this->single_user_id, wp_cache_get( 'splintermaster', 'userslugs' ) );
	}
}
