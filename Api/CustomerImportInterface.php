<?php

namespace WundermanThompson\CustomerImport\Api;

use Symfony\Component\Console\Output\OutputInterface;

interface CustomerImportInterface
{
    /**
     * Import customers from a CSV file.
     *
     * @param string $filePath Path to the CSV file
     * @param OutputInterface $output Console output interface
     * @return void
     */
    public function importCsv(string $filePath, OutputInterface $output): void;

    /**
     * Import customers from a JSON file.
     *
     * @param string $filePath Path to the JSON file
     * @param OutputInterface $output Console output interface
     * @return void
     */
    public function importJson(string $filePath, OutputInterface $output): void;
}
