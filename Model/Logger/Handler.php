<?php
namespace Majidian\Newsletter\Model\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    /** @var string  */
    protected $fileName = '/var/log/newsletter.log';
    /** @var int */
    protected $loggerType = Logger::DEBUG;
}
