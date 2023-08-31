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

        $dh = GeneralUtility::makeInstance(DataHandler::class);
        $dh->start([
            // ...
        ], []);
        $dh->process_datamap();

        if ($dh->errorLog) {
            $this->io->error($dh->errorLog);
            return 1;
        }

        $query = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_template');
        $query->from('sys_template')
            ->select('*')
            ->where($query->expr()->eq('uid', 1));
        $template = $query->execute()->fetchAssociative();

        if (!$template) {
            $this->io->error('Could not load sys_template:1');
            return 1;
        }

        $includes = GeneralUtility::trimExplode(',', $template['include_static_file']);
        $ts = 'EXT:bugs_base/Configuration/TypoScript/';

        if (!in_array($ts, $includes)) {
            $includes[] = $ts;

            $query->resetQueryParts();
            $query->update('sys_template')
                ->set('include_static_file', join(',', $includes))
                ->where($query->expr()->eq('uid', 1));

            if (!$query->execute()) {
                $this->io->error('Could not update sys_template:1');
            }
        }

        $this->io->success('Initialization successful.');
        return 0;
    }
}
