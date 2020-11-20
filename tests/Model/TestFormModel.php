<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Model;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class TestFormModel 
{
    private $object;

    private $created_at;
    
    function getCreatedAt() :?\DateTimeInterface
    {
        return $this->created_at;
    }

    function setCreatedAt(\DateTimeInterface $createdAt): self 
    {
        $this->created_at = $createdAt;
        
        return $this;
    }

    public function getObject(): ?ObjectTest
    {
        return $this->object;
    }

    public function setObject(?ObjectTest $object): self
    {
        $this->object = $object;
        
        return $this;
    }
}
