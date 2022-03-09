<?php
/**
 * Tests for Altis Documentation module.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Test core module admin features.
 */
class DocumentationCest {

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

}
