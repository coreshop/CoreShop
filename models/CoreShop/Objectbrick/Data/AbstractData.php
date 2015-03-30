<?

namespace CoreShop\Objectbrick\Data;

class AbstractData extends \Pimcore\Model\Object\Objectbrick\Data\AbstractData
{
    /**
    *  Zend_View
    */
    protected $view;
    
    public function getView()
    {
        if(!$this->view)
        {
            $this->view = new \Zend_View();
            $this->view->brick = $this;
            
            $this->view->setScriptPath(
                array(
                    PIMCORE_PLUGINS_PATH . '/CoreShop/views/scripts',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/',
                    PIMCORE_WEBSITE_PATH . '/views/layouts/',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/cart',
                )
            );
        }
        
        return $this->view;
    }
    
    public function render()
    {
        throw new Exception("not implemented");
    }
}