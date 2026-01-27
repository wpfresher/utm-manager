=== UTM Manager – UTM Tracking, Lead Attribution & Campaign Analytics ===
Contributors: urldev, kawsarahmedr
Tags: utm, analytics, insights, utm tracker, leads
Tested up to: 6.9
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track UTM parameters, capture leads with full attribution, and analyze marketing campaigns directly from your WordPress dashboard.

== Description ==

**UTM Manager** is a powerful and lightweight WordPress plugin designed to help marketers, business owners, and agencies track UTM parameters, capture leads with complete attribution data, and analyze marketing campaign performance—all without leaving your WordPress dashboard.

Whether you're running Google Ads, Facebook campaigns, email marketing, or affiliate promotions, UTM Manager ensures every visitor is tracked, recorded, and linked to the correct traffic source. Stop guessing which campaigns work and start making data-driven decisions.

= How It Works =

When visitors arrive at your website through URLs containing UTM parameters (like `?utm_source=google&utm_medium=cpc&utm_campaign=summer_sale`), UTM Manager automatically captures this data and creates a detailed lead record. Each lead stores the visitor's IP address along with all tracked UTM parameters, giving you complete visibility into your traffic sources.

= Example UTM URL =
`https://your-domain.com/?utm_id=12345&utm_source=google&utm_medium=advertising&utm_campaign=black-friday-sale&utm_term=campaign-term&utm_content=campaign-content`

== ✨ Key Features ==

= 🎯 Automatic UTM Parameter Tracking =
Capture all standard UTM parameters automatically from incoming URLs:
* **utm_id** – Unique identifier for tracking
* **utm_source** – Traffic source (google, facebook, newsletter)
* **utm_medium** – Marketing medium (cpc, email, social)
* **utm_campaign** – Campaign name (black_friday, summer_sale)
* **utm_term** – Paid search keywords
* **utm_content** – Differentiate ads or links

= 📊 Comprehensive Lead Management =
* View all captured leads in a clean, sortable table
* Search leads by IP address, UTM parameters, or date
* View detailed lead information with full UTM attribution
* Bulk delete leads with one click
* Customizable leads per page display

= 📥 CSV Export Tool (New in v1.3.0) =
* Export unlimited leads to CSV format
* Filter exports by custom date range
* Select specific fields to export
* Download leads for external analysis or backup
* Process large datasets without timeouts

= ⚙️ Flexible Configuration =
* Enable or disable individual UTM parameters
* Choose exactly which parameters to track
* Configure tracking to match your marketing strategy
* Simple, intuitive settings interface

= 🔒 Privacy & Security First =
* GDPR-compliant tracking approach
* All data stored locally in your WordPress database
* No external data transmission
* Proper input sanitization and output escaping
* Nonce verification on all forms and actions
* Capability checks for administrative functions

== 🚀 Benefits of Using UTM Manager ==

* **📈 Track Campaign Performance** – Know exactly which campaigns drive traffic and conversions
* **🎯 Understand Lead Sources** – See where your leads come from with complete attribution
* **💰 Optimize Marketing ROI** – Focus budget on channels that deliver results
* **⏱️ Save Time** – Automatic tracking eliminates manual data entry
* **📋 Export & Analyze** – Download lead data for advanced analysis in spreadsheets
* **🏠 Keep Data In-House** – No third-party dashboards or external services required
* **🔧 Developer Friendly** – Clean code with filters and hooks for customization

== 🎯 Perfect For ==

* **Digital Marketers** tracking paid advertising campaigns
* **E-commerce Stores** measuring customer acquisition sources
* **SaaS Companies** analyzing trial signups and conversions
* **Affiliate Marketers** monitoring traffic from partners
* **Content Creators** understanding audience sources
* **Marketing Agencies** managing multiple client campaigns
* **Small Businesses** wanting simple, effective campaign tracking

== Why Choose UTM Manager? ==

Unlike complex analytics platforms, UTM Manager focuses on one thing and does it exceptionally well: capturing and organizing UTM parameter data from your incoming traffic. There's no learning curve, no external accounts to create, and no monthly fees. Everything works directly within your WordPress admin area.

Your marketing data stays on your server, under your control. Whether you need a quick overview of recent leads or want to export months of data for detailed analysis, UTM Manager delivers the insights you need without the complexity.

== Installation ==

= Automatic Installation =
1. Go to your WordPress Admin Dashboard
2. Navigate to **Plugins > Add New**
3. Search for "UTM Manager"
4. Click **Install Now** and then **Activate**

= Manual Installation =
1. Download the plugin ZIP file from WordPress.org
2. Go to **Plugins > Add New > Upload Plugin**
3. Choose the downloaded file and click **Install Now**
4. Activate the plugin after installation

