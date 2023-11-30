<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Controller\Adminhtml\Document;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Index Page Controller
 */
class Index extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Anhvdk_DocumentManagement::document_management';

    /**
     * Result Page
     *
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Document Listing'));
        return $resultPage;
    }
}
