<?php
namespace Excellence\Trello\Setup;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
$table = $installer->getConnection()->newTable(
            $installer->getTable('excellence_trello_setupwizard')
    )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'order_state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
        )->addColumn(
            'trello_list_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
         );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable(
            $installer->getTable('excellence_trello_card')
    )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ]
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
        )->addColumn(
            'card_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
        );
$installer->getConnection()->createTable($table);

       /**
         * Create table 'trello_rules'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('trello_rules')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'trello_rules'
        )
        
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [ 'nullable' => false, ]
        )->addColumn(
            'rule_website_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Actions Serialized'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
        )->addColumn(
            'member',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
         )->addColumn(
            'list',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ]
         )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [ 'nullable' => false, ]
         );
     
        $installer->getConnection()->createTable($table);
  
  //actointab table
  
        $table = $installer->getConnection()->newTable(
            $installer->getTable('excellence_trello_actiontab')
    )->addColumn(
            'tab_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2000,
            [ 'nullable' => false, ]
        )->addColumn(
            'member',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2000,
            [ 'nullable' => false, ]
         )->addColumn(
            'list',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2000,
            [ 'nullable' => false, ]
         )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [ 'nullable' => false, ]
         );
$installer->getConnection()->createTable($table);

$installer->endSetup();
    }
}
