<?php
/**
 * @link https://github.com/bigdropinc/yii2-cloudstorage
 * @copyright Copyright (c) 2012 Big Drop Inc
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace tests;

use Yii;

/**
 * Class RackspaceCloudFilesTest
 * @package tests
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
class RackspaceCloudFilesTest extends TestCase
{
    public function testAppName()
    {
        $this->assertEquals(Yii::$app->name, 'yii2-cloudstorage');
    }
}
