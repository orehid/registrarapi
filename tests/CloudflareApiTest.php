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
    use orehid\registrarapi\CloudflareApi;

class CloudflareApiTest extends TestCase
{
    public function test_filterDomain()
    {
        $this->assertEquals(CloudflareApi::filterDomain("notexanple.com"), "notexanple.com");
    }
}
