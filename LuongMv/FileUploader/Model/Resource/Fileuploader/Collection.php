<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace LuongMv\FileUploader\Model\Resource\Fileuploader;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'fileuploader_id';

    protected function _construct()
    {
        $this->_init('LuongMv\FileUploader\Model\Fileuploader', 'LuongMv\FileUploader\Model\Resource\Fileuploader');
        //$this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
