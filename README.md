<h1>CiviCRM Extension that uses DataBC Geographic Data and Services to GeoCode British Columbia (Canada) addresses</h1>

This extension adds gives administrators the option to set the primary Geocoding method to DataBC in the Administer -> System Settings -> Mapping & Geocoding. Administrators can set a backup Geocoding method on: /civicrm/admin/setting/databcgeocode for addresses outside of British Columbia.

To download the latest version of this module: https://github.com/KarinG/DataBC

This extension was sponsored by: <b>British Columbia New Democratic Party</b>

<h2>Warnings</h2>

If you have a lot of addresses outside of British Columbia - you may still run into Google Geocoding limits!

<h2>Requirements</h2>

CiviCRM 4.4 or 4.6 (other versions untested)

<h2>Installation</h2>

Install as any other regular CiviCRM extension:

1- Download this extension and unpack it in your 'extensions' directory. You may need to create it if it does not already exist, and configure the correct path in CiviCRM -> Administer -> System -> Directories.

2- Enable the extension from CiviCRM -> Administer -> System -> Extensions.
Usage

The DataBC is injected everywhere a the CiviCRM Geocoder is called: so Edit of an Address -> Save but also in the Administer -> System Settings -> Address Geocoder Job.

<h2>Support</h2>

Please post bug reports in the issue tracker of this project on github: https://github.com/KarinG/DataBC/issues

<h2>License</h2>

AGPL-3.0

<h2>Copyright of the data + reference to Terms & Conditions</h2>

Copyright © 2016, Province of British Columbia. Contains information licensed under the Open Government License – British Columbia. http://www2.gov.bc.ca/gov/content/governments/about-the-bc-government/databc/open-data/open-government-license-bc http://www2.gov.bc.ca/gov/content/governments/about-the-bc-government/databc/open-data/api-terms-of-use-for-ogl-information
