# UTM Manager Review Report

Review date: 2026-04-25  
Reviewed version: 1.3.0  
Scope: PHP plugin bootstrap, lead capture, admin UI, CSV export, migration, build metadata, and readme positioning.

## Executive Summary

UTM Manager has a clear free-plugin foundation: it captures standard UTM query parameters, stores them as private lead records, lists them in wp-admin, and exports CSV files. The current implementation is small and understandable, which is a good base for enhancement.

Before adding major new features, fix the core data and export issues. The highest-risk areas are lead overwriting by IP address, CSV export query arguments, unsafe/fragile export file handling, direct IP storage without retention controls, and an unconditional `vendor/autoload.php` dependency that can fatal if the release package does not include `vendor`.

## Priority Findings

### 1. Critical: One lead per IP overwrites attribution history

Location: `includes/Leads.php:61-76`, `includes/functions.php`

The plugin uses the visitor IP as the lead title and updates the existing post when the same IP returns:

- `post_title` is the IP address.
- `utmm_get_post_by_title( $ip )` finds the previous lead.
- `wp_insert_post()` receives the previous lead ID and replaces `post_content`.

Impact:

- Multiple visitors behind the same NAT, office network, VPN, mobile carrier, or proxy overwrite each other.
- A returning visitor with a new campaign erases the previous campaign.
- Analytics totals become unreliable because records represent latest IP state, not visits or leads.

Recommended fix:

- Treat each UTM hit as an event or visit record by default.
- Add a separate visitor identity if deduplication is needed: cookie/session ID, user ID, order ID, form submission ID, or hashed IP plus user-agent.
- Preserve historical campaign records and show rollups separately.

### 2. High: CSV export query uses invalid WP query keys

Location: `includes/Controllers/Actions.php:127-134`

The export query uses `per_page` and `status`, but `get_posts()` expects `posts_per_page` and `post_status`.

Impact:

- Batch size may not be limited to 30 as intended.
- Status filtering may not behave as expected.
- Large exports can become slow or memory-heavy.

Recommended fix:

- Replace `per_page` with `posts_per_page`.
- Replace `status` with `post_status`.
- Add `orderby => ID`, `order => ASC`, and `no_found_rows => true` for predictable batching.

### 3. High: Export file path is user-influenced and stored in public uploads

Location: `includes/Controllers/Actions.php:77-123`, `includes/Controllers/Actions.php:276-294`

The CSV filename comes from POST/GET, is sanitized with `sanitize_text_field()`, and is appended into the uploads path. The generated CSV is temporarily stored in the public uploads directory.

Impact:

- A weak filename sanitizer is not ideal for filesystem paths.
- Export files containing IP addresses and campaign data may be web-accessible until download/deletion.
- Concurrent exports can collide if filenames are reused.
- Failed downloads leave personal data in uploads.

Recommended fix:

- Generate the filename server-side only.
- Use `sanitize_file_name()` and a plugin-specific private subdirectory with an `index.html` and deny rules where possible.
- Store an export token/transient mapping to the generated file.
- Add scheduled cleanup for stale export files.

### 4. High: Raw IP addresses are stored without privacy controls

Location: `includes/Leads.php:32-76`, admin lead views, CSV export

The plugin records IP addresses as lead titles and exports them. The readme claims a privacy-first approach, but the product currently lacks consent, retention, anonymization, deletion policy, and data export/erasure integrations.

Impact:

- IP addresses can be personal data depending on jurisdiction and context.
- Site owners need tools to configure retention and minimize stored data.
- The current readme overstates the privacy posture.

Recommended fix:

- Add configurable retention and automatic purge.
- Offer IP anonymization or hashing.
- Add an option to disable IP storage.
- Integrate with WordPress personal data exporter/eraser.
- Update the readme to describe actual controls instead of broad compliance claims.

### 5. High: Plugin can fatal if `vendor/autoload.php` is missing

Location: `utm-manager.php:37-38`

The main plugin file unconditionally requires `vendor/autoload.php`, but this working tree does not contain a `vendor` directory.

Impact:

- A source checkout or incorrectly built release will fatal on activation.
- WordPress.org packaging must guarantee Composer autoload files are included.

Recommended fix:

- Ensure release builds include `vendor/autoload.php`.
- Consider a guarded bootstrap with an admin notice when autoload is missing.
- Add a release validation step that fails when required runtime files are absent.

### 6. Medium: Nonce verification is misused in read-only/public contexts

Locations:

- `includes/Leads.php:87-89`
- `includes/Admin/Admin.php:165-167`
- `includes/Admin/ListTables/LeadsListTable.php:42-43`

`wp_verify_nonce( '_nonce' )` is called without reading a nonce value and without using the result. Public UTM capture cannot require a nonce because it is triggered by external campaign URLs.

Impact:

- Creates a false sense of protection.
- May trigger coding-standard warnings.
- Does not secure anything.

Recommended fix:

- Remove nonce calls from public GET tracking and read-only admin views.
- Keep `check_admin_referer()` for state-changing admin actions.

