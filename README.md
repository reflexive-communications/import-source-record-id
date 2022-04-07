# import-source-record-id

[![CI](https://github.com/reflexive-communications/import-source-record-id/actions/workflows/main.yml/badge.svg)](https://github.com/reflexive-communications/import-source-record-id/actions/workflows/main.yml)

This extension provides a custom importer.

Activity import where the source record id mapping is also possible. If you want to execute this importer, navigate to
the `Contacts > Import Activities With Source Record Id` menu. The data mapping screen provides the source record id as
a new mapping parameter. The import process validates this id, the import will skip the activity if the given source
record id does not exists in the CRM system. The import will also skip the activity if the same activity could be found
in the system (same source contact, activity type and source record id).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM v5.43

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and install it
with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/reflexive-communications/import-source-record-id.git
cv ext:enable import-source-record-id
```
