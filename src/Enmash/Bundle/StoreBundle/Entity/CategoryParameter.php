<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryParameter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CategoryParameter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="units", type="string", length=255, nullable=true)
     */
    private $units;

    public function __toString() {
        $result = $this->getName();
        if ($this->getUnits() !== null) {
            $result .= ' (' . $this->getUnits() . ')';
        }
        return $result;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CategoryParameter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set units
     *
     * @param string $units
     * @return CategoryParameter
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get units
     *
     * @return string 
     */
    public function getUnits()
    {
        return $this->units;
    }
}
