<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bigdropinc\cloudstorage;

use yii\base\InvalidConfigException;
use OpenCloud\Rackspace;

/**
 * Class RackspaceCloudFiles
 * @package bigdropinc\cloudstorage
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class RackspaceCloudFiles extends BaseCloudStorage
{
    public $username;
    public $apiKey;
    public $containerName;
    /**
     * @var string The region (DFW, IAD, ORD, LON, SYD)
     */
    public $region;
    public $identityEndpoint = Rackspace::US_IDENTITY_ENDPOINT;

    /**
     * @var Rackspace
     */
    protected $client;

    protected $container;

    /**
     * @inheritdoc
     */
    public function init()
    {
        foreach (['username', 'apiKey', 'containerName', 'region'] as $attribute) {
            if ($this->$attribute === null) {
                throw new InvalidConfigException(strtr('"{class}::{attribute}" cannot be empty.', [
                    '{class}' => static::className(),
                    '{attribute}' => '$' . $attribute
                ]));
            }
        }
        parent::init();
    }

    public function getContainer()
    {
        if (null === $this->container) {
            $this->container = $this->getClient()
                ->objectStoreService(null, $this->region)
                ->getContainer($this->containerName);
            if (!$this->container) {
                $this->container = $this->getClient()
                    ->objectStoreService(null, $this->region)
                    ->createContainer($this->containerName);
                $this->container->enableCdn();
            }
        }
        return $this->container;
    }

    /**
     * Returns a Rackspace instance
     * @return Rackspace
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Rackspace($this->identityEndpoint, array(
                'username' => $this->username,
                'apiKey'   => $this->apiKey,
            ));
        }
        return $this->client;
    }

    /**
     * Saves a file to cloud storage
     * @param string $file the file uploaded.
     * The [[UploadedFile::$tempName]] will be used as the source file.
     */
    public function upload($file)
    {
        $name = $this->getName($file);
        $handle = fopen(\Yii::getAlias($file), 'r');
        $this->getContainer()->uploadObject($name, $handle);
    }

    /**
     * Downloads file(s) to the local filesystem
     *
     * @param string $prefix Only download objects that use this key prefix
     * @param string $dir Directory to download to
     */
    public function download($prefix = '', $dir = null)
    {
        $object = @$this->getContainer()->getObject($prefix);
        if ($object) {
            $stream = $object->getContent();
            $stream->rewind();
            if (null === $dir) {
                $dir = $this->localeStorageBasePath;
            }
            $dir = \Yii::getAlias($dir);
            $dir = rtrim($dir, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $prefix);
            file_put_contents($dir, $stream->getStream());
        }
    }

    /**
     * Removes a file
     * @param string|array $file the path of the file(s) to remove
     * @return int number of deleted files
     */
    public function delete($file)
    {
        if (!is_array($file)) {
            $file = [$file];
        }
        foreach ($file as $item) {
            $object = @$this->getContainer()->getObject($this->getName($item));
            if ($object) {
                $object->delete();
            }
        }
        return count($file);
    }

    /**
     * Returns the public url of the file or empty string if the file does not exists.
     * @param string $file the path of the file to access
     * @return string
     */
    public function getPublicUrl($file)
    {
        $name = $this->getName($file);
        if (null !== $this->cloudStorageBaseUrl) {
            return rtrim($this->cloudStorageBaseUrl, '/') . '/' . ltrim($name, '/');
        }
        $object = @$this->getContainer()->getObject($name);
        if ($object) {
            return strval($object->getPublicUrl());
        }
        return '';
    }
}
