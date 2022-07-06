# amos-partnership-profiles

Plugin to make partnership profiles.

## Installation

### 1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require open20/amos-partnership-profiles
```

or add this row

```
"open20/amos-partnership-profiles": "~1.0"
```

to the require section of your `composer.json` file.


### 2. Add module to your main config in backend:
	
```php
<?php
'modules' => [
    'partnershipprofiles' => [
        'class' => 'open20\amos\partnershipprofiles\Module'
    ],
],
```


### 3. Apply migrations

```bash
php yii migrate/up --migrationPath=@vendor/open20/amos-partnership-profiles/src/migrations
```

or add this row to your migrations config in console:

```php
<?php
return [
    '@vendor/open20/amos-partnership-profiles/src/migrations',
];
```


### 4. Add configuration to tag module. In backend/config/modules-amos.php add configuration like this:

```php
<?php

if (isset($modules['tag'])) {
    ...
    if (isset($modules['partnershipprofiles'])) {
        $modules['tag']['modelsEnabled'][] = 'open20\amos\partnershipprofiles\models\PartnershipProfiles';
        $modules['tag']['modelsEnabled'][] = 'open20\amos\partnershipprofiles\models\ExpressionsOfInterest';
    }
    ...
}
```

Then go in the tag manager and configure the roles for the trees you want for this model.


### 5. Add model of partnership profiles to the report module:

```php
<?php
'modules' => [
    'report' => [
        'class' => 'open20\amos\report\AmosReport',
        'modelsEnabled' => [
            'open20\amos\partnershipprofiles\models\PartnershipProfiles',
        ]
    ],
],
```


### 6. Configure the cwh for the partnership profile model:

Go to the cwh configuration and set the field of the status and the default status.
The cwh configuration is at this url: /cwh/configuration/wizard


### 7. Action to archive the proposal 
php yii /partnershipprofiles/partnership-profiles-console/archive-partnership-profiles

## Configurable fields 

Here the list of configurable fields, properties of module AmosPartnershipProfiles.
If some property default is not suitable for your project, you can configure it in module-amos, eg: 

```php
'fieldsConfigurations' => [
            'required' => [
                'extended_description',
                'expected_contribution',
                'partnership_profile_date',
                'expiration_in_months',
            ],
            'tabs' => [
                'tab-more-information' => false,
                'tab-attachments' => true
            ],
            'fields' => [
                //tab general
                'title' => true,
                'short_description' => false,
                'extended_description' => true,
                'advantages_innovative_aspects' => false,
                'expected_contribution' => true,
                'partnership_profile_date' => true,
                'expiration_in_months' => false,
                'attrPartnershipProfilesTypesMm' => false,
                'other_prospect_desired_collab' => false,
                'contact_person' => true,
            ],
        ]
 
```
If you want to modify this fields only for a specific community , you can add it on module-amos:
```php
        'fieldsCommunityConfigurations' => [
            'communityId-5' => [
                'required' => [
                    'extended_description',
                    'expected_contribution',
                    'partnership_profile_date',
                    'expiration_in_months',
                ],
                'tabs' => [
                    'tab-more-information' => false,
                    'tab-attachments' => true
                ],
                'fields' => [
                    //tab general
                    'title' => true,
                    'short_description' => false,
                    'extended_description' => true,
                    'advantages_innovative_aspects' => false,
                    'expected_contribution' => true,
                    'partnership_profile_date' => true,
                    'expiration_in_months' => false,
                    'attrPartnershipProfilesTypesMm' => false,
                    'other_prospect_desired_collab' => false,
                    'contact_person' => true,
                ],
            ]
        ],
```