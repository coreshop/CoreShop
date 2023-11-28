# Carrier Discovery

In CoreShop, carrier discovery for a given shippable item is facilitated through a specialized service. This service is
responsible for identifying the available carriers that can handle the shipping of a specific item.

## Service Implementation

The carrier discovery service implements the
interface `CoreShop\Bundle\ShippingBundle\Discover\ShippableCarriersDiscoveryInterface`. This interface is crucial for
determining suitable carriers based on the characteristics and requirements of the shippable item.

The service implementing this interface is registered with the service ID `coreshop.carrier.discovery`. It plays a
pivotal role in the shipping process, ensuring that each shippable item is matched with appropriate and available
shipping options.
