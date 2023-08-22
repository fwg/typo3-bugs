<?php

namespace Fwg\BugsExt\Controller;

use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3Fluid\Fluid\View\AbstractTemplateView;

class TestController extends ActionController
{
    public function indexAction()
    {
        $this->view->assign('variable', __CLASS__ . '::' . __FUNCTION__);

        if ($this->view instanceof AbstractTemplateView) {
            $this->view->assign('paths',  $this->view->getTemplatePaths());
        }

        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '12.4', '>=')) {
            return new HtmlResponse($this->view->render());
        }
    }
}
