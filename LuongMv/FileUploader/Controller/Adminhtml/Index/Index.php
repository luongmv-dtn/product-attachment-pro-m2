<?php
namespace LuongMv\FileUploader\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('LuongMv_FileUploader::fileuploader');
        $resultPage->addBreadcrumb(__('luong'), __('luong'));
        $resultPage->addBreadcrumb(__('Manage Attachments'), __('Manage Attachments'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Attachments'));
        return $resultPage;
    }
}
