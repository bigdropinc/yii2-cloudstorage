<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace bigdropinc\cloudstorage;

use Aws\S3\Enum\CannedAcl;
use Aws\S3\S3Client;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class AmazonS3
 * @package bigdropinc\cloudstorage
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class AmazonS3 extends BaseCloudStorage
{

    /**
     * @var string Amazon access key
     */
    public $key;
    /**
     * @var string Amazon secret access key
     */
    public $secret;
    /**
     * @var string Amazon Bucket
     */
    public $bucket;
    
    /**
     * @var \Aws\S3\S3Client
     */
    protected $client;

    /**
     * @inheritdoc
     */
    public function init()
    {
        foreach (['key', 'secret', 'bucket'] as $attribute) {
            if ($this->$attribute === null) {
                throw new InvalidConfigException(strtr('"{class}::{attribute}" cannot be empty.', [
                    '{class}' => static::className(),
                    '{attribute}' => '$' . $attribute
                ]));
            }
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function delete($file)
    {
        if (!is_array($file)) {
            $result = $this->getClient()->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getName($file)
            ]);
            return (int)$result['DeleteMarker'];
        }

        $result = $this->getClient()->deleteObjects([
            'Bucket' => $this->bucket,
            'Objects' => array_map(function ($item) {
                return array('Key' => $this->getName($item));
            }, $file),
        ]);

        return count($result['Deleted']);
    }

    /**
     * Downloads a bucket to the local filesystem
     *
     * @param string $dir Directory to download to
     * @param string $prefix Only download objects that use this key prefix
     * @param array $options Associative array of download options
     *     - params: Array of parameters to use with each GetObject operation performed during the transfer
     *     - base_dir: Base directory to remove from each object key when storing in the local filesystem
     *     - force: Set to true to download every file, even if the file is already on the local filesystem and has not
     *       changed
     *     - concurrency: Maximum number of parallel downloads (defaults to 10)
     *     - debug: Set to true or a fopen resource to enable debug mode to print information about each download
     *     - allow_resumable: Set to true to allow previously interrupted downloads to be resumed using a Range GET
     */
    public function download($prefix = '', $dir = null, array $options = [])
    {
        // remove extension part of the file. Because amazonS3 sdk does not support download by full name
        $prefix = current(explode('.', $prefix));
        if (null === $dir) {
            $dir = $this->localeStorageBasePath;
        }
        $dir = \Yii::getAlias($dir);
        $this->getClient()->downloadBucket($dir, $this->bucket, $prefix, $options);
    }
    

    /**
     * Saves a file to cloud storage
     * @param string $file the file uploaded.
     * The [[UploadedFile::$tempName]] will be used as the source file.
     * @param array $options extra options for the object to save on the bucket. For more information, please visit
     * [[http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_putObject]]
     * @return \Guzzle\Service\Resource\Model
     */
    public function upload($file, $options = [])
    {
        $options = ArrayHelper::merge([
            'Bucket' => $this->bucket,
            'Key' => $this->getName($file),
            'SourceFile' => \Yii::getAlias($file),
            'ACL' => CannedAcl::PUBLIC_READ // default to ACL public read
        ], $options);

        $this->getClient()->putObject($options);
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
        return $this->getClient()->getObjectUrl($this->bucket, $name);
    }

    /**
     * Returns a S3Client instance
     * @return \Aws\S3\S3Client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = S3Client::factory([
                'key' => $this->key,
                'secret' => $this->secret
            ]);
        }
        return $this->client;
    }
}
