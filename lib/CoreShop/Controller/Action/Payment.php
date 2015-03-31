<?php
    
namespace CoreShop\Controller\Action;

class Payment extends \CoreShop\Controller\Action {
    
    protected function paymentReturnAction () {
        $this->prepareCart();
        $this->cart->delete();
        
        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }
}
