# Drupal 11 upgrade evidence

Local upgrade completed on 20 October 2026, from Drupal 10.6.19 to 11.4.10.
See the [complete work and maintenance record](../../DRUPAL-11-READINESS.md).

These are point-in-time records, not continuously refreshed monitoring reports.

| File | Purpose |
| --- | --- |
| [composer-update.txt](composer-update.txt) | Actual major-upgrade dependency changes and scaffold generation |
| [composer-install-check.txt](composer-install-check.txt) | Lock-file installation consistency after the upgrade |
| [database-updates.txt](database-updates.txt) | Completed database updates and post-updates |
| [verification.txt](verification.txt) | Read-only theme, crop-form, menu and removed-extension checks |
| [http-checks.txt](http-checks.txt) | Successful page and stylesheet requests |
| [platform-checks.txt](platform-checks.txt) | PHP and extension requirement checks |
| [composer-audit.json](composer-audit.json) | Zero vulnerability advisories; existing abandoned-package notice |
| [status-errors.json](status-errors.json) | Empty list of error-level Drupal requirements |
| [config-changes.json](config-changes.json) | Objects changed/removed by the upgrade and Fitvids correction, before full export |
| [fresh-config-export.txt](fresh-config-export.txt) | Fresh export into root sync/ |
| [config-status.txt](config-status.txt) | No differences between active configuration and root sync/ after consolidation |

Detailed temporary scans, manifests and snapshots were archived outside the
project before cleaning tmp/. Backup/archive locations and test limitations
are documented in the complete work record. Database dumps are not included here.