= FTP Installation =
1. Download and extract the plugin ZIP file
2. Upload the `utm-manager` folder to `/wp-content/plugins/`
3. Go to **Plugins** in your WordPress admin and activate UTM Manager

= After Activation =
1. Navigate to **UTM Manager > Settings** in your WordPress admin
2. Enable the UTM parameters you want to track
3. Save your settings and start collecting lead data
4. View captured leads under **UTM Manager > Leads**

== Frequently Asked Questions ==

= Which UTM parameters does the plugin track? =
UTM Manager tracks all standard UTM parameters: utm_id, utm_source, utm_medium, utm_campaign, utm_term, and utm_content. You can enable or disable each parameter individually from the settings page.

= How do I view my captured leads? =
Navigate to **UTM Manager > Leads** in your WordPress admin dashboard. You'll see a table displaying all captured leads with their IP addresses, UTM data, and capture dates. Click on any lead to view its complete details.

= Can I export my lead data? =
Yes! Version 1.3.0 introduced a powerful CSV export feature. Go to **UTM Manager > Tools**, select your date range and fields, then click Export. The tool handles unlimited leads without timeout issues.

= Is UTM Manager GDPR compliant? =
UTM Manager is designed with privacy in mind. It stores data locally on your WordPress database with no external transmission. You control all data and can delete leads at any time. We recommend adding appropriate disclosures to your privacy policy about UTM tracking.

= Does it work with any website or theme? =
Yes, UTM Manager works with any WordPress theme and doesn't modify your frontend appearance. It operates entirely in the background, capturing UTM data from URLs when visitors arrive.

= Will it slow down my website? =
No. UTM Manager is lightweight and only runs when visitors arrive with UTM parameters. It has minimal impact on page load times and server resources.

= Can I search through my leads? =
Absolutely! The leads table includes a search function that lets you find leads by IP address or any captured UTM parameter value.

= Does it track returning visitors? =
When a visitor returns with the same IP address but different UTM parameters, the lead record is updated with the new UTM data. This ensures you always have the most recent attribution information.

= What happens if a visitor has no UTM parameters? =
No lead is created. UTM Manager only captures visitors who arrive through URLs containing at least one tracked UTM parameter.

= Can I customize which fields appear in exports? =
Yes, the export tool lets you select exactly which fields to include: IP Address, UTM ID, UTM Source, UTM Medium, UTM Campaign, UTM Term, UTM Content, and Date.

== Screenshots ==

1. **Leads Dashboard** – View all captured leads with UTM attribution data in a sortable, searchable table
2. **Lead Details** – Detailed view of individual lead with complete UTM parameter information
3. **Settings Page** – Configure which UTM parameters to track with simple toggle controls
4. **Export Tool** – Export leads to CSV with date range filtering and field selection

== Changelog ==

= 1.3.0 (27 January 2026) =
* New: Added CSV export tool for unlimited lead exports
* New: Date range filtering for lead exports
* New: Selectable export fields for customized reports
* New: Tools submenu page for data management
* Fix: Resolved known issues with lead data handling
* Enhance: Improved overall performance and stability
* Enhance: Better memory handling for large datasets

= 1.2.6 (22 January 2026) =
* New: Search leads by any column (IP, UTM parameters, date)
* Fix: Minor bug fixes and performance improvements
* Compatibility: Tested with WordPress 6.9

= 1.2.5 (17 October 2025) =
* Fix: Minor bugs and performance optimizations

= 1.2.4 (15 July 2025) =
* Fix: Resolved known issues
* Security: Enhanced security measures for data protection

= 1.2.3 (12 July 2025) =
* Update: Plugin name updated to match official slug
* Compatibility: WordPress latest version support

= 1.2.2 (01 June 2025) =
* Fix: Resolved known issues

= 1.2.1 (01 June 2025) =
* Compatibility: WordPress latest version support

= 1.2.0 (23 February 2025) =
* New: Updated plugin framework architecture
* Enhance: Optimized autoloader for better performance
* Compatibility: WordPress latest version support

= 1.1.0 (29 July 2024) =
* Compatibility: WordPress latest version support
* Fix: Plugin banner image display issue on WordPress.org
* Enhance: Added plugin banner and icon assets

= 1.0.1 (25 July 2024) =
* Fix: Minor known issues
* Remove: Unused assets

= 1.0.0 (25 July 2024) =
* Initial release

== Upgrade Notice ==

= 1.3.0 =
Major update! New CSV export feature allows unlimited lead exports with date filtering and field selection. Recommended for all users.

= 1.2.6 =
Search functionality added. Now search leads by any column including IP address and UTM parameters.

= 1.2.0 =
Framework update with improved performance. Please backup before updating.

== System Requirements ==

* WordPress 5.0 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher / MariaDB 10.0 or higher

== Support ==

Need help? Visit our [support page](https://urldev.com/plugins/utm-manager/) for documentation and assistance.
