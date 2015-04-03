<?php
    
namespace CoreShop\Controller\Action;

use CoreShop\Controller\Action;

class Payment extends Action {
    
    protected function paymentReturnAction () {
        $this->prepareCart();
        $this->cart->delete();
        
        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }
}
