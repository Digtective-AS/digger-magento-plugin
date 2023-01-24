<?php

namespace Digtective\Digger\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            "digger_id",
            [
                'type' => Table::TYPE_TEXT,
                'comment' => "Digtective digger app lead id"
            ]
        );

        $setup->endSetup();
    }
}
