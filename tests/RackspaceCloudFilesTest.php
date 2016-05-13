<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace tests;

use bigdropinc\cloudstorage\RackspaceCloudFiles;
use Yii;

/**
 * Class RackspaceCloudFilesTest
 * @package tests
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class RackspaceCloudFilesTest extends AbstractCloudStorageTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication([
            'components' => [
                'cloudStorage' => [
                    'class' => RackspaceCloudFiles::className(),
                    'username' => $GLOBALS['RCF_USERNAME'],
                    'apiKey' => $GLOBALS['RCF_API_KEY'],
                    'region' => $GLOBALS['RCF_REGION'],
                    'containerName' => $GLOBALS['RCF_CONTAINER'],
                    'localeStorageBasePath' => '@tests',
                ],
            ],
        ]);
    }
    
    public function testUpload()
    {
        return parent::testUpload();
    }


    /**
     * @requires function curl_init
     * @requires function curl_exec
     * @requires function curl_close
     * @requires function curl_getinfo
     * @depends testUpload
     * @param $publicUrl
     */
    public function testDelete($publicUrl)
    {
        Yii::$app->get('cloudStorage')->delete($this->file);
        $ch = curl_init($publicUrl);
        curl_exec($ch);
        $this->assertEquals(404, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
    }
}
