<?php
namespace Majidian\Newsletter\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Majidian\Newsletter\Model\SubscribeManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Majidian\Newsletter\Setup\InstallSchema;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Majidian\Newsletter\Model\Logger\Logger;

class TestSubscribeManagement extends TestCase
{
    protected $objectManager;
    protected $subscriberFactory;
    protected $ruleRepositoryInterface;
    protected $searchCriteriaInterface;
    protected $couponRepositoryInterface;
    protected $scopeConfigInterface;
    protected $storeManagerInterface;
    protected $transportBuilder;
    private $_logger;
    private $subscriberManagement;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->subscriberFactory = $this->getMockBuilder(SubscriberFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['subscribe'])
            ->getMock();
        $this->ruleRepositoryInterface = $this->getMockBuilder(RuleRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList', 'getById', 'save', 'deleteById'])
            ->getMock();
        $this->searchCriteriaInterface = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addFilter', 'create'])
            ->getMock();
        $this->couponRepositoryInterface = $this->getMockBuilder(CouponRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList', 'getById', 'save', 'deleteById'])
            ->getMock();
        $this->scopeConfigInterface = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerInterface = $this->getMockBuilder(StoreManagerInterface::class)
        ->disableOriginalConstructor()
        ->getMock();
        $this->transportBuilder = $this->getMockBuilder(TransportBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList'])
            ->getMock();
        $this->_logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subscriberManagement = $this->getMockBuilder(SubscribeManagement::class)
            ->setConstructorArgs([
                $this->subscriberFactory,
                $this->ruleRepositoryInterface,
                $this->searchCriteriaInterface,
                $this->couponRepositoryInterface,
                $this->scopeConfigInterface,
                $this->storeManagerInterface,
                $this->transportBuilder,
                $this->_logger
            ])
            ->setMethods(['subscribe', 'getRuleId', 'getCouponCode', 'sendEmail'])
            ->getMock();
    }


    public function testSubscribe()
    {
        $email = 'dmajidian@hotmail.com';

        $result = $this->subscriberManagement->subscribe($email);

        $expectedResult = '2';
        $this->assertEquals($expectedResult, $result);
    }

}
