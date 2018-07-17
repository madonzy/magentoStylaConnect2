# Styla Connect 2 (v 2.0.6)
---

Requires: 
* PHP >= 5.4
* Magento 2, for our Magento 1 plugin check this https://github.com/styladev/magentoStylaConnect2
* Magento REST API activated to share product information http://devdocs.magento.com/guides/v2.0/rest/bk-rest.html

Styla Connect is a module to connect your Magento 2 Store with [Styla](http://www.styla.com/). The first diagram on [this page](https://styladocs.atlassian.net/wiki/spaces/CO/pages/9961481/Technical+Integration) should provide you an overview of what the plugin does and how it exchanges data with Styla. 

* [Installation](doc/installation.md)
* [Configuration](doc/configuration.md)
* [Customization](doc/customization.md)
* [Event List](doc/events.md)

## Setup Process

The process of setting up your Content Hub(s) usually goes as follows:

1. Install and configure the plugin on your stage using Content Hub ID(s) shared by Styla
2. Share the stage URL, credentials with Styla
4. Styla integrates product data from Magento REST API, test your stage Content Hub and asks additional questions, if needed
5. Install and configure the plugin on production, without linking to the Content Hub(s) there and, again, share the URL with Styla
6. Make sure your content is ready to go live
7. Styla conducts final User Acceptance Tests before the go-live
8. Go-live (you link to the Content Hub embedded on your production)
