# Location Landscapes: Drupal 11 upgrade and maintenance record

Work completed on the local DDEV copy on **20 October 2026**. This document records the final state, the preparation and upgrade work, subsequent cleanup, verification, and recovery locations.

## Final state

- Drupal **11.4.10**; all four root core package constraints and core-dev use **^11**.
- Drush **13.8.0**, PHP **8.3.30**, MariaDB **11.8**, DDEV project type **drupal11**.
- The site bootstraps and connects to its database; all upgrade database updates completed.
- Composer audit found **zero vulnerability advisories** after the upgrade.
- Project-root **sync/** is the sole configured location for saved configuration: **488 objects**, matching the active database when last checked.
- Tour is uninstalled and its contributed package removed. The obsolete modules-old/ directory was removed.
- Upgrade Status remains installed/enabled. components/ was inspected and left unchanged.
- No production deployment or Git commit was performed. Pre-existing unrelated working-tree changes were preserved.

## 1. Upgrade assessment and tooling

Initially inspected Drupal 10.6.15, Composer dependencies, enabled extensions, the custom theme, and the DDEV platform. Installed **Upgrade Status 4.3.10** with Composer and enabled it.

Added the matching **drupal/core-dev** development dependency to provide the test dependencies needed for useful static analysis. The first scan without those dependencies produced many missing-test-class warnings; subsequent scans used the matching development tools.

Refreshed available-release metadata and checked Composer security advisories. Identified four contributed-project version blockers, the custom theme's Drupal 10-only declaration, Drush 12, and core Tour.

Ran full scans and dependency dry runs before changing core. Preparation eventually showed 100% declared compatibility and passing environment checks. That was a declaration/environment result, not proof that all static-analysis warnings had disappeared.

The scan with contributed Tour still installed covered 38 projects: 18 had no findings, while the rest included 1,041 review findings and 42 findings categorized as ignorable for the next major version. Examples involved bundled tests, optional integrations and APIs absent from the Drupal 10 analysis environment. Tour was subsequently removed. Historical full scan output is in the external working archive listed below.

Upgrade Status now runs on Drupal 11, so its next-major checks concern **Drupal 12**. Historical preparation scores are not a fresh post-upgrade assessment.

## 2. Dependency preparation on Drupal 10

| Component | Initial version | Prepared/final contributed version |
| --- | --- | --- |
| Drupal core and matching development tools | 10.6.15 | Prepared on 10.6.19; finally upgraded to 11.4.10 |
| Bootstrap5 | 3.0.16 | 4.0.8 |
| Image Widget Crop | 2.4.0 | 3.0.0 |
| Responsive Favicons | 2.0.2 | 4.0.0 |
| Responsive Menu | 5.0.0-alpha2 | 4.4.5 |
| Webform | 6.3.0 | 6.3.1 |
| Drush | 12.5.3 | 13.8.0 |
| Image Hover Effects | 2.0.2 | 2.0.2, unchanged and enabled |
| Upgrade Status | Not installed | 4.3.10, enabled |

Applied Drupal 10 core, Webform and Symfony security fixes before upgrading the major version. Symfony YAML moved from 6.4.44 to 6.4.48 during tooling preparation; HTTP Foundation moved to 6.4.48 during security preparation. The final Drupal 11 upgrade subsequently selected its required Symfony 7 dependencies. composer.lock records every exact dependency version; the retained Composer log records the major-upgrade changes.

### Responsive Menu downgrade

Compared the alpha and stable branch's configuration schema and frontend code. Stable 4.4.5 supports Drupal 10/11 and the existing mmenu 8 library.

Preserved the main menu, right-hand dark panel, pagedim, 768px breakpoint, wrapper setting and Bootstrap integration setting. Removed the unsupported toolbar_override key and explicitly disabled optional polyfills.

Verified the desktop Media dropdown, mobile menu opening, Media submenu and navigation to Press. The stable release changes toolbar positioning and uses mmenu's default page selection; those behaviours worked in the checks. Requirements checks found mmenu 8.5.24 and Superfish 1.7.10.

### Existing patch and notices

The Colorbox Media Video responsive-video patch in composer.json was retained; that contributed package was not upgraded. Its applied-patch record remained present.

The existing oomphinc/composer-installers-extender abandonment notice remains. It is not a vulnerability advisory. Composer validation also retains the existing wildcard-version warning for the manually declared PDF.js package. Composer audit does not establish the security of every manually downloaded browser library.

## 3. Custom theme

In themes/custom/locationlandscapes/:

- Changed core_version_requirement from ^9.4 || ^10 to **^10.3 || ^11**.
- Replaced Renderer::renderPlain() with **renderInIsolation()** and removed an unused import.
- Rebuilt site and CKEditor CSS, including source maps, against Bootstrap5 4.0.8 using Dart Sass 1.98.0.
- Corrected the theme README title and documented compatibility/build information.

All five custom Twig templates parsed successfully, and the preparation scan reported zero custom-theme findings.

To rebuild styles from the theme directory:

```sh
sass scss/style.scss css/style.css
sass scss/ck5style.scss css/ck5style.css
```

Sass reported deprecation warnings from existing SCSS, but both builds completed.

## 4. Tour and directory cleanup

Tour provides optional guided help for administration screens. Initially replaced core Tour with contributed **2.0.17**, ran its updates and verified all seven existing tours were preserved.

At the owner's subsequent request:

- Uninstalled Tour using Drush.
- Removed drupal/tour with Composer and deleted its contributed directory.
- Removed saved Tour settings, tour entities, the Tour token view mode, enablement, and the content-editor role's access-tour permission/dependency.
- Verified Tour was disabled, no Tour configuration remained, and the homepage still loaded.

Also removed **modules-old/** at the owner's request. It existed before this work and contained an older Image Hover Effects 1.1 development copy. Drupal was using **modules/contrib/image_hover_effects**, version 2.0.2, which was retained.

Inspected **components/**. It contains RequireJS 2.1.5 in two identical approximately 79 KB files (require.js and require-built.js), a small loader configuration, and an empty require.css. No references were found in the checked custom code or exported configuration. It appears to be legacy material; it was **not removed or changed**.

## 5. Actual Drupal 11 upgrade

After backing up the database, Composer files, theme and scaffold files:

1. Set drupal/core-recommended, core-composer-scaffold, core-project-message, core-vendor-hardening and core-dev constraints to **^11**.
2. Explicitly allowed the **symfony/runtime** Composer plugin required by Drupal 11.4.
3. Ran a dependency dry run, then the real Composer update. It selected **11.4.10**, retaining the prepared contributed-project versions.
4. Composer regenerated the Drupal scaffold files and runtime autoloader. The pre-upgrade scaffold comparison found no custom changes to carry forward.
5. Ran all database updates and post-updates and rebuilt caches.
6. Changed .ddev/config.yaml to project type **drupal11** and restarted DDEV. PHP/database versions were retained.
7. Verified that a Composer install dry run had nothing to install, update or remove.

Drupal enabled **Search Node** to retain node search and migrated search dependencies/configuration. Help Search remained disabled. Updates also changed Views, editor/list settings, blocks and performance configuration, and removed obsolete field.settings and system.rss objects.

### Fitvids correction

Browser testing found a real JavaScript exception: Fitvids' stored selector list ended with a blank line, producing an invalid comma-separated selector under Drupal 11's jQuery.

Normalised the setting to two non-empty selectors, **.node** and **#colorbox**, with consistent line endings. After cache rebuild/reload, the exception cleared. The corrected setting is included in the saved configuration.

## 6. Configuration: root sync/ is authoritative

The site originally used sites/default/files/sync, while the repository also contained a different root sync/ folder. During preparation/upgrade, only affected objects were saved to both. The upgrade plus Fitvids correction changed 48 objects and removed two; the [object inventory](docs/drupal11-upgrade/config-changes.json) records that stage.

At the owner's request, subsequently consolidated configuration:

1. Backed up both previous directories and settings.php.
2. Set this in sites/default/settings.php, before the DDEV include:
   ```php
   $settings['config_sync_directory'] = DRUPAL_ROOT . '/sync';
   ```
3. Purged all previous YAML files from both directories.
4. Ran a **fresh full export** of the active configuration into root sync/.
5. Verified **488 exported objects** and **no differences** between saved and active configuration.

The old sites/default/files/sync location contains no configuration YAML and is no longer configured for imports/exports. No full configuration import was performed during consolidation.

Because sync/ is inside this project's document root, preserved its Apache .htaccess protection and added **.ddev/nginx/config-sync.conf**. Confirmed HTTP requests for the directory and a YAML file return **403**, while the homepage returns **200**.

The settings.php and .ddev changes are covered by existing Git ignore rules. They must be carried to other environments separately as appropriate; an Nginx deployment needs an equivalent deny rule for /sync.

### Future configuration workflow

Run from the project root:

```sh
# Check saved configuration against the current database.
ddev drush config:status

# Save intentional local configuration changes into root sync/.
ddev drush config:export -y

# Review files before committing.
git status --short -- sync

# Apply reviewed saved configuration to an environment's database.
ddev drush config:import -y
ddev drush cache:rebuild
```

Export writes database configuration to files; import applies saved files to the database. Do not resume maintaining a second copy in sites/default/files/sync. At the end of this work the project had untracked files, including sync/; no commit or push was made.

## 7. Verification and remaining acceptance testing

Completed on Drupal 11:

- Successful bootstrap/database connection and no pending database updates.
- Composer validation, lock/install consistency, platform requirements and zero security advisories.
- Drupal status report with no error-level requirements.
- All five custom Twig templates parsed.
- Read-only rendering of the staff Image Widget Crop form and Responsive Menu settings form.
- Checks for absent removed core extensions and preserved menu settings.
- HTTP 200 for home, About, Gardens, a garden detail, Show Gardens, Team, Press, Contact and both custom stylesheets.
- Browser checks of desktop navigation, mobile navigation/submenu at 440px, garden listing/detail, gallery next-slide controls, garden accordion and cookie rejection.
- Confirmation that the Fitvids exception cleared.
- Final configuration comparison with no differences, and HTTP 403 for exported configuration.

An mmenu focus/aria-hidden accessibility warning appeared when opening its mobile submenu; navigation worked.

No new content, uploads/crop saves or Webform messages were submitted. Production deployment, mail delivery, upload/crop persistence, a complete editorial acceptance test and broader accessibility testing were not performed.

## 8. Permanent evidence

Selected reports are retained in [docs/drupal11-upgrade/](docs/drupal11-upgrade/README.md):

- Composer update/install-check logs.
- Database update log.
- Verification results, HTTP checks and platform checks.
- Security audit and error-level status report.
- Configuration change inventory, fresh export confirmation and final configuration status.

The temporary verification script depended on the preparation snapshots; its results are retained instead of presenting it as a standalone maintenance tool. Verbose scans, temporary manifests, page snapshots and the pre-existing config.tar.gz were archived outside the project before cleaning tmp/.

## 9. Backups and recovery

These are local **temporary-storage** paths, not committed or durable backup storage:

| Location | Contents |
| --- | --- |
| /private/tmp/locationlandscapes11-upgrade-backup/ | Original Composer files, pre-Upgrade-Status-enablement database dump and early diagnostic artifacts |
| /private/tmp/locationlandscapes11-preparation-before/ | Database/Composer state before contributed/security preparation, original custom theme and menu code |
| /private/tmp/locationlandscapes11-before-drupal11/ | Pre-major-upgrade database, Composer files, theme, scaffold files and affected saved configuration |
| /private/tmp/locationlandscapes11-sync-before-w9xxssaq/ | Both previous sync directories and settings.php before consolidation |
| /private/tmp/locationlandscapes11-work-record-20261020-onexw46g.tar.gz | Full project tmp/ contents before cleanup, including the old config.tar.gz and all working reports/scripts |

A rollback across the major upgrade must restore the **database and matching code/dependencies together**. Restoring Composer alone would pair Drupal 10 code with a Drupal 11-updated database. The saved backups do not constitute a full independent copy of every uploaded public/private file.

## 10. Working-folder cleanup

Archived all previous project tmp/ contents, retained the selected evidence above, and removed the working reports/scripts and old config.tar.gz from the project. Preserved tmp/.htaccess. Added ignore rules so future tmp/ contents are not accidentally versioned, while its protective .htaccess remains eligible for version control.

Other pre-existing files, including composer-working.json and components/, were left in place. Existing deployment configuration was not executed or changed.

## Reference

The upgrade followed the [official Drupal 10 to Drupal 11 guide](https://www.drupal.org/docs/upgrading-drupal/upgrading-drupal/how-to-upgrade-from-drupal-10-to-drupal-11). Exact installed dependencies are recorded in composer.lock.
