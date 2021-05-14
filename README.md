# Doctor search

Manage and display doctor records.

## Dependencies

* typo3/cms-core `^10.4`
* sjbr/static-info-tables `^6.9`
* guzzlehttp/guzzle `^6.5`

> TYPO3 - Composer mode REQUIRED!

## Description

This extension provides the possibility to manage and display filtered doctors data. The extension
uses [MapQuest Geocoding API](https://developer.mapquest.com/documentation/geocoding-api/)
to search for doctors and there is a command to fetch geo data for existing doctor records.

## Install

* Activate the extension via the extensions module.
* Include the static typoscript file.
* Define your [MapQuest api key](https://developer.mapquest.com/user/me/apps) in extension configuration.

### Configuration

## Frontend-Preview

```typo3_typoscript
TCEMAIN.preview {
    tx_ricodoctors_domain_model_doctor {
        previewPageId = 123
        useDefaultLanguageRecord = 0
        fieldToParameterMap {
            last_name = tx_ricodoctors_doctors[filterArguments][name]
            zip = tx_ricodoctors_doctors[filterArguments][zip]
        }

        additionalGetParameters {
            tx_ricodoctors_doctors.controller = Doctor
            tx_ricodoctors_doctors.Action = index
        }
    }
}
```

## Countries for frontend dropdown-filter

> see https://docs.typo3.org/typo3cms/extensions/static_info_tables/stable/Manual/Index.html#countriesallowed

```typo3_typoscript
plugin.tx_staticinfotables.settings.countriesAllowed = DEU,AUT,CHE
plugin.tx_staticinfotables_pi1.countriesAllowed = DEU,AUT,CHE
```

## Commands

### Fetch/update geo data of doctor records.

```shell
php vendor/bin/typo3 rico_doctors:geo-data
```

> logs are saved to `var/log/rico_doctors.log`

## Credits

* Extension Icon made by [Kiranshastry](https://www.flaticon.com/authors/kiranshastry) from [Flaticon](www.flaticon.com)
