<?php


class CoreShop_Controller_Action_Payment extends CoreShop_Controller_Action {
    
    protected function paymentReturnAction () {
        $this->prepareCart();
        $this->cart->delete();
        
        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }
}
