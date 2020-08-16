<?php
namespace Majidian\Newsletter\Model;

use Majidian\Newsletter\Api\SubscribeManagementInterface;
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

class SubscribeManagement implements SubscribeManagementInterface
{
    /**
     * @var Logger
     */
    private $_logger;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $rule;
    /**
     * @var CouponRepositoryInterface
     */
    protected $coupon;

    /**
     * SubscribeManagement constructor.
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        RuleRepositoryInterface $rule,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CouponRepositoryInterface $coupon,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Logger $logger
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->rule = $rule;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->coupon = $coupon;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->_logger = $logger;
    }

    /**
     * @api
     *
     * @param string $email
     * @return string
     */
    public function subscribe(
        string $email
    ) {
        if (!empty($email)) {
            try {
                $subscriber = $this->subscriberFactory->create();
                $subscriber->loadByEmail($email);
                if ($subscriber->isSubscribed()) {
                    $status = 2;
                } else {
                    $subscriber->subscribe($email);
                    $status = 1;
                    $ruleId = $this->getRuleId();
                    if ($ruleId !== null) {
                        $coupon = $this->getCouponCode($ruleId);
                        $this->sendEmail($email, $coupon);
                    }
                }
            } catch (LocalizedException $e) {
                $status = 0;
                $this->_logger->error(__('LocalizedException: %1 ', $e->getMessage()));
            } catch (\Exception $e) {
                $status = 0;
                $this->_logger->error(__('Exception: %1 ', $e->getMessage()));
            }
        }
        return (string) $status;
    }

    /**
     * @param $email
     * @param $coupon
     * @return bool
     */
    private function sendEmail($email, $coupon): bool
    {
        $return = false;
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('majidian_newsletter_coupon')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars([
                    'subject' => 'New Coupons',
                    'coupon' => $coupon
                ])
                ->setFrom([
                    'name' => 'David Majidian',
                    'email' => 'dmajidian@hotmail.com'
                ])
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $return = true;
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->_logger->error(__('MailException: %1 ', $e->getMessage()));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->_logger->error(__('NoSuchEntityException: %1 ', $e->getMessage()));
        } catch (LocalizedException $e) {
            $this->_logger->error(__('LocalizedException: %1 ', $e->getMessage()));
        }

        return $return;
    }

    /**
     * @return int|null
     * @throws LocalizedException
     */
    private function getRuleId()
    {
        /** @var SearchCriteria $criteria */
        $criteria = $this->searchCriteriaBuilder
            ->addFilter(InstallSchema::FIELD, 1)
            ->create();
        /** @var \Magento\SalesRule\Api\Data\RuleSearchResultInterface $rules */
        $rules = $this->rule->getList($criteria)->setTotalCount(1)->getItems();
        /** @var \Magento\SalesRule\Model\Data\Rule $rule */
        $rule = reset($rules);
        if ($rule) {
            return $rule->getRuleId();
        }
        return null;
    }

    /**
     * @param $ruleId
     * @return string|null
     * @throws LocalizedException
     */
    private function getCouponCode($ruleId)
    {
        /** @var SearchCriteria $criteria */
        $criteria = $this->searchCriteriaBuilder
            ->addFilter('rule_id', $ruleId)
            ->create();
        $coupons = $this->coupon->getList($criteria)->getItems();
        $coupon = reset($coupons);

        return $coupon->getCode();
    }
}
