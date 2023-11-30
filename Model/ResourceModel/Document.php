<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model\ResourceModel;

use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Anhvdk\DocumentManagement\Model\DocumentUrlRewriteGenerator;

/**
 * Document resource model
 */
class Document extends AbstractDb
{
    const MAIN_TABLE_NAME = 'documents_management';
    const MAIN_TABLE_ID_FIELD_NAME = DocumentInterface::ENTITY_ID;

    /**
     * @var UrlPersistInterface
     */
    protected UrlPersistInterface $urlPersist;

    /**
     * @var DocumentUrlRewriteGenerator
     */
    protected DocumentUrlRewriteGenerator $documentUrlRewriteGenerator;

    /**
     * @param Context $context
     * @param $connectionName
     */
    public function __construct(
        Context $context,
        UrlPersistInterface $urlPersist,
        DocumentUrlRewriteGenerator $documentUrlRewriteGenerator,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->urlPersist = $urlPersist;
        $this->documentUrlRewriteGenerator = $documentUrlRewriteGenerator;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    protected function _afterDelete(AbstractModel $object)
    {
        parent::_afterDelete($object);
        $this->urlPersist->deleteByData(
            [
                UrlRewrite::ENTITY_ID => $object->getEntityId(),
                UrlRewrite::ENTITY_TYPE => DocumentUrlRewriteGenerator::ENTITY_TYPE,
            ]
        );

        return $this;
    }

    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        if ($object->dataHasChangedFor(DocumentInterface::URL_REWRITE)
            || $object->dataHasChangedFor(DocumentInterface::WEBSITE_IDS)
            || $object->dataHasChangedFor(DocumentInterface::FILE_NAME)
        ) {
            $urls = $this->documentUrlRewriteGenerator->generate($object);

            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $object->getId(),
                UrlRewrite::ENTITY_TYPE => DocumentUrlRewriteGenerator::ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        }
        return $this;
    }
}
