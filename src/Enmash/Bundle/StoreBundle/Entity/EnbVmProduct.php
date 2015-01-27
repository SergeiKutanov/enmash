<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnbVmProduct
 *
 * @ORM\Table(name="enb_vm_product", indexes={@ORM\Index(name="idx_product_vendor_id", columns={"vendor_id"}), @ORM\Index(name="idx_product_product_parent_id", columns={"product_parent_id"}), @ORM\Index(name="idx_product_sku", columns={"product_sku"}), @ORM\Index(name="idx_product_ship_code_id", columns={"ship_code_id"}), @ORM\Index(name="idx_product_name", columns={"product_name"})})
 * @ORM\Entity(repositoryClass="Enmash\Bundle\StoreBundle\Entity\EnbVmProductRepository")
 */
class EnbVmProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productId;

    /**
     * @var integer
     *
     * @ORM\Column(name="vendor_id", type="integer", nullable=false)
     */
    private $vendorId = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_parent_id", type="integer", nullable=false)
     */
    private $productParentId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="product_sku", type="string", length=64, nullable=false)
     */
    private $productSku = '';

    /**
     * @var string
     *
     * @ORM\Column(name="product_s_desc", type="string", length=255, nullable=true)
     */
    private $productSDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="product_a_desc", type="text", length=65535, nullable=true)
     */
    private $productADesc;

    /**
     * @var string
     *
     * @ORM\Column(name="product_desc", type="text", length=65535, nullable=true)
     */
    private $productDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="product_thumb_image", type="string", length=255, nullable=true)
     */
    private $productThumbImage;

    /**
     * @var string
     *
     * @ORM\Column(name="product_small_image", type="string", length=64, nullable=true)
     */
    private $productSmallImage;

    /**
     * @var string
     *
     * @ORM\Column(name="product_full_image", type="string", length=255, nullable=true)
     */
    private $productFullImage;

    /**
     * @var string
     *
     * @ORM\Column(name="product_publish", type="string", length=1, nullable=true)
     */
    private $productPublish;

    /**
     * @var string
     *
     * @ORM\Column(name="product_weight", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $productWeight;

    /**
     * @var string
     *
     * @ORM\Column(name="product_weight_uom", type="string", length=32, nullable=true)
     */
    private $productWeightUom = 'pounds.';

    /**
     * @var string
     *
     * @ORM\Column(name="product_length", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $productLength;

    /**
     * @var string
     *
     * @ORM\Column(name="product_width", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $productWidth;

    /**
     * @var string
     *
     * @ORM\Column(name="product_height", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $productHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="product_lwh_uom", type="string", length=32, nullable=true)
     */
    private $productLwhUom = 'inches';

    /**
     * @var string
     *
     * @ORM\Column(name="product_url", type="string", length=255, nullable=true)
     */
    private $productUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_in_stock", type="integer", nullable=false)
     */
    private $productInStock = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_available_date", type="integer", nullable=true)
     */
    private $productAvailableDate;

    /**
     * @var string
     *
     * @ORM\Column(name="product_availability", type="string", length=56, nullable=false)
     */
    private $productAvailability = '';

    /**
     * @var string
     *
     * @ORM\Column(name="product_special", type="string", length=1, nullable=true)
     */
    private $productSpecial;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_discount_id", type="integer", nullable=true)
     */
    private $productDiscountId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ship_code_id", type="integer", nullable=true)
     */
    private $shipCodeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="cdate", type="integer", nullable=true)
     */
    private $cdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="mdate", type="integer", nullable=true)
     */
    private $mdate;

    /**
     * @var string
     *
     * @ORM\Column(name="product_nic", type="string", length=64, nullable=true)
     */
    private $productNic;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=64, nullable=true)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="product_s_name", type="string", length=128, nullable=true)
     */
    private $productSName;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_sales", type="integer", nullable=false)
     */
    private $productSales = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="attribute", type="text", length=65535, nullable=true)
     */
    private $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_attribute", type="text", length=65535, nullable=false)
     */
    private $customAttribute;

    /**
     * @var boolean
     *
     * @ORM\Column(name="product_tax_id", type="boolean", nullable=false)
     */
    private $productTaxId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="product_unit", type="string", length=32, nullable=true)
     */
    private $productUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_packaging", type="integer", nullable=true)
     */
    private $productPackaging;

    /**
     * @var string
     *
     * @ORM\Column(name="child_options", type="string", length=45, nullable=true)
     */
    private $childOptions;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity_options", type="string", length=45, nullable=true)
     */
    private $quantityOptions;

    /**
     * @var string
     *
     * @ORM\Column(name="child_option_ids", type="string", length=45, nullable=true)
     */
    private $childOptionIds;

    /**
     * @var string
     *
     * @ORM\Column(name="product_order_levels", type="string", length=45, nullable=true)
     */
    private $productOrderLevels;



    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set vendorId
     *
     * @param integer $vendorId
     * @return EnbVmProduct
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    /**
     * Get vendorId
     *
     * @return integer 
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * Set productParentId
     *
     * @param integer $productParentId
     * @return EnbVmProduct
     */
    public function setProductParentId($productParentId)
    {
        $this->productParentId = $productParentId;

        return $this;
    }

    /**
     * Get productParentId
     *
     * @return integer 
     */
    public function getProductParentId()
    {
        return $this->productParentId;
    }

    /**
     * Set productSku
     *
     * @param string $productSku
     * @return EnbVmProduct
     */
    public function setProductSku($productSku)
    {
        $this->productSku = $productSku;

        return $this;
    }

    /**
     * Get productSku
     *
     * @return string 
     */
    public function getProductSku()
    {
        return $this->productSku;
    }

    /**
     * Set productSDesc
     *
     * @param string $productSDesc
     * @return EnbVmProduct
     */
    public function setProductSDesc($productSDesc)
    {
        $this->productSDesc = $productSDesc;

        return $this;
    }

    /**
     * Get productSDesc
     *
     * @return string 
     */
    public function getProductSDesc()
    {
        return $this->productSDesc;
    }

    /**
     * Set productADesc
     *
     * @param string $productADesc
     * @return EnbVmProduct
     */
    public function setProductADesc($productADesc)
    {
        $this->productADesc = $productADesc;

        return $this;
    }

    /**
     * Get productADesc
     *
     * @return string 
     */
    public function getProductADesc()
    {
        return $this->productADesc;
    }

    /**
     * Set productDesc
     *
     * @param string $productDesc
     * @return EnbVmProduct
     */
    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    /**
     * Get productDesc
     *
     * @return string 
     */
    public function getProductDesc()
    {
        return $this->productDesc;
    }

    /**
     * Set productThumbImage
     *
     * @param string $productThumbImage
     * @return EnbVmProduct
     */
    public function setProductThumbImage($productThumbImage)
    {
        $this->productThumbImage = $productThumbImage;

        return $this;
    }

    /**
     * Get productThumbImage
     *
     * @return string 
     */
    public function getProductThumbImage()
    {
        return $this->productThumbImage;
    }

    /**
     * Set productSmallImage
     *
     * @param string $productSmallImage
     * @return EnbVmProduct
     */
    public function setProductSmallImage($productSmallImage)
    {
        $this->productSmallImage = $productSmallImage;

        return $this;
    }

    /**
     * Get productSmallImage
     *
     * @return string 
     */
    public function getProductSmallImage()
    {
        return $this->productSmallImage;
    }

    /**
     * Set productFullImage
     *
     * @param string $productFullImage
     * @return EnbVmProduct
     */
    public function setProductFullImage($productFullImage)
    {
        $this->productFullImage = $productFullImage;

        return $this;
    }

    /**
     * Get productFullImage
     *
     * @return string 
     */
    public function getProductFullImage()
    {
        return $this->productFullImage;
    }

    /**
     * Set productPublish
     *
     * @param string $productPublish
     * @return EnbVmProduct
     */
    public function setProductPublish($productPublish)
    {
        $this->productPublish = $productPublish;

        return $this;
    }

    /**
     * Get productPublish
     *
     * @return string 
     */
    public function getProductPublish()
    {
        return $this->productPublish;
    }

    /**
     * Set productWeight
     *
     * @param string $productWeight
     * @return EnbVmProduct
     */
    public function setProductWeight($productWeight)
    {
        $this->productWeight = $productWeight;

        return $this;
    }

    /**
     * Get productWeight
     *
     * @return string 
     */
    public function getProductWeight()
    {
        return $this->productWeight;
    }

    /**
     * Set productWeightUom
     *
     * @param string $productWeightUom
     * @return EnbVmProduct
     */
    public function setProductWeightUom($productWeightUom)
    {
        $this->productWeightUom = $productWeightUom;

        return $this;
    }

    /**
     * Get productWeightUom
     *
     * @return string 
     */
    public function getProductWeightUom()
    {
        return $this->productWeightUom;
    }

    /**
     * Set productLength
     *
     * @param string $productLength
     * @return EnbVmProduct
     */
    public function setProductLength($productLength)
    {
        $this->productLength = $productLength;

        return $this;
    }

    /**
     * Get productLength
     *
     * @return string 
     */
    public function getProductLength()
    {
        return $this->productLength;
    }

    /**
     * Set productWidth
     *
     * @param string $productWidth
     * @return EnbVmProduct
     */
    public function setProductWidth($productWidth)
    {
        $this->productWidth = $productWidth;

        return $this;
    }

    /**
     * Get productWidth
     *
     * @return string 
     */
    public function getProductWidth()
    {
        return $this->productWidth;
    }

    /**
     * Set productHeight
     *
     * @param string $productHeight
     * @return EnbVmProduct
     */
    public function setProductHeight($productHeight)
    {
        $this->productHeight = $productHeight;

        return $this;
    }

    /**
     * Get productHeight
     *
     * @return string 
     */
    public function getProductHeight()
    {
        return $this->productHeight;
    }

    /**
     * Set productLwhUom
     *
     * @param string $productLwhUom
     * @return EnbVmProduct
     */
    public function setProductLwhUom($productLwhUom)
    {
        $this->productLwhUom = $productLwhUom;

        return $this;
    }

    /**
     * Get productLwhUom
     *
     * @return string 
     */
    public function getProductLwhUom()
    {
        return $this->productLwhUom;
    }

    /**
     * Set productUrl
     *
     * @param string $productUrl
     * @return EnbVmProduct
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;

        return $this;
    }

    /**
     * Get productUrl
     *
     * @return string 
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * Set productInStock
     *
     * @param integer $productInStock
     * @return EnbVmProduct
     */
    public function setProductInStock($productInStock)
    {
        $this->productInStock = $productInStock;

        return $this;
    }

    /**
     * Get productInStock
     *
     * @return integer 
     */
    public function getProductInStock()
    {
        return $this->productInStock;
    }

    /**
     * Set productAvailableDate
     *
     * @param integer $productAvailableDate
     * @return EnbVmProduct
     */
    public function setProductAvailableDate($productAvailableDate)
    {
        $this->productAvailableDate = $productAvailableDate;

        return $this;
    }

    /**
     * Get productAvailableDate
     *
     * @return integer 
     */
    public function getProductAvailableDate()
    {
        return $this->productAvailableDate;
    }

    /**
     * Set productAvailability
     *
     * @param string $productAvailability
     * @return EnbVmProduct
     */
    public function setProductAvailability($productAvailability)
    {
        $this->productAvailability = $productAvailability;

        return $this;
    }

    /**
     * Get productAvailability
     *
     * @return string 
     */
    public function getProductAvailability()
    {
        return $this->productAvailability;
    }

    /**
     * Set productSpecial
     *
     * @param string $productSpecial
     * @return EnbVmProduct
     */
    public function setProductSpecial($productSpecial)
    {
        $this->productSpecial = $productSpecial;

        return $this;
    }

    /**
     * Get productSpecial
     *
     * @return string 
     */
    public function getProductSpecial()
    {
        return $this->productSpecial;
    }

    /**
     * Set productDiscountId
     *
     * @param integer $productDiscountId
     * @return EnbVmProduct
     */
    public function setProductDiscountId($productDiscountId)
    {
        $this->productDiscountId = $productDiscountId;

        return $this;
    }

    /**
     * Get productDiscountId
     *
     * @return integer 
     */
    public function getProductDiscountId()
    {
        return $this->productDiscountId;
    }

    /**
     * Set shipCodeId
     *
     * @param integer $shipCodeId
     * @return EnbVmProduct
     */
    public function setShipCodeId($shipCodeId)
    {
        $this->shipCodeId = $shipCodeId;

        return $this;
    }

    /**
     * Get shipCodeId
     *
     * @return integer 
     */
    public function getShipCodeId()
    {
        return $this->shipCodeId;
    }

    /**
     * Set cdate
     *
     * @param integer $cdate
     * @return EnbVmProduct
     */
    public function setCdate($cdate)
    {
        $this->cdate = $cdate;

        return $this;
    }

    /**
     * Get cdate
     *
     * @return integer 
     */
    public function getCdate()
    {
        return $this->cdate;
    }

    /**
     * Set mdate
     *
     * @param integer $mdate
     * @return EnbVmProduct
     */
    public function setMdate($mdate)
    {
        $this->mdate = $mdate;

        return $this;
    }

    /**
     * Get mdate
     *
     * @return integer 
     */
    public function getMdate()
    {
        return $this->mdate;
    }

    /**
     * Set productNic
     *
     * @param string $productNic
     * @return EnbVmProduct
     */
    public function setProductNic($productNic)
    {
        $this->productNic = $productNic;

        return $this;
    }

    /**
     * Get productNic
     *
     * @return string 
     */
    public function getProductNic()
    {
        return $this->productNic;
    }

    /**
     * Set productName
     *
     * @param string $productName
     * @return EnbVmProduct
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productSName
     *
     * @param string $productSName
     * @return EnbVmProduct
     */
    public function setProductSName($productSName)
    {
        $this->productSName = $productSName;

        return $this;
    }

    /**
     * Get productSName
     *
     * @return string 
     */
    public function getProductSName()
    {
        return $this->productSName;
    }

    /**
     * Set productSales
     *
     * @param integer $productSales
     * @return EnbVmProduct
     */
    public function setProductSales($productSales)
    {
        $this->productSales = $productSales;

        return $this;
    }

    /**
     * Get productSales
     *
     * @return integer 
     */
    public function getProductSales()
    {
        return $this->productSales;
    }

    /**
     * Set attribute
     *
     * @param string $attribute
     * @return EnbVmProduct
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set customAttribute
     *
     * @param string $customAttribute
     * @return EnbVmProduct
     */
    public function setCustomAttribute($customAttribute)
    {
        $this->customAttribute = $customAttribute;

        return $this;
    }

    /**
     * Get customAttribute
     *
     * @return string 
     */
    public function getCustomAttribute()
    {
        return $this->customAttribute;
    }

    /**
     * Set productTaxId
     *
     * @param boolean $productTaxId
     * @return EnbVmProduct
     */
    public function setProductTaxId($productTaxId)
    {
        $this->productTaxId = $productTaxId;

        return $this;
    }

    /**
     * Get productTaxId
     *
     * @return boolean 
     */
    public function getProductTaxId()
    {
        return $this->productTaxId;
    }

    /**
     * Set productUnit
     *
     * @param string $productUnit
     * @return EnbVmProduct
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;

        return $this;
    }

    /**
     * Get productUnit
     *
     * @return string 
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * Set productPackaging
     *
     * @param integer $productPackaging
     * @return EnbVmProduct
     */
    public function setProductPackaging($productPackaging)
    {
        $this->productPackaging = $productPackaging;

        return $this;
    }

    /**
     * Get productPackaging
     *
     * @return integer 
     */
    public function getProductPackaging()
    {
        return $this->productPackaging;
    }

    /**
     * Set childOptions
     *
     * @param string $childOptions
     * @return EnbVmProduct
     */
    public function setChildOptions($childOptions)
    {
        $this->childOptions = $childOptions;

        return $this;
    }

    /**
     * Get childOptions
     *
     * @return string 
     */
    public function getChildOptions()
    {
        return $this->childOptions;
    }

    /**
     * Set quantityOptions
     *
     * @param string $quantityOptions
     * @return EnbVmProduct
     */
    public function setQuantityOptions($quantityOptions)
    {
        $this->quantityOptions = $quantityOptions;

        return $this;
    }

    /**
     * Get quantityOptions
     *
     * @return string 
     */
    public function getQuantityOptions()
    {
        return $this->quantityOptions;
    }

    /**
     * Set childOptionIds
     *
     * @param string $childOptionIds
     * @return EnbVmProduct
     */
    public function setChildOptionIds($childOptionIds)
    {
        $this->childOptionIds = $childOptionIds;

        return $this;
    }

    /**
     * Get childOptionIds
     *
     * @return string 
     */
    public function getChildOptionIds()
    {
        return $this->childOptionIds;
    }

    /**
     * Set productOrderLevels
     *
     * @param string $productOrderLevels
     * @return EnbVmProduct
     */
    public function setProductOrderLevels($productOrderLevels)
    {
        $this->productOrderLevels = $productOrderLevels;

        return $this;
    }

    /**
     * Get productOrderLevels
     *
     * @return string 
     */
    public function getProductOrderLevels()
    {
        return $this->productOrderLevels;
    }
}
