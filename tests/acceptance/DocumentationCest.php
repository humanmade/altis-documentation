<?php
/**
 * Tests for Altis Documentation module.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Test documentation module renders correctly.
 */
class DocumentationCest {

	/**
	 * Documantation link is shown.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 */
	public function testDocumentationLink( AcceptanceTester $I ) {
		$I->wantToTest( 'Documantation link is shown.' );
		$I->loginAsAdmin();
		$I->amOnAdminPage( '/' );

		$I->moveMouseOver( '.altis-logo-wrapper' );
		$I->seeLink( 'Documentation' );
	}

	/**
	 * Documentation module renders correctly.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testDocumentationRender( AcceptanceTester $I ) {
		$I->wantToTest( 'Documentation module renders correctly.' );
		$I->loginAsAdmin();
		$I->amOnAdminPage( 'admin.php?page=altis-documentation' );

		// Documentation title.
		$I->seeElement( '.altis-ui__doc-title' );
	}

	/**
	 * Navigate to CMS module and confirm there is content.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testDocumentationNavigation( AcceptanceTester $I ) {
		$I->wantToTest( 'Navigate to CMS module and confirm there is content.' );
		$I->loginAsAdmin();
		$I->amOnAdminPage( 'admin.php?page=altis-documentation' );

		// CMS Module.
		$I->see( 'CMS', 'li' );
		$I->click( 'CMS' );

		// See the CMS H! title.
		$I->see( 'CMS', 'h1' );
	}



}
