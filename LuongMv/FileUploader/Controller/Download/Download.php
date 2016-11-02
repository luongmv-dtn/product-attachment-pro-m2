<?php

namespace LuongMv\FileUploader\Controller\Download;

use Magento\Framework\App\Action\Context;
use Magento\Downloadable\Helper\Download as DownloadHelper;

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $_helper;

    /**
     * Download constructor.
     * @param Context $context
     * @param \LuongMv\FileUploader\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        \LuongMv\FileUploader\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->_helper = $helperData;
    }

    protected function _processDownload($file, $disposition=0)
    {
        $resource = $this->_objectManager->get(\Magento\Downloadable\Helper\File::class)->getFilePath('', $file);
        $resourceType = DownloadHelper::LINK_TYPE_FILE;
        /* @var $helper \Magento\Downloadable\Helper\Download */
        $helper = $this->_objectManager->get(\Magento\Downloadable\Helper\Download::class);
        $helper->setResource($resource, $resourceType);
        
        $fileName = $helper->getFilename();
        $contentType = $helper->getContentType();

        $this->getResponse()->setHttpResponseCode(
            200
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $contentType,
            true
        );

        if ($fileSize = $helper->getFileSize()) {
            $this->getResponse()->setHeader('Content-Length', $fileSize);
        }

        $contentDisposition = (($disposition)?'inline':'attachment');
        if ($contentDisposition) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);
        }

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        $helper->output();
    }
    
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file', null);
        $disp = $this->getRequest()->getParam('d', null);
        if ($fileName) {
            try {
                $this->_processDownload($fileName, $disp);
                exit(0);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Sorry, there was an error getting requested content.'));
            }
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
