<?php
    
class CoreShop_Cart extends CoreShop_Base {
    
    public static function getAll()
    {
        $list = new Object_CoreShopCart_List();
        
        return $list->getObjects();
    }
    
    public static function create()
    {
        $cartsFolder = Object_Folder::getByPath("/coreshop/carts");
        
        $cart = new Object_CoreShopCart();
        $cart->setKey(uniqid());
        $cart->setParent($cartsFolder);
        $cart->setPublished(true);
        $cart->save();
        
        return $cart;
    }
    
    public function addProduct(CoreShop_Product $product)
    {
        $products = $this->getProducts();
        
        if(!is_array($products))
            $products = array();
        
        $products[] = $product;

        $this->setProducts($products);
        $this->save(true);
    }
    
    public function removeProduct(CoreShop_Product $product)
    {
        $products = $this->getProducts();

        for($i = 0; $i < count($products); $i++)
        {
            if($products[$i]->getId() == $product->getId())
            {
                unset($products[$i]);
                break;
            }
        }
        
        $this->setProducts($products);
        $this->save();
    }
    
    public function toArray()
    {
        $products = array();
        $total = 0;
        
        foreach($this->getProducts() as $product)
        {
            $products[] = $product->toArray();
            
            $total += $product->getPrice();
        }
        
        return array(
            "user" => $this->user ? $this->user->toArray() : null,
            "products" => $products,
            "total" => $total,
            "totalFormatted" => CoreShop_Tool::formatPrice($total)
        );
    }
}