<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Adminhtml\Document;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;

/**
 * Edit Page Controller
 */
class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Anhvdk_DocumentManagement::edit_document_management';

    /**
     * Page Page
     *
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * Document Repository
     *
     * @var DocumentRepositoryInterface
     */
    protected DocumentRepositoryInterface $documentRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param DocumentRepositoryInterface $documentRepository
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        DocumentRepositoryInterface $documentRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->documentRepository = $documentRepository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Anhvdk_DocumentManagement::document_management');

        $id = $this->getRequest()->getParam('id', false);
        if ($id) {
            try {
                $document = $this->documentRepository->get((int) $id);
                $resultPage->getConfig()->getTitle()->prepend(
                    $document->getEntityId() ? $document->getName() : __('New Document')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This document no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $resultPage->addBreadcrumb(
            $id ? __('Edit Document %1', $id) : __('New Document'),
            $id ? __('Edit Document %1', $id) : __('New Document')
        );

        return $resultPage;
    }
}
