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

        $out->addHTML(new OOUI\FormLayout([
            'method' => 'POST',
            'action' => 'Special:ManageNamespaces',
            'items' => [
                new OOUI\FieldsetLayout([
                    'label' => 'Namespaces',
                    'items' => [
                        new OOUI\FieldLayout(
                            new OOUI\MultilineTextInputWidget([
                                'rows' => 60,
                                'value' => NamespaceManager::loadNamespaceDataRaw()
                            ]),
                            [
                                'label' => 'Namespaces JSON file',
                                'align' => 'top',
                            ]
                        ),
                        new OOUI\FieldLayout(
                            new OOUI\ButtonInputWidget([
                                'name' => 'save',
                                'label' => 'Save JSON',
                                'type' => 'submit',
                                'flags' => ['primary', 'progressive'],
                                'icon' => 'check',
                            ]),
                            [
                                'label' => null,
                                'align' => 'top',
                            ]
                        ),
                    ]
                ])
            ]
        ]));
	}

	protected function getGroupName() {
		return 'other';
	}
}
