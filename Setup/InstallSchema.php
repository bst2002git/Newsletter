<?php
namespace Majidian\Newsletter\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    const FIELD = 'newsletter';
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            self::FIELD,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 1,
                'unsigned' => true,
                'nullable' => true,
                'default' => '0',
                'comment' => self::FIELD
            ]
        );

        $installer->endSetup();
    }
}
