<?php
 
namespace WoundermanThompson\CustomerImport\Model;
 
use Exception;
use Generator;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class Customer
{
  private $file;
  private $storeManagerInterface;
  private $customerImport;
  private $output;
  protected $customerRepository;
 
  public function __construct(
    File $file,
    StoreManagerInterface $storeManagerInterface,
    CustomerRepositoryInterface $customerRepository
  ) {
      $this->file = $file;
      $this->storeManagerInterface = $storeManagerInterface;
      $this->customerRepository = $customerRepository;
    }

  public function import(string $fixture, OutputInterface $output): void
  {
    $this->output = $output;
 
    // get store and website ID
    $store = $this->storeManagerInterface->getStore();
    $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
    $storeId = (int) $store->getId();
 
    // read the csv header
    $header = $this->readCsvHeader($fixture)->current();
 
    // read the csv file and skip the first (header) row
    $row = $this->readCsvRows($fixture, $header);
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
    $customer = $this->customerRepository->create();
    $customer->setFirstname($data['fname']);
    $customer->setLastname($data['lname']);
    $customer->setEmail($data['email']);
    $customer->setWebsiteId($websiteId);
    $customer->setStoreId($storeId]);
    $this->customerRepository->save($customer);
       
  }
}
