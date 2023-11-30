<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model\ResourceModel\Document;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AreaManagement;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;
use Anhvdk\DocumentManagement\Api\Data\DocumentInterface;
use Anhvdk\DocumentManagement\Model\Document as DocumentModel;
use Anhvdk\DocumentManagement\Model\ResourceModel\Document as DocumentResource;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Document Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Area Management
     *
     * @var AreaManagement
     */
    private AreaManagement $areaManagement;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * @var array
     */
    private $fullTextSpecialChars = ['$', '@', '*', '<', '>', '(', ')', '-', '+', '~', '"'];

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AreaManagement $areaManagement
     * @param TimezoneInterface $timezone
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AreaManagement $areaManagement,
        TimezoneInterface $timezone,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->areaManagement = $areaManagement;
        $this->timezone = $timezone;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * @inheritDoc
     */
    protected $_idFieldName = DocumentResource::MAIN_TABLE_ID_FIELD_NAME;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(DocumentModel::class, DocumentResource::class);
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setOrder('sort_order', 'ASC');
        try {
            if ($this->areaManagement->getAreaCode() == Area::AREA_FRONTEND) {
                $this->filterByWebsiteId();
                $this->filterPublishedDocument();
                $this->filterPublishedDateDocument();
            }
        } catch (\Exception $exception) {
            $this->_logger->error(__('Can\'t check state, %1', $exception->getMessage()));
        }

        return $this;
    }

    /**
     * Filter collection by current website
     *
     * @return $this
     */
    private function filterByWebsiteId()
    {
        try {
            $currentWebsiteId = $this->storeManager->getWebsite()->getId();
            if ($currentWebsiteId) {
                $this->addFieldToFilter('website_ids', [
                    'finset' => $currentWebsiteId
                ]);
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $this;
    }

    /**
     * Filter published document only
     *
     * @return $this
     */
    private function filterPublishedDocument()
    {
        $this->addFieldToFilter(
            DocumentInterface::STATUS,
            DocumentModel\Source\Status::PUBLISHED
        );

        return $this;
    }

    /**
     * Filter published date document
     *
     * @return $this
     */
    private function filterPublishedDateDocument()
    {
        $this->addFieldToFilter(
            DocumentInterface::PUBLISHED_DATE,
            [
                'lteq' => $this->getTodayTimeZone()
            ]
        );

        return $this;
    }

    /**
     * Get today timezone
     *
     * @return string
     */
    protected function getTodayTimezone()
    {
        return $this->timezone->date()->format('Y-m-d');
    }

    /**
     * Fulltext search document
     *
     * @param string $query
     *
     * @return Collection
     */
    public function search(string $query)
    {
        $this->getSelect()->where(
            $this->getMatchCondition($query),
            $this->getSearchQuery($query)
        );

        return $this;
    }

    /**
     * Get search query
     *
     * @param string $query
     *
     * @return string
     */
    private function getSearchQuery(string $query)
    {
        $query = trim(str_replace($this->fullTextSpecialChars, ' ', $query));
        if (strlen($query) >= 2) {
            $query .= '*';
        }

        return $query;
    }

    /**
     * Get match condition
     *
     * @param string $query
     *
     * @return string
     */
    private function getMatchCondition(string $query)
    {
        $query = trim(str_replace($this->fullTextSpecialChars, ' ', $query));
        $columns = $this->getFulltextIndexColumns();
        $matchMode = (strlen($query) >= 2) ? ' IN BOOLEAN MODE' : '';

        return 'MATCH(' . implode(',', $columns) . ") AGAINST(?$matchMode)";
    }

    /**
     * Get all full text columns
     *
     * @return array
     */
    private function getFulltextIndexColumns()
    {
        return [
            DocumentInterface::NAME,
            DocumentInterface::SHORT_DESCRIPTION,
            DocumentInterface::TAGGING
        ];
    }
}
