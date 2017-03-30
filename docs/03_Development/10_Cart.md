# CoreShop Cart

If you need some Extra Informations added with a Product in your Cart, you can do that by creating an ObjectBrick for your CartItem/OrderItem. This ObjectBrick needs to implement CoreShop\Bundle\LegacyBundle\Model\Objectbrick\Data\AbstractData.

To render some extra information in your cart/invoice, you need to implement the "renderCart" or "renderInvoice" method.

For example, you need some personal informations for a product added to your CartItem/OrderItem:

Hook into "cart.preAdd", "cart.postAdd"

## cart.preAdd
```php
    public function cartPreAdd($e)
    {
        $product = $e->getParam("product");
        $request = $e->getParam("request");
        
        if($product->getNeedsPersonalData() || $product->getNeedsDoublePersonalData())
        {
            return is_array($this->checkCartAddRequest($product, $request));
        }
        
        return true;
    }
```

## cart.postAdd
```php
    public function cartPostAdd($e)
    {
        $product = $e->getParam("product");
        $cartItem = $e->getParam("cartItem");
        $request = $e->getParam("request");
        
        if($product->getNeedsPersonalData() || $product->getNeedsDoublePersonalData())
        {
            $data = $this->checkCartAddRequest($product, $request);
            
            if($product->getNeedsPersonalData())
            {
                $dataBrick = new Object\Objectbrick\Data\CoreShopCartItemPersonal($cartItem);
                $dataBrick->setValues($data);
                
                $cartItem->getExtraInformation()->setCoreShopCartItemPersonal($dataBrick);
                $cartItem->save();
            }
            else if($product->getNeedsDoublePersonalData())
            {
                $dataBrick = new Object\Objectbrick\Data\CoreShopCartItemPersonalDouble($cartItem);
                $dataBrick->setValues($data);
                
                $cartItem->getExtraInformation()->setCoreShopCartItemPersonalDouble($dataBrick);
                $cartItem->save();
            }
        }
        
        return true;
    }

    protected function checkCartAddRequest($product, $request)
    {
        $personalData = array(
            "firstname" => $request->getParam("firstname1"),
            "lastname" => $request->getParam("lastname1"),
            "birthdate" => new \Zend_Date($request->getParam("birthdate1")),
            "birthtime" => $request->getParam("birthtime1"),
            "birthzip" => $request->getParam("birthzip1"),
            "birthcity" => $request->getParam("birthcity1"),
            "birthcountry" => $request->getParam("birthcountry1"),
            "firstname2" => $request->getParam("firstname2"),
            "lastname2" => $request->getParam("lastname2"),
            "birthdate2" => new \Zend_Date($request->getParam("birthdate2")),
            "birthtime2" => $request->getParam("birthtime2"),
            "birthzip2" => $request->getParam("birthzip2"),
            "birthcity2" => $request->getParam("birthcity2"),
            "birthcountry2" => $request->getParam("birthcountry2")
        );

        if($product->getNeedsPersonalData())
        {
            foreach($personalData as $key=>$value)
            {
                if(!endsWith($key, "2")) {
                    if(!isset($value) || empty($value)) {
                        return false;
                    }
                }
            }
        }
        
        if($product->getNeedsDoublePersonalData())
        {
            foreach($personalData as $value)
            {
                if(!isset($value) || empty($value))
                    return false;
            }
        }
        
        return $personalData;
    }
```

## CoreShop\Bundle\LegacyBundle\Model\Objectbrick\Data\AbstractData

```php
class CoreShopCartItemPersonal extends AbstractData
{
    public function renderCart()
    {
        return $this->getView()->render('personal-data.php');
    }

    public function renderInvoice()
    {
        return $this->getView()->render('personal-data.php');
    }
}
```

## View: personal-data.php

## List all Carts

```php
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <span class="feature-label">
            <?=$this->translate("Vorname");?>:
        </span>
    </div>
    <div class="col-xs-12  col-sm-6">
        <span class="feature-text">
            <?=$this->brick->getFirstname();?>
        </span>
    </div>
</div>
```

# Cart Manager

CoreShop supports the use of multiple Carts per User.

The \CoreShop\Bundle\LegacyBundle\Model\Cart\Manager helps to manage all the carts.

## List all Carts
```php
\CoreShop::getTools()->getCartManager()->getCarts(\CoreShop::getTools()->getUser());
```

## Activate a specific Cart

To activate a specific cart, use following code.

```php
\CoreShop::getTools()->getCartManager()->setSessionCart($cart);
```

**Be aware**: The active Cart is used for checkout!

## Delete a specific Cart

```php
\CoreShop::getTools()->getCartManager()->deleteCart($cart);
```

## Create a new Cart

```php
\CoreShop::getTools()->getCartManager()->setSessionCart(\CoreShop::getTools()->getCartManager()->createCart("default", \CoreShop::getTools()->getUser(), \CoreShop\Bundle\LegacyBundle\Model\Shop::getShop(), true));
```

The last parameter is used to determine if the Cart should be persisted in the database, or in the session. On adding items, the cart will always be persisted in database.

