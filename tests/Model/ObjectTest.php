<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Model;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectTest 
{
    private $name;
    
    public function getName():?string
    {
        return $this->name;
    }
    
    public function setName(string $name):self
    {
        $this->name = $name;
        
        return $this;
    }
}
