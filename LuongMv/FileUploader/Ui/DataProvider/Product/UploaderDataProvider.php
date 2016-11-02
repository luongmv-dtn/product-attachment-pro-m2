<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace LuongMv\FileUploader\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use LuongMv\FileUploader\Model\Resource\Fileuploader\CollectionFactory;
use LuongMv\FileUploader\Model\Resource\Fileuploader\Collection;
use LuongMv\FileUploader\Model\Fileuploader;

/**
 * Class ReviewDataProvider
 *
 * @method Collection getCollection
 */
class UploaderDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $id = $this->request->getParam('current_product_id', 0);

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        $id_list = array();
        $i = 0;
        foreach ($this->getCollection() as $item) {
            $ids = $item->getProductIds();
            $id_list = explode(',', $ids);
            if (in_array($id, $id_list)) {
                if ($item->getContentDisp() == 0) {
                    $item->setContentDisp('Attachment');
                } else {
                    $item->setContentDisp('Inline');
                }
                if ($item->getFileStatus() == 1) {
                    $item->setFileStatus('Enabled');
                } else {
                    $item->setFileStatus('Disabled');
                }
                $item->setProductId($id);
                $arrItems['items'][] = $item->toArray([]);
                $i++;
            }
        }
        $arrItems['totalRecords'] = $i;

        return $arrItems;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        parent::addFilter($filter);
    }
}
