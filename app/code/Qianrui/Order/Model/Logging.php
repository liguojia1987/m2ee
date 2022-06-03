<?php

namespace Qianrui\Order\Model;

use Magento\Logging\Model\Handler\Controllers;

class Logging extends Controllers
{
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Logging\Helper\Data $loggingData,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $actionAttribute,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Logging\Model\Event\ChangesFactory $eventChangesFactory,
        \Magento\Logging\Model\Handler\Controllers\ConfigSaveHandler $configSaveHandler
    ) {
        parent::__construct(
            $messageManager,
            $loggingData,
            $jsonHelper,
            $actionAttribute,
            $coreRegistry,
            $request,
            $response,
            $eventChangesFactory,
            $configSaveHandler
        );

        $this->getLogger()->info(__METHOD__);
    }

    /**
     * Generic Action handler
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @param \Magento\Logging\Model\Processor $processorModel
     * @return \Magento\Logging\Model\Event
     */
    public function postDispatchUpdateImei($config, $eventModel, $processorModel)
    {
        $this->getLogger()->info(__METHOD__);

        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getParam('item_id');

            $sourceName = "Magento\\Sales\\Model\\Order\\Item";
            $origData = ["imei"=>111];
            $resultData = ["imei"=>222];

            $processorModel->addEventChanges(
                $processorModel->createChanges($sourceName, $origData, $resultData)->setSourceId($id)
            );

            return $eventModel->setInfo($id);
        }

        return $eventModel->setInfo('-');
    }

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    protected function getLogger()
    {
        if ($this->logger == null) {
            $this->logger = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Psr\Log\LoggerInterface::class);
        }
        return $this->logger;
    }
}
