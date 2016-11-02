<?php

namespace LuongMv\FileUploader\Helper;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use LuongMv\FileUploader\Model\Resource\Fileuploader\CollectionFactory;
use LuongMv\FileUploader\Model\Resource\Fileuploader\Collection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var DirectoryList
     */
    protected $_dir;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductCollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
       \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_filesystem = $filesystem;
        $this->_dir = $directoryList;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * get products tab Url in admin
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('fileuploader/get/products', ['_current' => true]);
    }


    public function getDownloadFileUrl($url, $disposition)
    {
        return $this->_getUrl('fileuploader/download/download', array('_query' => array('d' => $disposition, 'file' => $url)));
    }
    
    public function getFileExtension($filename, $pos = 0)
    {
        return strtolower(substr($filename, strrpos($filename, '.') + $pos));
    }

    public function getAllAvailProductIds()
    {
        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productIds = $productCollection->getAllIds();
        return $productIds;
    }

    public function getFilesHtml($url, $title, $id=null, $showTitle=false, $disposition=0, $size=true)
    {
        $html = '';
        $fileSize = '';
        $mediaUrl = $pathurl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $ext = $this->getFileExtension($url, 1); //"jpg","jpeg","gif","png","txt","csv","htm","html","xml","css","doc","docx","xls","rtf","ppt","pdf","swf","flv","avi","wmv","mov","wav","mp3","zip"
        $mediaIcon = $mediaUrl . '/custom/upload/icons/' . $ext . '.png';
        $playerPath = $mediaUrl . '/custom/upload/player/';
        $mediaIconImage = '<span class="attach-img"><img src="' . $mediaIcon . '" alt="View File" style="margin-right: 5px;"/></span>' . (($showTitle) ? '<span class="attach-title">' . $title . '</span>' : '');
        $wh = ($showTitle) ? '16' : '22';
        if ($size) {
            $file = $url;
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $filePath = $mediaDir.$file;
            if (file_exists($filePath)) {
                $fileSize = '&nbsp; &nbsp;Size: ('.$this->getFileSize($filePath).')';
            }
        }
        if ($disposition) {
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' || $ext == 'bmp') {
                $image = $mediaUrl . $url;
                $onclick = "return fileUploaderPopup.open({url:this.rel, title: '" . str_replace(' ', '_', $title) . "'});";
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" title="' . $title . '" rel="' . $url . '" href="javascript:;" onclick="' . $onclick . '"><span class="attach-img"><img src="' . $image . '" id="' . $id . '_image" title="' . $title . '" alt="' . $title . '" height="' . $wh . '" width="' . $wh . '" class="small-image-preview v-middle" style="margin-right: 5px; width: ' . $wh . 'px; height: ' . $wh . 'px"/></span>' . (($showTitle) ? '<span class="attach-title">' . $title . '</span>' : '') . '</a>';
            } elseif ($ext == 'txt' || $ext == 'rtf' || $ext == 'csv' || $ext == 'css' || $ext == 'htm' || $ext == 'html' || $ext == 'xml' || $ext == 'doc' || $ext == 'docx' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'ppt' || $ext == 'pdf' || $ext == 'swf' || $ext == 'zip') {
                $url = $this->getDownloadFileUrl($url, $disposition);
                $onclick = "return fileUploaderPopup.open({url:this.rel, title: '" . str_replace(' ', '_', $title) . "'});";
                $html = '<a class="prod-attach" title="' . $title . '" rel="' . $url . '" href="javascript:;" onclick="' . $onclick . '">' . $mediaIconImage . '</a> ';
            } elseif ($ext == 'avi') {
                $url = $mediaUrl . $url;
                $onclick = "javascript:playerAviOpen('" . $title . "',this.rel); return false;";
                $onmouseover = "window.status=''; return true;";
                $html = '<a class="prod-attach" title="' . $title . '" onmouseover="' . $onmouseover . '" onclick="' . $onclick . '" target="_blank" rel="' . $url . '" href="javascript:;"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } elseif ($ext == 'flv') {
                $url = $mediaUrl . $url;
                $onclick = "javascript:playerFlvOpen('" . $title . "',this.rel,'" . $playerPath . "'); return false;";
                $onmouseover = "window.status=''; return true;";
                $html = '<a class="prod-attach" title="' . $title . '" onmouseover="' . $onmouseover . '" onclick="' . $onclick . '" target="_blank" rel="' . $url . '" href="javascript:;"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } elseif ($ext == 'mov') {
                $url = $mediaUrl . $url;
                $onclick = "javascript:playerMovOpen('" . $title . "',this.rel); return false;";
                $onmouseover = "window.status=''; return true;";
                $html = '<a class="prod-attach" title="' . $title . '" onmouseover="' . $onmouseover . '" onclick="' . $onclick . '" target="_blank" rel="' . $url . '" href="javascript:;"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } elseif ($ext == 'wmv' || $ext == 'wav' || $ext == 'mp3') {
                $url = $mediaUrl . $url;
                $onclick = "javascript:playerOpen('" . $title . "',this.rel); return false;";
                $onmouseover = "window.status=''; return true;";
                $html = '<a class="prod-attach" title="' . $title . '" onmouseover="' . $onmouseover . '" onclick="' . $onclick . '" target="_blank" rel="' . $url . '" href="javascript:;"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } else {
                $mediaIcon = $mediaUrl . '/custom/upload/icons/plain.png';
                $mediaIconImage = '<span class="attach-img"><img src="' . $mediaIcon . '" alt="View File" style="margin-right: 5px;"/></span>' . (($showTitle) ? '<span class="attach-title">' . $title . '</span>' : '');
                $url = $this->getDownloadFileUrl($url, $disposition);
                $onclick = "return fileUploaderPopup.open({url:this.rel, title: '" . str_replace(' ', '_', $title) . "'});";
                $html = '<a class="prod-attach" title="' . $title . '" rel="' . $url . '" href="javascript:;" onclick="' . $onclick . '">' . $mediaIconImage . '</a> ';
            }
        } else {
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' || $ext == 'bmp') {
                $image = $mediaUrl . $url;
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '" href="' . $url . '"><span class="attach-img"><img src="' . $image . '" id="' . $id . '_image" title="' . $title . '" alt="' . $title . '" height="' . $wh . '" width="' . $wh . '" class="small-image-preview v-middle" style="margin-right: 5px; width: ' . $wh . 'px; height: ' . $wh . 'px"/></span>' . (($showTitle) ? '<span class="attach-title">' . $title . '</span>' : '') . '</a>';
            } elseif ($ext == 'txt' || $ext == 'rtf' || $ext == 'csv' || $ext == 'css' || $ext == 'htm' || $ext == 'html' || $ext == 'xml' || $ext == 'doc' || $ext == 'docx' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'ppt' || $ext == 'pdf' || $ext == 'swf' || $ext == 'flv' || $ext == 'zip') {
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '" href="' . $url . '">' . $mediaIconImage . '</a> ';
            } elseif ($ext == 'avi') {
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '" href="' . $url . '"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } elseif ($ext == 'mov') {
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '"href="' . $url . '"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } elseif ($ext == 'wmv' || $ext == 'wav' || $ext == 'mp3') {
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '" href="' . $url . '"><span style="font-weight:bold">' . $mediaIconImage . '</span></a>';
            } else {
                $mediaIcon =$mediaUrl . '/custom/upload/icons/plain.png';
                $mediaIconImage = '<span class="attach-img"><img src="' . $mediaIcon . '" alt="View File" style="margin-right: 5px;"/></span>' . (($showTitle) ? '<span class="attach-title">' . $title . '</span>' : '');
                $url = $this->getDownloadFileUrl($url, $disposition);
                $html = '<a class="prod-attach" target="_blank" title="' . $title . '" href="' . $url . '">' . $mediaIconImage . '</a> ';
            }
        }
        return $html.$fileSize;
    }

    protected function getFileSize($file)
    {
        $size = filesize($file);
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return('n/a');
        } else {
            return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
        }
    }

    public function getFilesByProductId($productId)
    {
        $data = array();
        $collection = $this->collectionFactory->create();
        $collection = $collection->addFieldToFilter('file_status', 1);
        $collection->getSelect()->order('sort_order');
        foreach ($collection as $item) {
            $ids = $item->getProductIds();
            $id_list = explode(',', $ids);
            if (in_array($productId, $id_list) && !empty($file = $item->getUploadedFile())) {
                $data['items'][] = $item->toArray([]);
            }
        }
        return $data;
    }
}
