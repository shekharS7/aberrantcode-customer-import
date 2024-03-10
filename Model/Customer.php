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


class Customer
{
  private $file;
  private $storeManagerInterface;
  private $output;
  protected $customerRepository;
  protected $state;
  protected $customerFactory;
  protected $logger;

 
  public function __construct(
    File $file,
    StoreManagerInterface $storeManagerInterface,
    CustomerInterfaceFactory $customerFactory,
    CustomerRepositoryInterface $customerRepository,
    State $state,
    LoggerInterface $logger
  ) {
      $this->file = $file;
      $this->storeManagerInterface = $storeManagerInterface;
      $this->customerFactory = $customerFactory;
      $this->customerRepository = $customerRepository;
      $this->state = $state;
      $this->logger = $logger;
    }

  public function importCsv(string $filePath, OutputInterface $output): void
  {
    $this->output = $output;
 
    // get store and website ID
    $store = $this->storeManagerInterface->getStore();
    $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
    $storeId = (int) $store->getId();
 
    // read the csv header
    $header = $this->readCsvHeader($filePath)->current();
 
    // read the csv file and skip the first (header) row
    $row = $this->readCsvRows($filePath, $header);
    $row->next();
 
    // while the generator is open, read current row data, create a customer and resume the generator
    while ($row->valid()) {
        $data = $row->current();
        $this->createCustomer($data, $websiteId, $storeId);
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
  private function createCustomer(array $data, int $websiteId, int $storeId): void
  {
    $customer = $this->customerFactory->create();
    $customer->setFirstname($data['fname']);
    $customer->setLastname($data['lname']);
    $customer->setEmail($data['emailaddress']);
    $customer->setWebsiteId($websiteId);
    $customer->setStoreId($storeId);
    $this->customerRepository->save($customer);
       
  }
  public function importJson(string $filePath, OutputInterface $output): void
  {
    $this->output = $output;
 
    // get store and website ID
    $store = $this->storeManagerInterface->getStore();
    $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
    $storeId = (int) $store->getId();

    // read the json
    if ($this->file->fileExists($filePath)) {
      $jsonContent = $this->file->read($filePath);
      $jsonData = json_decode($jsonContent, true);
    }
    // read current row data, create a customer
    foreach ($jsonData as $data) {
        $this->createCustomer($data, $websiteId, $storeId);
    }
  }
 
 }
