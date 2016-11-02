<?php
namespace LuongMv\FileUploader\Block\Adminhtml\Customformfield\Edit\Renderer;

use Magento\Framework\Data\Form;
use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;

class CustomRenderer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $_helper;

    /**
     * CustomRenderer constructor.
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Magento\Framework\Registry $registry
     * @param \LuongMv\FileUploader\Helper\Data $helper
     * @param array $data
     */
    public function __construct(Factory $factoryElement, CollectionFactory $factoryCollection, Escaper $escaper, \Magento\Framework\Registry $registry, \LuongMv\FileUploader\Helper\Data $helper, array $data)
    {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->setType('file');
        $this->setExtType('fileuploader');
    }
    
    public function getElementHtml()
    {
        $html = '';
        if ($this->getValue()) {
            $url = $this->_getUrl();
            $id = $this->getHtmlId();
            $title = 'File Uploader';
            $disposition = 0;
            if ($form_post = $this->_coreRegistry->registry('form_post')) {
                $title  = $form_post->getUploadedFile();
                $disposition  = $form_post->getContentDisp();
            }
            $html = $this->_helper->getFilesHtml($url, $title, $id, false, $disposition);
        }
        $this->setClass('fileuploader-input-file');
        $html.= parent::getElementHtml();
        $html.= $this->_getDeleteCheckbox();

        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="' . parent::getName() . '[delete]" value="1" class="checkbox" id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"' : '') . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . __('Delete File') . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }
    
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }
}