### 7. Medium: IP detection trusts spoofable headers too broadly

Location: `includes/Leads.php:103-124`

The plugin checks many `$_SERVER` headers and returns the first valid IP. Headers such as `HTTP_X_FORWARDED_FOR` can be client-controlled unless the site is behind a trusted proxy configuration.

Impact:

- Visitors can spoof lead identity.
- Deduplication by IP becomes even less reliable.
- Exported IP data may be inaccurate.

Recommended fix:

- Default to `REMOTE_ADDR`.
- Add a setting/filter for trusted proxy headers.
- Properly parse comma-separated forwarded IP chains.

### 8. Medium: Search claims exceed actual implementation

Locations: `includes/Admin/ListTables/LeadsListTable.php:50-61`, `readme.txt`

The readme says users can search by IP, UTM parameters, or date. The list table passes the search term to `WP_Query` as `s`, while UTM values are stored as serialized data in `post_content`.

Impact:

- Search behavior is inconsistent and inefficient.
- Serialized data is a poor fit for filtering, indexing, and reporting.

Recommended fix:

- Store UTM fields as normalized post meta or in custom tables.
- Add field-specific filters for campaign/source/medium/date.
- Update the readme until the search behavior is actually implemented.

### 9. Medium: Migration cron scheduling may fail because custom interval is registered late

Location: `includes/Plugin.php:82-89`, `includes/Installer.php:30-33`, `includes/Installer.php:119-130`

Activation calls `Installer::install()` statically, but the custom cron interval is added from the `Installer` constructor, which runs later during normal plugin initialization.

Impact:

- Scheduling `utmm_every_minutes` during activation may fail if the interval has not been registered yet.

Recommended fix:

- Register the interval before scheduling on activation, or avoid a custom interval for migrations.
- Add an activation test around scheduled migration creation.

### 10. Medium: Update framework currently has no registered update callbacks

Location: `includes/Installer.php:23`, `includes/Installer.php:63-80`

`$updates` is empty, but `check_update()` still computes update versions and calls `end( $update_versions )`.

Impact:

- The update path is harder to reason about and may emit warnings in some PHP configurations.
- Future migrations may be skipped or mishandled if the version logic is not tightened.

Recommended fix:

- Short-circuit when there are no update callbacks.
- Add explicit migration versions when schema or storage behavior changes.

### 11. Low: CSV download output is over-sanitized and headers are duplicated

Location: `includes/Controllers/Actions.php:296-320`

The file is emitted through `wp_kses( $file, array() )`, which is not appropriate for raw CSV. Several `Content-Type` and `Content-Disposition` headers are also repeated.

Impact:

- CSV content can be changed unexpectedly.
- Headers are noisy and harder to maintain.

Recommended fix:

- Output raw CSV after authorization and path validation.
- Use one clear set of headers.
- Prefix UTF-8 BOM only if required for spreadsheet compatibility.

### 12. Low: Admin URLs need consistent URL escaping

Locations: `includes/Admin/ListTables/LeadsListTable.php:157-170`, `includes/Admin/views/view-lead.php`

Some admin URLs are built correctly but not escaped at final output.

Impact:

- Low practical risk with admin-generated URLs, but this should be cleaned up for WordPress.org quality.

Recommended fix:

- Use `esc_url()` for `href` attributes.
- Use `esc_attr()` only for attributes that are not URLs.

## Product Direction

The strongest product path is to split the plugin into two layers:

1. A reliable free UTM capture and lead log that site owners can trust.
2. A pro analytics and attribution layer that turns raw events into campaign insights, integrations, and automation.

The free version should not feel broken or artificially limited. It should solve the core problem cleanly: capture UTM visits, view records, filter them, export them, and control privacy. The pro version should add advanced attribution, reporting, integrations, automation, and team/business features.

## Todo List: Free Version

### Stabilization and Correctness

- [ ] Change lead storage so each UTM visit creates a historical event instead of overwriting by IP.
- [ ] Fix CSV export query keys: use `posts_per_page` and `post_status`.
- [ ] Generate export filenames server-side and sanitize with `sanitize_file_name()`.
- [ ] Move temporary CSV exports to a plugin-specific protected folder and clean stale files.
- [ ] Remove unused/misleading `wp_verify_nonce()` calls from public/read-only flows.
- [ ] Default IP detection to `REMOTE_ADDR` and add a filter for trusted proxy headers.
- [ ] Add error handling for `wp_insert_post()` and export file write failures.
- [ ] Guard `vendor/autoload.php` or verify it exists in release builds.
- [ ] Fix migration cron scheduling so the custom interval exists before scheduling.
- [ ] Add a short-circuit for empty installer update callbacks.

### Privacy and Data Controls

- [ ] Add a setting to disable IP storage.
- [ ] Add IP anonymization mode.
- [ ] Add automatic data retention cleanup: 7, 30, 90, 180, 365 days, or never.
- [ ] Add a manual "delete all plugin data" tool with confirmation.
- [ ] Add WordPress personal data exporter and eraser integration.
- [ ] Update readme privacy claims to match implemented controls.

