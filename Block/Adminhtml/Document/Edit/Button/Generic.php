<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Block\Adminhtml\Document\Edit\Button;

use Exception;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Anhvdk\DocumentManagement\Api\DocumentRepositoryInterface;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Psr\Log\LoggerInterface;

/**
 * Generic Button Class
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Context
     *
     * @var Context
     */
    protected Context $context;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Document Repository
     *
     * @var DocumentRepositoryInterface
     */
    protected DocumentRepositoryInterface $documentRepository;

    /**
     * Authorization Interface
     *
     * @var AuthorizationInterface
     */
    protected AuthorizationInterface $authorization;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param DocumentRepositoryInterface $documentRepository
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        DocumentRepositoryInterface $documentRepository,
        AuthorizationInterface $authorization
    ) {
        $this->context = $context;
        $this->logger = $logger;
        $this->documentRepository = $documentRepository;
        $this->authorization = $authorization;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl(string $route = '', array $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get current context
     *
     * @return false|DocumentInterface
     */
    protected function getCurrentDocument()
    {
        try {
            $documentId = $this->context->getRequestParam('id', false);
            if ($documentId) {
                $documentModel = $this->documentRepository->get((int) $documentId);
                if ($documentModel->getEntityId()) {
                    return $documentModel;
                }
            }
        } catch (Exception $exception) {
            $this->logger->error(
                __(
                    'Can not get current document, %1',
                    $exception->getMessage()
                )
            );
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        return [];
    }
}
