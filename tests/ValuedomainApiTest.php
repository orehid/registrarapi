<?php
/** 
 * CloudflareApi Test
 *
 * PHP version 7
 *
 * @category Library
 * @package  registrarapi
 * @author   orehid <orehid@example.com>
 * @license  MIT
 * @version  1.0
 */


    use PHPUnit\Framework\TestCase;
    use orehid\registrarapi\ValuedomainApi;

class ValuedomainApiTest extends TestCase
{
    /**
     * @test
     * @expectedException \Exception
     */
    public function test_filterDomainNg()
    {
        $this->expectException(\Exception::class);
        ValuedomainApi::filterDomain("example.com");
    }

    /**
     * 
     */
    public function test_filterDomainOk()
    {
        $this->assertEquals(ValuedomainApi::filterDomain("not.example.com"), "not.example.com");
    }
}
