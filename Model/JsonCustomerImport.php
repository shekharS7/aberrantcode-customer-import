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
namespace AberrantCode\CustomerImport\Model;
 
use Exception;
use Generator;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Psr\Log\LoggerInterface;
use AberrantCode\CustomerImport\Api\CustomerImportInterface;
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
 
    // get store and website ID
    $store = $this->storeManagerInterface->getStore();
    $websiteId = (int) $this->storeManagerInterface->getWebsite()->getId();
    $storeId = (int) $store->getId();
    $generalCustomerGroupId = 1;

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
