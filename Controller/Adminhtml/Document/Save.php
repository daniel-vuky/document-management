<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Adminhtml\Document;

use Exception;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterfaceFactory;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;
use Anhvdk\DocumentManagement\Model\Document\Source\Status;
use Anhvdk\DocumentManagement\Model\Uploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class save
 */
class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Anhvdk_DocumentManagement::edit_document_management';

    /**
     * Document Repository
     *
     * @var DocumentRepositoryInterface
     */
    protected DocumentRepositoryInterface $documentRepository;

    /**
     * Document Interface Factory
     *
     * @var DocumentInterfaceFactory
     */
    protected DocumentInterfaceFactory $documentFactory;

    /**
     * Timezone Interface
     *
     * @var TimezoneInterface
     */
    protected TimezoneInterface $timezone;

    /**
     * Image Uploader
     *
     * @var ImageUploader
     */
    protected ImageUploader $imageUploader;

    /**
     * Uploader
     *
     * @var Uploader
     */
    protected Uploader $fileUploader;

    /**
     * @param Context $context
     * @param DocumentRepositoryInterface $documentRepository
     * @param DocumentInterfaceFactory $documentFactory
     * @param TimezoneInterface $timezone
     * @param ImageUploader $imageUploader
     * @param Uploader $uploader
     */
    public function __construct(
        Action\Context $context,
        DocumentRepositoryInterface $documentRepository,
        DocumentInterfaceFactory $documentFactory,
        TimezoneInterface $timezone,
        ImageUploader $imageUploader,
        Uploader $uploader
    ) {
        $this->documentRepository = $documentRepository;
        $this->documentFactory = $documentFactory;
        $this->timezone = $timezone;
        $this->imageUploader = $imageUploader;
        $this->fileUploader = $uploader;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $dataPost = $this->getRequest()->getPostValue();
        try {
            if (!$dataPost) {
                return $this->getReturnPath(0, $resultRedirect);
            }
            $this->initPublishedDate($dataPost);
            $document = $this->documentFactory->create();
            $document->setData($dataPost);
            $this->documentRepository->save($document);
            $this->moveFileFromTmp($document, $dataPost);
            $this->messageManager->addSuccessMessage(__("The document has been saved successfully"));
            if ($this->getRequest()->getParam('stay', false)) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id' => $document->getEntityId(),
                        '_current' => true
                    ]
                );
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->getReturnPath(
                (int) ($dataPost[DocumentInterface::ENTITY_ID] ?? ''),
                $resultRedirect
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Get Return Path
     *
     * @param int $id
     * @param $resultRedirect
     * @return mixed
     */
    protected function getReturnPath(int $id, &$resultRedirect)
    {
        return $id
            ? $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true])
            : $resultRedirect->setPath('*/*/new');
    }

    /**
     * Init published date
     *
     * @param array $dataPost
     *
     * @return $this
     */
    protected function initPublishedDate(array &$dataPost)
    {
        $publishedDate = $dataPost[DocumentInterface::PUBLISHED_DATE] ?? false;
        $status = $dataPost[DocumentInterface::STATUS] ?? false;
        if (!$publishedDate && $status == Status::PUBLISHED) {
            $dataPost[DocumentInterface::PUBLISHED_DATE] = $this->getCurrenDateTime();
        }

        return $this;
    }

    /**
     * Get time when published
     *
     * @return string
     */
    protected function getCurrenDateTime()
    {
        return $this->timezone->date()->format('Y-m-d h:i:s');
    }

    /**
     * Move file from tmp to correct folder
     *
     * @param DocumentInterface $document
     * @param array $dataPost
     *
     * @return $this
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function moveFileFromTmp(DocumentInterface $document, array $dataPost)
    {
        $inputPreviewImage = $dataPost[DocumentInterface::PREVIEW_IMAGE][0]['old_file'] ?? false;
        $previewImage = $document->getPreviewImage();
        if ($previewImage && $previewImage != $inputPreviewImage) {
            $this->imageUploader->moveFileFromTmp($previewImage);
        }

        $inputFileName = $dataPost[DocumentInterface::FILE_NAME][0]['old_file'] ?? false;
        $fileName = $document->getFileName();
        if ($fileName && $fileName != $inputFileName) {
            $this->fileUploader->moveFileFromTmp($fileName);
        }

        return $this;
    }
}
