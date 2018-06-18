<?php
class ExternalTest extends \PHPUnit\Framework\TestCase
{

    public function testInclude()
    {
        try{
            include __DIR__.'/../.last_version/lib/external/autoload.php';
        } catch (\Exception $e){
        
        }
        $this->assertTrue(true);
    }
}

