<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace BehatExtension\DoctrineDataFixturesExtension\Service\Backup;

use Symfony\Component\Process\Process;

/**
 * Mysql dump backup.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
final class MysqlDumpBackup implements BackupInterface
{
    /**
     * @var string
     */
    private $mysqldumpBin = 'mysqldump';

    /**
     * @var string
     */
    private $mysqlBin = 'mysql';

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'mysql';
    }

    /**
     * @param string $command
     *
     * @throws \RuntimeException
     *
     * @return null|int
     */
    private function runCommand(string $command): ?int
    {
        $process = new Process($command);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getExitCode();
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $database, string $file, array $params): void
    {
        $command = sprintf('%s %s > %s', $this->mysqldumpBin, escapeshellarg($database), escapeshellarg($file));

        if (isset($params['host']) && strlen($params['host'])) {
            $command .= sprintf(' --host=%s', escapeshellarg($params['host']));
        }

        if (isset($params['user']) && strlen($params['user'])) {
            $command .= sprintf(' --user=%s', escapeshellarg($params['user']));
        }

        if (isset($params['password']) && strlen($params['password'])) {
            $command .= sprintf(' --password=%s', escapeshellarg($params['password']));
        }

        if (isset($params['port'])) {
            $command .= sprintf(' -P%s', escapeshellarg($params['port']));
        }

        $this->runCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function restore(string $database, string $file, array $params): void
    {
        $command = sprintf('%s %s < %s', $this->mysqlBin, escapeshellarg($database), escapeshellarg($file));

        if (isset($params['host']) && strlen($params['host'])) {
            $command .= sprintf(' --host=%s', escapeshellarg($params['host']));
        }

        if (isset($params['user']) && strlen($params['user'])) {
            $command .= sprintf(' --user=%s', escapeshellarg($params['user']));
        }

        if (isset($params['password']) && strlen($params['password'])) {
            $command .= sprintf(' --password=%s', escapeshellarg($params['password']));
        }

        if (isset($params['port'])) {
            $command .= sprintf(' -P%s', escapeshellarg($params['port']));
        }

        $this->runCommand($command);
    }
}
