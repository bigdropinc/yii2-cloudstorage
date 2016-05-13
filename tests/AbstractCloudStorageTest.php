<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace tests;

use Yii;
use yii\helpers\FileHelper;

/**
 * Class AbstractCloudStorageTest
 * @package tests
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
abstract class AbstractCloudStorageTest extends TestCase
{
    protected $file;

    protected function setUp()
    {
        FileHelper::createDirectory(Yii::getAlias('@tests/runtime'));
        $this->file = Yii::getAlias('@tests/data/avatar.jpg');
        parent::setUp();
    }

    /**
     * @requires function curl_init
     * @requires function curl_exec
     * @requires function curl_close
     * @requires function curl_getinfo
     * @requires function curl_setopt
     */
    public function testUpload()
    {
        Yii::$app->get('cloudStorage')->upload($this->file);
        $publicUrl = Yii::$app->get('cloudStorage')->getPublicUrl($this->file);
        $ch = curl_init($publicUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $this->assertEquals(file_get_contents($this->file), $output);
        return $publicUrl;
    }
    
    abstract public function testDelete($publicUrl);
}
