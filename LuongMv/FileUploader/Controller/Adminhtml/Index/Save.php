<?php
namespace LuongMv\FileUploader\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    protected $_fileUploaderFactory;

    /**
     * @var \LuongMv\FileUploader\Helper\Data
     */
    protected $_uploadHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param \LuongMv\FileUploader\Helper\Data $uploadHelper
     */
    public function __construct(
        Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \LuongMv\FileUploader\Helper\Data $uploadHelper
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_uploadHelper = $uploadHelper;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $filedata = array();
        if (!empty($_FILES['uploaded_file']['name'])) {
            try {
                $ext = $this->_uploadHelper->getFileExtension($_FILES['uploaded_file']['name']);
                $fname = 'File-' . time() . $ext;
                $pathurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'custom/upload/';
                $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                $mediapath = $this->_mediaBaseDirectory = rtrim($mediaDir, '/');

                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'uploaded_file']);
                $uploader->setAllowRenameFiles(true);
                $path = $mediapath . '/custom/upload/';
                $result = $uploader->save($path, $fname);
                $filedata['uploaded_file'] = 'custom/upload/' . $fname;
                $currenttime = date('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['fileuploader_id' => $this->getRequest()->getParam('fileuploader_id'), '_current' => true]);
            }
        }

        if ($data = $this->getRequest()->getPostValue()) {
            $products = array();
            $availProductIds = $this->_uploadHelper->getAllAvailProductIds();
            if (isset($data['products'])) {
                $products = explode('&', $data['products']);
            }
            foreach ($products as $k => $v) {
                if (preg_match('/[^0-9]+/', $k) || preg_match('/[^0-9]+/', $v)) {
                    unset($products[$k]);
                }
            }
            $productIds = array_intersect($availProductIds, $products);
            $data['product_ids'] = implode(',', $productIds);
            
            $model = $this->_objectManager->create('LuongMv\FileUploader\Model\Fileuploader');

            $id = $this->getRequest()->getParam('fileuploader_id');
            if ($id) {
                $model->load($id);
            }

            if (!empty($filedata['uploaded_file'])) {
                $data['uploaded_file'] = $filedata['uploaded_file'];
            } else {
                if (isset($data['uploaded_file']['delete']) && $data['uploaded_file']['delete'] == 1) {
                    if ($data['uploaded_file']['value'] != '') {
                        $this->removeFile($data['uploaded_file']['value']);
                    }
                    $data['uploaded_file'] = '';
                } else {
                    unset($data['uploaded_file']);
                }
            }

            $model->setData('title', $data['title']);
            if (isset($data['uploaded_file'])) {
                $model->setData('uploaded_file', $data['uploaded_file']);
            }
            if (isset($data['products'])) {
                $model->setData('product_ids', $data['product_ids']);
            }
            $model->setData('file_status', $data['file_status']);
            $model->setData('content_disp', $data['content_disp']);
            $model->setData('file_content', $data['file_content']);
            $model->setData('sort_order', $data['sort_order']);
            $model->setData('update_time', date('Y-m-d H:i:s'));
            //$model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Item detail has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['fileuploader_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the entry.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['fileuploader_id' => $this->getRequest()->getParam('fileuploader_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function removeFile($file)
    {
        $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $path = $mediaDir . $file;
        $result = \Magento\Framework\Filesystem\Io\File::rmdirRecursive($path, true);
    }
}
