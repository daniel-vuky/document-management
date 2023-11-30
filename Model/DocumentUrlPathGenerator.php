<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model;

use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;

/**
 * Document Url Path Generator
 */
class DocumentUrlPathGenerator
{
    /**
     * @param DocumentInterface $document
     *
     * @return string
     */
    public function getUrlPath(DocumentInterface $document)
    {
        return $document->getUrlRewrite();
    }

    /**
     * Get canonical document url path
     *
     * @param DocumentInterface $document
     *
     * @return string
     */
    public function getCanonicalUrlPath(DocumentInterface $document)
    {
        $entityId = $document->getEntityId();
        return 'document/document/download/id/' . $entityId;
    }
}
