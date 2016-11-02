<?php

namespace LuongMv\FileUploader\Block\Adminhtml\Form\Grid\Renderer;

use Magento\Store\Model\StoreManagerInterface;

class Link extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $_helper;

    /**
     * Link constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param StoreManagerInterface $storemanager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \LuongMv\FileUploader\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        \Magento\Framework\Filesystem $filesystem,
        \LuongMv\FileUploader\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }
    
    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $link = $this->_helper->getDownloadFileUrl($this->_getValue($row), $row->getData('content_disp'));

        if ($this->_getValue($row)) {
            return '<a href="'.$link.'" target="_blank">'.$this->_getValue($row).'</a>';
        }
    }
}
