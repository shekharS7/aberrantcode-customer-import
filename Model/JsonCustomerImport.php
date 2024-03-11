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
use Magento\Customer\Api\CustomerGroupManagementInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;


class JsonCustomerImport implements CustomerImportInterface
{
  private $file;
  private $storeManagerInterface;
  private $output;
  protected $customerRepository;
  protected $state;
  protected $customerFactory;
  protected $logger;
  private $customerGroupManagement;
  private $websiteRepository;

 
  public function __construct(
    File $file,
    StoreManagerInterface $storeManagerInterface,
    CustomerInterfaceFactory $customerFactory,
    CustomerRepositoryInterface $customerRepository,
    State $state,
    LoggerInterface $logger,
    CustomerGroupManagementInterface $customerGroupManagement,
    WebsiteRepositoryInterface $websiteRepository
  ) {
      $this->file = $file;
      $this->storeManagerInterface = $storeManagerInterface;
      $this->customerFactory = $customerFactory;
      $this->customerRepository = $customerRepository;
      $this->state = $state;
      $this->logger = $logger;
      $this->customerGroupManagement = $customerGroupManagement;
      $this->websiteRepository = $websiteRepository;
    }

  public function import(string $filePath, OutputInterface $output): void
  {
    $this->output = $output;
 
    // get store and website ID
    $store = $this->storeManagerInterface->getStore();
    $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
    $storeId = (int) $store->getId();
    
    // Assign general customer group
    $generalCustomerGroupCode = 'General'; // Change this if the general customer group has a different code
    $generalCustomerGroup = $this->customerGroupManagement->getGroup($generalCustomerGroupCode);
    $generalCustomerGroupId = $generalCustomerGroup->getId();

     // Assign default website
     $defaultWebsiteCode = 'base'; // Change this if the default website has a different code
     $defaultWebsite = $this->websiteRepository->get($defaultWebsiteCode);
     $defaultWebsiteId = $defaultWebsite->getId();
    
    // read the json
    if ($this->file->fileExists($filePath)) {
      $jsonContent = $this->file->read($filePath);
      $jsonData = json_decode($jsonContent, true);
    }

    // read current row data, create a customer
    foreach ($jsonData as $data) {
        $this->createCustomer($data, $defaultWebsiteId, $storeId, $generalCustomerGroupId);
    }
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
