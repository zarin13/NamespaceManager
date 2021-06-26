<?php
/**
 * ManageNamespaces special page
 *
 * @file SpecialManageNamespaces.php
 * @ingroup Extensions
 */
class SpecialManageNamespaces extends SpecialPage {
	public function __construct() {
		parent::__construct( 'ManageNamespaces', 'managenamespaces' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 */
	public function execute( $sub ) {
		global $wgDBname;

		$this->checkPermissions();
		
		$out = $this->getOutput();

		$out->enableOOUI();

		$out->setPageTitle( $this->msg( 'managenamespaces-title' ) );
		$out->addWikiMsg( 'managenamespaces-intro' );

		$namespaceJsonTextArea = new OOUI\MultilineTextInputWidget([
            'rows' => 60,
            'value' => NamespaceManager::loadNamespaceData()
        ]);

        $submitBtn = new OOUI\ButtonInputWidget([
            'type' => 'submit'
        ]);

        $out->addHTML( <<<EOD
            <form action="Special:ManageNamespaces" method="post">
                $namespaceJsonTextArea
                $submitBtn
            </form>
EOD
// The preceding line should remain unindented, otherwise the code will break.
        );

		$out->addHTML( "<p>$btn1</p><p>$btn2</p><p>$btn3</p>" );
	}

	protected function getGroupName() {
		return 'other';
	}
}
