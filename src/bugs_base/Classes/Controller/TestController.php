<?php

namespace Fwg\BugsBase\Controller;

use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TestController extends ActionController
{
    public function indexAction()
    {
        $this->view->assign('variable', __CLASS__ . '::' . __FUNCTION__);

        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '12.4', '>=')) {
            return new HtmlResponse($this->view->render());
        }
    }
}
