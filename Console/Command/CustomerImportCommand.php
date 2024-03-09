<?php

namespace WundermanThompson\CustomerImport\Console\Command;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Filesystem;
use WundermanThompson\CustomerImport\Model\Customer;


class CustomerImportCommand extends Command
{
    protected static $defaultName = 'customer:import';

    protected $customerRepository;
    private $filesystem;
    private $customer;
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Filesystem $filesystem,
        Customer $customer
    ) {
        parent::__construct();
        $this->customerRepository = $customerRepository;
        $this->filesystem = $filesystem;
        $this->customer = $customer;
    }

    protected function configure()
    {
        $this->setDescription('Import customers from a CSV or JSON file')
             ->setHelp('This command allows you to import customers from a CSV or JSON file')
             ->addArgument('profile', InputArgument::REQUIRED, 'Profile name')
             ->addArgument('source', InputArgument::REQUIRED, 'Source file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profile = $input->getArgument('profile');
        $source = $input->getArgument('source');

        // Implement logic to parse the input file based on the profile
        // Extract customer data from the file
        // Import customers using customer repository
        try { 
            $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $filepath = $mediaDir->getAbsolutePath() . $source;
            if ($profile == 'sample-csv'){
                $this->customer->importCsv($filepath, $output);
            } 
            if ($profile == 'sample-json'){
                $this->customer->importJson($filepath, $output);
            }
            $output->writeln('<info>Customers imported successfully.</info>');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Command::FAILURE;
        }
    }
}
