<?php
declare(strict_types=1);

namespace Anhvdk\DocumentManagement\Model\Document;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * File Information Class
 */
class FileInfo
{
    const ENTITY_MEDIA_PATH = '/Anhvdk/document-image';

    const ENTITY_MEDIA_FILE_PATH = '/Anhvdk/document-file';

    const ENTITY_DOCUMENT_PATH = 'tmp/document-file';

    const DEFAULT_ZIP_FILE_NAME = 'document_{time}.zip';

    /**
     * Filesystem
     *
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * Mime
     *
     * @var Mime
     */
    private Mime $mime;

    /**
     * WriteInterface
     *
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
        $this->storeManager = $storeManager;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    public function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    public function getMimeType($fileName, string $type = 'image')
    {
        $filePath = $this->getFilePath($fileName, $type);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws FileSystemException
     */
    public function getStat($fileName, string $type = 'image')
    {
        $filePath = $this->getFilePath($fileName, $type);

        return $this->getMediaDirectory()->stat($filePath);
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @param string $type
     *
     * @return bool
     * @throws FileSystemException
     */
    public function isExist(string $fileName, string $type)
    {
        $filePath = $this->getFilePath($fileName, $type);

        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * Construct and return file subpath based on filename relative to media directory
     *
     * @param string $fileName
     * @return string
     */
    public function getFilePath($fileName, string $type = 'image')
    {
        $filePath = $this->removeStorePath($fileName);
        $filePath = ltrim($filePath, '/');
        if ($type == 'file') {
            return self::ENTITY_MEDIA_FILE_PATH . '/' . $filePath;
        }
        return self::ENTITY_MEDIA_PATH . '/' . $filePath;
    }

    /**
     * Clean store path in case if it's exists
     *
     * @param string $path
     * @return string
     */
    private function removeStorePath(string $path): string
    {
        $result = $path;
        try {
            $storeUrl = $this->storeManager->getStore()->getBaseUrl();
        } catch (NoSuchEntityException $e) {
            return $result;
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $path = parse_url($path, PHP_URL_PATH);
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $storePath = parse_url($storeUrl, PHP_URL_PATH);
        $storePath = rtrim($storePath, '/');

        return preg_replace('/^' . preg_quote($storePath, '/') . '/', '', $path);
    }

    /**
     * Check document is existed
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function documentIsExisted(string $fileName)
    {
        $filePath = $this->getDocumentPath($fileName);

        return $this->getMediaDirectory()->isExist($filePath);
    }

    /**
     * Get document Path
     *
     * @param string $fileName
     * @param bool $useFullPath
     * @return string
     * @throws FileSystemException
     */
    public function getDocumentPath(string $fileName, $useFullPath = false)
    {
        $filePath = $this->removeStorePath($fileName);
        $filePath = ltrim($filePath, '/');
        return implode('/', [
            $useFullPath ? $this->getMediaDirectory()->getAbsolutePath() : '',
            self::ENTITY_MEDIA_FILE_PATH,
            $filePath
        ]);
    }

    /**
     * Get zip file path
     *
     * @return string
     * @throws FileSystemException
     */
    public function getZipFileFullPath()
    {
        $absolutePath = $this->getMediaDirectory()->getAbsolutePath() . self::ENTITY_DOCUMENT_PATH;
        return $absolutePath . '/' . $this->getZipFileName();
    }

    /**
     * Get zip file path
     *
     * @return string
     */
    public function getZipFileMiniPath()
    {
        return self::ENTITY_DOCUMENT_PATH . '/' . $this->getZipFileName();
    }

    /**
     * Get zip name
     *
     * @return string
     */
    public function getZipFileName()
    {
        return str_replace('{time}', (string) strtotime('now'), self::DEFAULT_ZIP_FILE_NAME);
    }
}
