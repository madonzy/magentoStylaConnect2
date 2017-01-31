<?php
namespace Styla\Connect2\Controller\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Styla\Connect2\Model\Product\Info as ProductInfo;
use Magento\Framework\Controller\Result\JsonFactory;

class Info extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var ProductRepositoryInterface 
     */
    protected $productRepository;
    
    /**
     *
     * @var ProductInfo 
     */
    protected $productInfo;
    
    /**
     *
     * @var JsonFactory 
     */
    protected $resultJsonFactory;

    /**
     * Info constructor.
     * @param \Magento\Framework\App\Action\Context            $context
     * @param ProductRepositoryInterface                       $productRepository
     * @param \Styla\Connect2\Model\Product\Info               $productInfo
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ProductRepositoryInterface $productRepository,
        ProductInfo $productInfo,
        JsonFactory $resultJsonFactory
    )
    {
        $this->productRepository = $productRepository;
        $this->productInfo       = $productInfo;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $error = false;
        $result = null;

        try {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_initProduct();

            $this->productInfo->setProduct($product);

            /** @var array $result */
            $result = $this->productInfo->getInfo();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        //return the json...
        $jsonResult = $this->resultJsonFactory->create();
        if ($error !== false) {
            $jsonResult->setData(
                    [
                        'error'     => $error,
                        'saleable'  => false,
                    ]
                );
            //$jsonResult->setHttpResponseCode(WebapiException::HTTP_NOT_FOUND);
        } else {
            $jsonResult->setData($result);
        }

        return $jsonResult;
    }

    /**
     * Initialize product instance from request data
     * @return false|\Magento\Catalog\Model\Product
     * @throws \Exception
     */
    protected function _initProduct()
    {
        $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
        try {
            $product = false;

            if ($sku = $this->getRequest()->getParam('sku')) {
                $product = $this->productRepository->getBySku($sku, false, $storeId);
            } elseif ($id = (int)$this->getRequest()->getParam('product', $this->getRequest()->getParam('id', false))) {
                $product = $this->productRepository->getById($id, false, $storeId);
            }

            if (!$product->getId()) {
                throw new \Exception('Product not found.');
            }

            if (!$product->getIsSalable()) {
                throw new \Exception('Product is unavailable.');
            }

            return $product;
        } catch (NoSuchEntityException $e) {
            throw new \Exception('Product not found.');
        }
    }
}
