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
	 * Documentation link is shown, and page renders correctly.
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testDocumentationLink( AcceptanceTester $I ) {
		$I->wantToTest( 'Documentation link is shown, and page renders correctly.' );

		$I->resizeWindow( 1200, 800 );

		$I->loginAsAdmin();
		$I->amOnAdminPage( '/' );

		// See the Documentation link in menu.
		$I->seeLink( 'Documentation' );

		// Click the link to open the documentation.
		$I->click( 'Documentation' );

		// Doc sets are visible in submenu.
		$I->seeLink( 'Developer Documentation' );
		$I->seeLink( 'User Guides' );

		// See the main title.
		$I->seeElement( '.altis-ui__doc-title' );

		// Click to go to CMS Module docs.
		$I->see( 'CMS', 'li' );
		$I->click( 'CMS' );

		// See the CMS H1 title.
		$I->see( 'CMS', 'h1' );

		// User guide section is visible.
		$I->click( 'User Guides' );
		$I->see( 'User Guides', 'h1' );
	}

}
