<?php

namespace Fwg\BugsBase\Cli;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InitCommand extends Command
{
    protected SymfonyStyle $io;

    protected function configure()
    {
        $this->setDescription('Initialize database for bug reproduction.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CommandLineUserAuthentication $user */
        $user = $GLOBALS['BE_USER'];
        $user->authenticate();

        $this->io = new SymfonyStyle($input, $output);

        try {
            $this->initTemplate();
            $this->initContent();
        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            return 1;
        }

        $this->io->success('Initialization successful.');
        return 0;
    }

    /**
     * @throws Exception
     */
    protected function initContent(): void
    {
        $rf = GeneralUtility::makeInstance(ResourceFactory::class);
        $storage = $rf->getStorageObject(1);
        $folder = $storage->getDefaultFolder();

        if (!$storage->hasFileInFolder('600x400.png', $folder)) {
            $local = GeneralUtility::getFileAbsFileName('EXT:bugs_base/Resources/Public/Images/600x400.png');
            $file = $storage->addFile($local, $folder, '600x400.png', DuplicationBehavior::CANCEL, false);
        } else {
            $file = $folder->getFile('600x400.png');
        }

        $dh = $this->processDatamap('tt_content', 'NEW1', [
            'pid' => 1,
            'CType' => 'textpic',
            'bodytext' => '<p>A square processed file. If the bug is present, the image is broken with a 404 src.</p>'
        ]);
        $tt_content_uid = $dh->substNEWwithIDs['NEW1'];

        $dh = $this->processDatamap('sys_file_reference', 'NEW2', [
            'pid' => 1,
            'uid_local' => $file->getUid(),
            'uid_foreign' => $tt_content_uid,
            'table_local' => 'sys_file',
            'tablenames' => 'tt_content',
            'fieldname' => 'image',
            'crop' => '{"default":{"cropArea":{"height":1,"width":0.6666666666666666,"x":0.17,"y":0},"selectedRatio":"1:1","focusArea":null}}'
        ]);
        $ref_uid = $dh->substNEWwithIDs['NEW2'];

        $this->processDatamap('tt_content', $tt_content_uid, [
            'image' => $ref_uid
        ]);
    }

    /**
     * @throws Exception
     */
    protected function processDatamap($table, $uid, $props): DataHandler
    {
        /** @var DataHandler $dh */
        $dh = GeneralUtility::makeInstance(DataHandler::class);
        $dh->start([
            $table => [
                $uid => $props
            ],
        ], []);
        $dh->process_datamap();

        if ($dh->errorLog) {
            throw new Exception(join(PHP_EOL, $dh->errorLog));
        }

        return $dh;
    }

    /**
     * @throws Exception
     */
    protected function initTemplate(): void
    {

        $query = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_template');
        $query->from('sys_template')
            ->select('*')
            ->where($query->expr()->eq('uid', 1));
        $template = $query->execute()->fetchAssociative();

        if (!$template) {
            throw new Exception('Could not load sys_template:1');
        }

        $includes = GeneralUtility::trimExplode(',', $template['include_static_file']);
        $ts = 'EXT:bugs_base/Configuration/TypoScript/';

        if (!in_array($ts, $includes)) {
            $includes[] = $ts;

            $query->resetQueryParts();
            $query->update('sys_template')
                ->set('include_static_file', join(',', $includes))
                ->set('config', "")
                ->where($query->expr()->eq('uid', 1));

            if (!$query->execute()) {
                throw new Exception('Could not update sys_template:1');
            }
        }
    }
}
