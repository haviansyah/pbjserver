<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RoleConst;

class UnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    function multiroles($roles){
        $jml = count($roles);
        $return = "";
        foreach($roles as $key=>$role){
            $return .= $role;
            if($key < $jml-1){
                $return .= "|";
            }
        }
        return $return;
    }
    public function testMultiRole()
    {
        $expected = "admin|rendal|gm";
        $actual = $this->multiroles(["admin","rendal","gm"]);
        $this->assertEquals($expected,$actual);
        $this->assertTrue(true);
    }
}
