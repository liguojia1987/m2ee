<?php
namespace Qianrui\Order\Controller\Adminhtml\Imei;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    protected $logger;

    protected $collectionFactory;

    protected $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::update_imei');
    }

    public function execute()
    {
        /** @var Json $result */
        $result = $this->resultJsonFactory->create();

        /** @var RequestInterface $request */
        $request = $this->getRequest();
        $id = (int)$request->getParam('item_id');
        $imei = $request->getParam('imei', '');
        $imei = $imei ? array_unique(explode(',', $imei)) : [];

        try {
            $collection = $this->collectionFactory->create();
            $orderItem = $collection->addIdFilter($id)->getFirstItem();

            if($orderItem){
                $imei = !empty($imei) ? implode(',', $imei) : '';

                $orderItem->setData('imei', $imei);
                $orderItem->save();

                $comment = "change imei to [{$imei}].";
                /** @var \Magento\Sales\Model\Order $order */
                $order = $orderItem->getOrder();
                /** @var \Magento\Sales\Model\Order\Status\History $history */
                $history = $order->addCommentToStatusHistory($comment, $order->getStatus());
                $history->save();

                $result->setData([
                    'code' => 0,
                    'message' => __('Success.')
                ]);
            }
        } catch (LocalizedException $e) {
            $result->setData([
                'code' => 400,
                'message' => $e->getMessage()
            ]);
        }

        return $result;
    }
}
