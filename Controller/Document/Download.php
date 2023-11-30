<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Document;

use Anhvdk\DocumentManagement\Model\Document\Source\Status;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;
use Anhvdk\DocumentManagement\Model\Document\FileInfo;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Download
 */
class Download implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * FileFactory
     *
     * @var FileFactory
     */
    private FileFactory $fileFactory;

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
     * File Info
     *
     * @var FileInfo
     */
    private FileInfo $fileInfo;

    /**
     * Document Repository
     *
     * @var DocumentRepositoryInterface
     */
    private DocumentRepositoryInterface $documentRepository;

    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $timezoneInterface;

    /**
     * Download constructor.
     * @param FileFactory $fileFactory
     * @param RequestInterface $request
     * @param ResultFactory $resultFactory
     * @param MessageManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param FileInfo $fileInfo
     * @param DocumentRepositoryInterface $documentRepository
     */
    public function __construct(
        FileFactory $fileFactory,
        RequestInterface $request,
        ResultFactory $resultFactory,
        MessageManagerInterface $messageManager,
        RedirectInterface $redirect,
        FileInfo $fileInfo,
        DocumentRepositoryInterface $documentRepository,
        TimezoneInterface $timezoneInterface
    ) {
        $this->fileFactory = $fileFactory;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->fileInfo = $fileInfo;
        $this->documentRepository = $documentRepository;
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $documentId = $this->request->getParam('id', false);
            if ($documentId) {
                $document = $this->documentRepository->get((int) $documentId);
                if ($document->getStatus() == Status::DRAFT ||
                    $document->getPublishedDate() > $this->getTodayTimezone()
                ) {
                    throw new LocalizedException(__('Document is not found'));
                }
                if ($document->getId()) {
                    $filepath = $this->fileInfo->getDocumentPath($document->getFileName());
                    $content['type'] = 'filename';
                    $content['value'] = $filepath;
                    $content['rm'] = 0;
                    return $this->fileFactory->create(
                        $document->getFileName(),
                        $content,
                        DirectoryList::MEDIA,
                        'application/pdf'
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return  $result->setUrl($this->redirect->getRefererUrl());
    }

    /**
     * @return string
     */
    protected function getTodayTimezone()
    {
        return $this->timezoneInterface->date()->format('Y-m-d');
    }
}
