<?php

namespace Fwg\BugsBase\Cli;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;
use TYPO3\CMS\Frontend\Typolink\PageLinkBuilder;

class TriggerBugCommand extends Command
{
    protected SymfonyStyle $io;

    protected function configure()
    {
        $this->setDescription('Trigger Bug #90600');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CommandLineUserAuthentication $user */
        $user = $GLOBALS['BE_USER'];
        $user->authenticate();
        $this->io = new SymfonyStyle($input, $output);

        $this->requestPage(1);
        // page has cachePeriod 1 second when cookie is present (withCookie: true)
        $this->requestPage(1, true);
        // simulate the modification_date being changed by the FAL indexer due to
        // file mtime change.
        $this->updateSysFileModificationDate();
        // let cookie page expire
        sleep(2);
        // regenerate page - this changes processed file checksum
        $this->requestPage(1, true);

        $this->io->success('Bug successfully prepared.');
        return 0;
    }

    /**
     * @throws Exception
     */
    protected function updateSysFileModificationDate(): void
    {

        $query = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');
        $query->update('sys_file')
            ->set('modification_date', time())
            ->where($query->expr()->eq('uid', 1));
        $updated = $query->execute();

        if (!$updated) {
            throw new Exception('Could not update sys_file:1');
        }
    }

    protected function requestPage(int $pageUid, bool $withCookie = false): void
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($pageUid);
        $_SERVER['HTTP_HOST'] = $site->getBase()->getHost();
        $request = ServerRequestFactory::fromGlobals();
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        if (method_exists($cObj, 'setRequest')) {
            $cObj->setRequest($request);
        } else {
            $GLOBALS['TYPO3_REQUEST'] = $request;
        }

        $cObj->start([]);
        $pageLinkBuilder = GeneralUtility::makeInstance(PageLinkBuilder::class, $cObj);
        $linkDetails = ['pageuid' => $pageUid];
        $result = $pageLinkBuilder->build($linkDetails, '', '', ['forceAbsoluteUrl' => 1]);

        if (interface_exists(LinkResultInterface::class) && $result instanceof LinkResultInterface) {
            $url = $result->getUrl();
        } else {
            $url = $result[0];
        }

        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $options = [];

        if ($withCookie) {
            $options = [
                'headers' => [
                    'Cookie' => 'test_90600=1'
                ]
            ];
        }

        $response = $requestFactory->request($url, 'GET', $options);
    }
}
