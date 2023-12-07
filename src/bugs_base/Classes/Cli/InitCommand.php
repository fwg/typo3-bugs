<?php

namespace Fwg\BugsBase\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
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

        $this->io->writeln('add folder at root level');
        $folderUid = $this->create('pages', 0, 'Test folder', ['doktype' => 254]);

        $this->io->writeln('add page below Home node');
        $pageUid = $this->create('pages', 1, 'Test page');

        $this->io->writeln('translate page to de and fr');
        $this->localize('pages', $pageUid, 1);
        $this->localize('pages', $pageUid, 2);

        $this->io->writeln('copy translated page to folder');
        $this->copy('pages', $pageUid, $folderUid);

        $this->io->writeln('add test group with modules web_layout, web_list, and tables_select pages,tt_content');
        $group = $this->create('be_groups', 0, 'Test group', [
            'db_mountpoints' => '1,' . $folderUid,
            'tables_select' => 'pages,tt_content'
        ]);

        $this->io->writeln('add user limited to second language with group test');
        $user = $this->create('be_users', 0, 'testuser', [
            'usergroup' => $group,
            'password' => 'TestUser123!',
            'options' => 3,
            'userMods' => 'web_layout,web_list',
            'allowed_languages' => '1',
            'disable' => 0,
        ]);

        $this->io->writeln('update all pages to be seen by test user');
        $query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $query->update('pages')
            ->set('hidden', 0)
            ->set('perms_everybody', 1)
            ->where('1=1');
        $query->executeStatement();

        $this->io->success("Initialization successful.\nPlease log in to TYPO3 with testuser / TestUser123!");
        return 0;
    }

    protected function create(string $table, int $pid, string $title, array $props = []): int
    {
        $dh = $this->getDataHandler();
        $dh->start([
            $table => [
                'NEW123' => array_merge([
                    'pid' => $pid,
                    $GLOBALS['TCA'][$table]['ctrl']['label'] => $title,
                ], $props)
            ]
        ], []);
        $dh->process_datamap();

        if ($dh->errorLog) {
            $this->io->error($dh->errorLog);
            return 0;
        }

        return (int)$dh->substNEWwithIDs['NEW123'];
    }

    protected function localize(string $table, int $uid, int $language): bool
    {
        $dh = $this->getDataHandler();
        $dh->start([], [
            $table => [
                $uid => [
                    'localize' => $language
                ]
            ]
        ]);
        $dh->process_cmdmap();

        if ($dh->errorLog) {
            $this->io->error($dh->errorLog);
            return false;
        }

        return true;
    }

    protected function copy(string $table, int $uid, int $newPid): bool
    {
        $dh = $this->getDataHandler();
        $dh->start([], [
            $table => [
                $uid => [
                    'copy' => $newPid
                ]
            ]
        ]);
        $dh->process_cmdmap();

        if ($dh->errorLog) {
            $this->io->error($dh->errorLog);
            return false;
        }

        return true;
    }

    protected function getDataHandler(): DataHandler
    {
        return GeneralUtility::makeInstance(DataHandler::class);
    }
}
