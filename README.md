# Router for listing slugs in a commercetools project

This router loads a category matching the given request uri to categories slugs. It utilizes the cmf routing package heavily and registers the provided listing router as a chained cmf router through the service tag "_router_".

## Install it

    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:best-it/commercetools-listing-slug-router-bundle.git"
        }
    ]
    
**Then do:**

    composer require bestit/commercetools-listing-slug-router-bundle

## Configuration

```
#!yaml
best_it_ct_listing_slug_router:

    # Which controller-method should be used on a positive match?
    controller:           'BestIt\Frontend\ListingBundle\Controller\ListingController::indexAction'

    # Which priority has this router in the cmf chaining?
    priority:             0

    # Service id for the repositry loading categories with their slug. You should fulfill the provided interface.
    repository:           ~

    # Which route name is used for a positive match?
    route:                best_it_frontend_listing_listing_index
```

## Further ToDos

* The lib folder could be moved to a separate repo.
