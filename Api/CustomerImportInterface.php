<?php

namespace WundermanThompson\CustomerImport\Api;

use Symfony\Component\Console\Output\OutputInterface;

interface CustomerImportInterface
{
    /**
     * Import customers from a file.
     *
     * @param string $filePath Path to the file
     * @param OutputInterface $output Console output interface
     * @return void
     */
    public function import(string $filePath, OutputInterface $output): void;
}
