<?php
/**
 * Created by PhpStorm.
 * User: zrsh
 * Date: 14-7-19
 * Time: 下午5:35
 */
class IssueTest extends CDbTestCase
{
    public function testGetTypes()
    {
        $option = Issue::model()->typeOptions;
        $this->assertTrue(is_array($option));
        $this->assertTrue(3 == count($option));
        $this->assertTrue(in_array('Bug', $option));
        $this->assertTrue(in_array('Feature', $option));
        $this->assertTrue(in_array('Task', $option));
    }

    public function testStatus()
    {
        $status = Issue::model()->typeStatus;
        $this->assertTrue(is_array($status));
        $this->assertTrue(5 == count($status));
        $this->assertTrue(in_array('0%', $status));
        $this->assertTrue(in_array('25%', $status));
        $this->assertTrue(in_array('50%', $status));
        $this->assertTrue(in_array('75%', $status));
        $this->assertTrue(in_array('100%', $status));
    }
}