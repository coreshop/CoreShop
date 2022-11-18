<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Model\WishlistItemInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20221118072919 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $listing = $this->container->get('coreshop.repository.wishlist_item')->getList();
        $batchListing = new DataObjectBatchListing($listing, 100);

        foreach ($batchListing as $wishlistItem) {
            if (!$wishlistItem instanceof WishlistItemInterface) {
                continue;
            }

            $wishlist = $this->findWishlist($wishlistItem);

            if ($wishlist instanceof WishlistInterface) {
                $wishlistItem->setWishlist($wishlist);
                $wishlistItem->save();
            }
        }
    }

    private function findWishlist(WishlistItemInterface $wishlistItem): ?WishlistInterface
    {
        $parent = $wishlistItem->getParent();
        do {
            if ($parent instanceof WishlistInterface) {
                return $parent;
            }
            $parent = $parent->getParent();
        } while ($parent !== null);

        return null;
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
