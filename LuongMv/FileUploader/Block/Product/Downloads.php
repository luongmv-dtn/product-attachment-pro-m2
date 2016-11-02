<?php
namespace LuongMv\FileUploader\Block\Product;

use Magento\Framework\View\Element\Template;

/**
 * Class Downloads
 * @package LuongMv\FileUploader\Block\Product
 */
class Downloads extends Template
{

    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Downloads constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \LuongMv\FileUploader\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \LuongMv\FileUploader\Helper\Data $helper
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_helper = $helper;

        parent::__construct($context);

        $this->setTabTitle();
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $this->setTitle(__('Downloads'));
    }


    /**
     * Return current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    public function getProductAttachments()
    {
        $attach = array();
        $_product = $this->getProduct();
        $data = $this->_helper->getFilesByProductId($_product->getId());
        //$totalFiles = $data['totalRecords'];
        if (isset($data['items'])) {
            $record = $data['items'];
            $i = 0;
            foreach ($record as $rec) {
                $i++;
                $file = $this->_helper->getFilesHtml($rec['uploaded_file'], $rec['title'], $i, true, $rec['content_disp'], true);
                $attach[] = array('title' => $rec['title'], 'file' => $file, 'content' => $rec['file_content']);
            }
        }
        return $attach;
    }
}
