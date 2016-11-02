<?php

namespace LuongMv\FileUploader\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('luong_fileuploader')
        )->addColumn(
            'fileuploader_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            array('identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true),
            'FileUploader ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Title'
        )->addColumn(
            'uploaded_file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            array('default' => null),
            'Uploaded File'
        )->addColumn(
            'file_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            array('default' => null),
            'File Content'
        )->addColumn(
            'product_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            array('default' => null),
            'Product Ids'
        )->addColumn(
            'file_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            4,
            array('default' => '2'),
            'File Status'
        )->addColumn(
            'content_disp',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            4,
            array('default' => '0'),
            'Content Disp'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            array('nullable' => false, 'default' => '0'),
            'Sort Order'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array('nullable' => true, 'default' => null),
            'Modification Time'
        )->setComment(
            'FieUploader Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
