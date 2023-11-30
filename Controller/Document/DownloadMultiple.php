<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Document;

use Exception;
use Anhvdk\DocumentManagement\Service\DownloadDocumentService;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Download Multiple Document Controller
 */
class DownloadMultiple implements HttpGetActionInterface, HttpPostActionInterface
{
    const SEPARATOR = ',';
    const DOCUMENT_IDS = 'ids';

    /**
     * Request Interface
     *
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * Result Factory
     *
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * Message Manager
     *
     * @var MessageManagerInterface
     */
    protected MessageManagerInterface $messageManager;

    /**
     * Redirect Interface
     *
     * @var RedirectInterface
     */
    protected RedirectInterface $redirect;

    /**
     * Download Document Service
     *
     * @var DownloadDocumentService
     */
    protected DownloadDocumentService $multipleDownloadService;

    /**
     * DownloadMultiple constructor.
     *
     * @param RequestInterface $request
     * @param ResultFactory $resultFactory
     * @param MessageManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param DownloadDocumentService $multipleDownloadService
     */
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        MessageManagerInterface $messageManager,
        RedirectInterface $redirect,
        DownloadDocumentService $multipleDownloadService
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->multipleDownloadService = $multipleDownloadService;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $ids = $this->request->getParam(self::DOCUMENT_IDS, '');
            $ids = explode(',', $ids);
            if (!empty($ids)) {
                return $this->multipleDownloadService->downloadFileByDocumentIds($ids);
            }
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return  $result->setUrl($this->redirect->getRefererUrl());
    }
}
