<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace tests;

use Yii;

/**
 * Class AmazonS3Test
 * @package tests
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class AmazonS3Test extends TestCase
{

    public function testAppId()
    {
        $this->assertEquals(Yii::$app->id, 'yii2-cloudstorage');
    }
}
