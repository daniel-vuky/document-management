<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Adminhtml\Document;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Anhvdk\DocumentManagement\Model\Uploader;

/**
 * Upload File Controller
 */
class Upload implements HttpPostActionInterface
{
    /**
     * Result Json
     *
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * Uploader
     *
     * @var Uploader
     */
    protected Uploader $uploader;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Uploader $uploader
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Uploader $uploader
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->uploader = $uploader;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->uploader->upload();
        $response = $this->resultJsonFactory->create();
        $response->setData($result);
        return $response;
    }
}