### Admin UX

- [ ] Add filters for date range, UTM source, medium, campaign, and content.
- [ ] Improve list-table sorting for date and key UTM fields.
- [ ] Add a campaign summary widget above the leads table: total visits, top source, top campaign.
- [ ] Add clear empty states and export progress/error messages.
- [ ] Add an admin setting for which columns are shown by default.
- [ ] Improve settings copy and fix typos.

### Data Model and Performance

- [ ] Move UTM values out of serialized `post_content` into queryable storage.
- [ ] For small sites, post meta is acceptable; for growth, prepare a custom table migration.
- [ ] Add indexes if custom tables are introduced.
- [ ] Add batch processing safeguards for export and cleanup jobs.
- [ ] Add a data migration path from the current serialized post content.

### Developer Quality

- [ ] Add PHPUnit tests for lead capture, settings save, migration, and CSV export.
- [ ] Add WordPress coding standards as a real Composer dev dependency.
- [ ] Add CI for PHP linting, PHPCS, JS linting, and build validation.
- [ ] Add release checks for required files: `vendor`, built assets, readme, POT file.
- [ ] Add hooks documentation for available filters/actions.

## Todo List: Pro Version

### Advanced Attribution

- [ ] First-touch, last-touch, and multi-touch attribution.
- [ ] Cookie-based visitor journey tracking.
- [ ] Session tracking with landing page, referrer, device, browser, and country.
- [ ] Conversion event tracking for forms, WooCommerce orders, EDD purchases, and custom events.
- [ ] Revenue attribution by campaign/source/medium.
- [ ] Campaign comparison across date ranges.

### Reporting and Analytics

- [ ] Dashboard charts for visits, conversions, conversion rate, and revenue.
- [ ] Top campaigns, sources, mediums, terms, and content reports.
- [ ] Funnel reports from visit to conversion.
- [ ] Saved reports and scheduled email reports.
- [ ] Custom report builder with selectable dimensions and metrics.
- [ ] UTM performance alerts for traffic spikes, drops, and high-converting campaigns.

### Integrations

- [ ] WooCommerce attribution integration.
- [ ] Easy Digital Downloads attribution integration.
- [ ] Gravity Forms, WPForms, Fluent Forms, Contact Form 7, Ninja Forms, and Elementor Forms integrations.
- [ ] CRM integrations: HubSpot, Zoho, Salesforce, FluentCRM, Groundhogg.
- [ ] Webhook and Zapier/Make integration.
- [ ] Google Sheets export/sync.
- [ ] REST API endpoints for external dashboards and BI tools.

### Campaign Tools

- [ ] UTM link builder with presets.
- [ ] Campaign naming templates and validation rules.
- [ ] Short-link generation and click tracking.
- [ ] QR code generation for offline campaigns.
- [ ] Bulk campaign URL generator/importer.
- [ ] Campaign archive and status management.

### Automation

- [ ] Trigger automations when a visitor arrives from a selected campaign.
- [ ] Add tags to users/customers based on source or campaign.
- [ ] Send lead notifications to email, Slack, Discord, or webhook.
- [ ] Sync high-value conversions to CRMs.
- [ ] Scheduled exports to email, SFTP, Google Drive, or Dropbox.

### Privacy, Compliance, and Teams

- [ ] Consent-mode integration with popular cookie plugins.
- [ ] Configurable data residency/export controls.
- [ ] Role-based access: viewer, analyst, manager, admin.
- [ ] Audit log for exports, deletes, and setting changes.
- [ ] Advanced retention policies per data type.
- [ ] Per-field masking for IP address and visitor identifiers.

### Scalability

- [ ] Custom database tables for events, visitors, sessions, conversions, and campaigns.
- [ ] Background queue for imports, exports, and report generation.
- [ ] Incremental aggregation tables for fast dashboards.
- [ ] WP-CLI commands for migration, export, cleanup, and diagnostics.
- [ ] Multisite network reporting.

## Suggested Enhancement Order

1. Fix critical correctness issues: no IP overwrite, fixed export query, safer export files.
2. Add privacy controls: retention, IP anonymization, data erasure/export.
3. Improve admin filtering and summary reporting for the free version.
4. Refactor storage into queryable fields or custom tables.
5. Add tests and CI before building pro-only features.
6. Build pro attribution and integration features on top of the corrected data model.

## Notes on Current Free/Pro Boundary

Keep these in free:

- Standard UTM capture.
- Historical lead/event records.
- Admin table with basic filters.
- CSV export.
- Basic campaign summary.
- Privacy controls.
- Data delete/retention tools.

Reserve these for pro:

- Conversion and revenue attribution.
- Visitor journey/session tracking.
- Charts, saved reports, scheduled reports.
- Form/ecommerce/CRM integrations.
- Automation and webhooks.
- UTM builder, short links, QR codes.
- Team roles, audit logs, and advanced compliance controls.
- High-scale custom table analytics and WP-CLI tooling.
