<?php

namespace SimpleApi\Helper;

use Symfony\Component\Translation\Translator;
use SimpleApi\Helper\NotificationError;

class NotificationErrorTest extends \PHPUnit_Framework_TestCase
{

    protected $notificationError;

    protected function setUp()
    {
        $this->notificationError = new NotificationError;
    }

    protected function tearDown()
    {
        $this->notificationError = null;
    }

    
    public function testReset()
    {
        
        $translator = $this->getMockTranslator($this->never());
        
        $this->notificationError->addErro('teste', 'valor');
        $this->notificationError->setCodigoErro(401);
        
        $this->notificationError->reset();
                
        $this->assertFalse($this->notificationError->hasErrors());
        $this->assertEquals(0,$this->notificationError->getCodigoErro());
        $this->assertEmpty($this->notificationError->getErrors($translator));
                
    }

   
    public function testSetGetCodigoErro()
    {
        
        $code = 401;
        
        $this->notificationError->setCodigoErro($code);
        $this->assertEquals($code,$this->notificationError->getCodigoErro());
        
        $code  = 302;
        
        $this->notificationError->setCodigoErro($code);
        $this->assertEquals($code,$this->notificationError->getCodigoErro());
        
        
    }

    public function testAddandGetErro()
    {
        $translator = $this->getMockTranslator($this->atLeastOnce());
        
        $this->notificationError->addErro('teste', 'valor');
        $this->notificationError->addErro('teste_param', 'valor_param', ['p1' => 'param1']);
        $this->notificationError->addErro('teste_param2', 'valor_param2', ['p1' => 'param21', 'p2' => 'param22']);
        
        $this->assertTrue($this->notificationError->hasErrors());
        
        $errors = $this->notificationError->getErrors($translator);
       
        $this->assertArrayHasKey('teste',$errors);
        $this->assertArraySubset(['valor',[]],$errors['teste']);
        
        $this->assertArrayHasKey('teste_param',$errors);
        $this->assertArraySubset(['valor_param',['p1' => 'param1']],$errors['teste_param']);
        
        $this->assertArrayHasKey('teste_param2',$errors);
        $this->assertArraySubset(['valor_param2',['p1' => 'param21','p2' => 'param22']],$errors['teste_param2']);
        
        
    }

    public function testHasErrors()
    {
        
        $this->assertFalse($this->notificationError->hasErrors());
        $this->notificationError->addErro('teste', 'valor');
        $this->assertTrue($this->notificationError->hasErrors());
        
    }

    public function getMockTranslator($invoke)
    {
        $translator = $this->getMockBuilder(Translator::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        
        $translator->expects($invoke)
                   ->method("trans")
                   ->will($this->returnCallback(function(){
                       return func_get_args();
                   }));
        
        return $translator;
    }

    
}
