<?php
/**
 * Copyright © Shekhar Suman, 2024. All rights reserved.
 * See COPYING.txt for license details.
 * 
 * @package     AberrantCode_CustomerImport
 * @version     1.0.0
 * @license     MIT License (http://opensource.org/licenses/MIT)
 * @autor       Shekhar Suman
 */
namespace AberrantCode\CustomerImport\Api;

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
