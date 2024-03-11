<?php
 
namespace WundermanThompson\CustomerImport\Model;
 
use Exception;
use Generator;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Psr\Log\LoggerInterface;
use WundermanThompson\CustomerImport\Api\CustomerImportInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;


class CsvCustomerImport implements CustomerImportInterface
{
  private $file;
  private $storeManagerInterface;
  private $output;
  protected $customerRepository;
  protected $state;
  protected $customerFactory;
  protected $logger;
  private $groupRepository;
  private $websiteRepository;

 
  public function __construct(
    File $file,
    StoreManagerInterface $storeManagerInterface,
    CustomerInterfaceFactory $customerFactory,
    CustomerRepositoryInterface $customerRepository,
    State $state,
    LoggerInterface $logger,
    WebsiteRepositoryInterface $websiteRepository
  ) {
      $this->file = $file;
      $this->storeManagerInterface = $storeManagerInterface;
      $this->customerFactory = $customerFactory;
      $this->customerRepository = $customerRepository;
      $this->state = $state;
      $this->logger = $logger;
      $this->websiteRepository = $websiteRepository;
    }

  public function import(string $filePath, OutputInterface $output): void
  {
    $this->output = $output;
 
    // get store 
    $store = $this->storeManagerInterface->getStore();
    $storeId = (int) $store->getId();
    
    $generalCustomerGroupId = 1;

     // Assign default website
     $defaultWebsiteCode = 'base'; // Change this if the default website has a different code
     $defaultWebsite = $this->websiteRepository->get($defaultWebsiteCode);
     $defaultWebsiteId = $defaultWebsite->getId();
 
    // read the csv header
    $header = $this->readCsvHeader($filePath)->current();
 
    // read the csv file and skip the first (header) row
    $row = $this->readCsvRows($filePath, $header);
    $row->next();
 
    // while the generator is open, read current row data, create a customer and resume the generator
    while ($row->valid()) {
        $data = $row->current();
        $this->createCustomer($data, $defaultWebsiteId, $storeId, $generalCustomerGroupId);
        $row->next();
    }
  }

  private function readCsvRows(string $file, array $header): ?Generator
  {
    $handle = fopen($file, 'rb');
 
    while (!feof($handle)) {
        $data = [];
        $rowData = fgetcsv($handle);
        if ($rowData) {
            foreach ($rowData as $key => $value) {
                $data[$header[$key]] = $value;
            }
            yield $data;
        }
    }
 
    fclose($handle);
  }
  
  private function readCsvHeader(string $file): ?Generator
  {
    $handle = fopen($file, 'rb');
 
    while (!feof($handle)) {
        yield fgetcsv($handle);
    }
 
    fclose($handle);
  }
  private function createCustomer(array $data, int $defaultWebsiteId, int $storeId, int $generalCustomerGroupId): void
  {
    $customer = $this->customerFactory->create();
    $customer->setFirstname($data['fname']);
    $customer->setLastname($data['lname']);
    $customer->setEmail($data['emailaddress']);
    $customer->setWebsiteId($defaultWebsiteId);
    $customer->setStoreId($storeId);
    $customer->setGroupId($generalCustomerGroupId);
    $this->customerRepository->save($customer);
       
  }
 }
