<?php
/**
 * CoreShopTemplate
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShopTemplate;

use CoreShop\Theme as CoreShopTheme;

use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Document;
use Pimcore\Model\Object;

class Theme extends CoreShopTheme
{
    public function getName() {
        return "default";
    }

    public function installDemoData()
    {
        $config = $this->getConfig();
        $templatePath = $this->getTemplatePath();

        if(array_key_exists("demo", $config["installation"])) {
            $categories = $config['installation']['demo']['categories'];
            $products = $config['installation']['demo']['products'];

            if(file_exists($templatePath . "/" . $categories)) {
                $this->insertDemoCategories($templatePath . "/" . $categories);
            }

            if(file_exists($templatePath . "/" . $products)) {
                $this->insertDemoProducts($templatePath . "/" . $products);
            }
        }
    }

    protected function insertDemoProducts($csvFile)
    {
        if (!file_exists($csvFile)) {
            return false;
        }

        $row = 0;

        /**

        Array
        (
            [0] => id_shop_default
            [1] => id_product
            [3] => name
            [13] => all_categories_Name
            [14] => on_sale
            [16] => ean13
            [21] => wholesale_price
            [22] => price
            [23] => price_vat
            [24] => price_discounted
            [31] => width
            [32] => height
            [33] => depth
            [34] => weight
            [39] => active
            [40] => available_for_order
            [48] => is_virtual
            [53] => description
            [54] => description_short
            [56] => url_image
            [58] => meta_description
            [59] => meta_keywords
            [60] => meta_title
        )
         */

        if (($handle = fopen($csvFile, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $row++;

                if ($row === 1) {
                    continue;
                }

                if(!$data[39]) {
                    continue;
                }

                $product = new Object\CoreShopProduct\Listing();
                $product->setCondition("name = ?", array($data[3]));
                $product = $product->getObjects();

                if(count($product) > 0) {
                    $product = $product[0];
                }

                if(!$product instanceof Object\CoreShopProduct) {
                    $categoriesString = explode(",", $data[13]);
                    $categories = array();

                    foreach($categoriesString as $cat) {
                        $category = new Object\CoreShopCategory\Listing();
                        $category->setCondition("name = ?", array($cat));
                        $category = $category->getObjects();

                        if(count($category) > 0) {
                            $categories[] = $category[0];
                        }
                    }

                    if(count($categories) > 0) {
                        $path = str_replace("categories", "products", $categories[0]->getFullPath());

                        $parent = Object\Service::createFolderByPath($path);

                        $product = new Object\CoreShopProduct();
                        $product->setParent($parent);
                        $product->setKey(File::getValidFilename($data[3]));
                        $product->setName($data[3], "de");
                        $product->setCategories($categories);
                        $product->setEan($data[16]);
                        $product->setWholesalePrice($data[21]);
                        $product->setWeight($data[34]);
                        $product->setWidth($data[31]);
                        $product->setHeight($data[32]);
                        $product->setDepth($data[33]);
                        $product->setAvailableForOrder($data[40]);
                        $product->setEnabled($data[39]);
                        $product->setTax(0.2);
                        $product->setRetailPrice($data[22] - ($data[22] * 0.2));
                        $product->setPrice($data[22]);
                        $product->setIsDownloadProduct($data[48]);
                        $product->setShortDescription($data[54]);
                        $product->setDescription($data[53]);
                        $product->setMetaDescription($data[58]);
                        $product->setMetaTitle($data[60]);
                        $product->setPublished(true);

                        $imageId = str_replace("-large.jpg", "", basename($data[56]));

                        $url = "http://shop.kaiba.at/".$imageId."-large_default/image.jpg";
                        $imageUrl = parse_url($url);

                        if($imageUrl)
                        {
                            try {
                                $imageData = @file_get_contents($url);

                                if($imageData) {
                                    $imageType = pathinfo($imageUrl['path'], PATHINFO_EXTENSION);

                                    $assetParent = Asset\Service::createFolderByPath($path);
                                    $key = File::getValidFilename($data[3]) . "." . $imageType;
                                    $fullPath = $assetParent->getFullPath() . "/" . $key;

                                    if($existingAsset = Asset::getByPath($fullPath)) {
                                        $existingAsset->delete();
                                    }

                                    $image = new Image();
                                    $image->setParent($assetParent);
                                    $image->setFilename($key);
                                    $image->setData($imageData);
                                    $image->save();

                                    $product->setImages(array($image));
                                }
                            }
                            catch(\Exception $ex) {

                            }
                        }

                        $product->save();
                    }
                }
            }
        }
    }

    protected function insertDemoCategories($csvFile) {
        if(!file_exists($csvFile)) {
            return false;
        }

        $row = 0;

        $idMapping = array();

        if (($handle = fopen($csvFile, "r")) !== false)
        {
            while (($data = fgetcsv($handle, 1000, ",")) !== false)
            {
                $row++;

                if($row === 1) {
                    continue;
                }

                /*
                 * id
                 * parent
                 * name
                 *
                 * */

                $category = new Object\CoreShopCategory\Listing();
                $category->setCondition("name = ?", array($data[2]));
                $category = $category->getObjects();

                if(count($category) > 0) {
                    $category = $category[0];
                }

                if(!$category instanceof Object\CoreShopCategory) {

                    $parent = null;

                    //Check if Category has Parent
                    if($data[1])
                    {
                        $parent = Object\CoreShopCategory::getById($idMapping[$data[1]]);

                        //We can't import the Category if the parent does not exist
                        if(!$parent) {
                            continue;
                        }
                    }

                    $category = new Object\CoreShopCategory();
                    $category->setName($data[2]);

                    if($parent instanceof Object\CoreShopCategory) {
                        $category->setParentCategory($parent);
                        $category->setParent($parent);
                    }
                    else {
                        //TODO: Read from Configuration or should be definied as Constants?
                        $category->setParent(Object\Folder::getByPath("/coreshop/categories"));
                    }

                    $category->setPublished(true);
                    $category->setKey(File::getValidFilename($data[2]));
                    $category->save();

                    $idMapping[$data[0]] = $category->getId();
                }
            }
            fclose($handle);
        }

        return true;
    }
}
