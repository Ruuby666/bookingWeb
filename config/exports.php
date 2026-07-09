<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Export policy
    |--------------------------------------------------------------------------
    |
    | Control whether `super_admin` users are allowed to export reservations
    | belonging to other owners. Set to `false` to restrict super admins to
    | only export reservations for properties they own.
    |
    */
    'super_admin_can_export_all' => env('SUPER_ADMIN_CAN_EXPORT_ALL', false),

    /*
    |--------------------------------------------------------------------------
    | Billing profiles
    |--------------------------------------------------------------------------
    |
    | Invoicing details used by FacturasExport. Keyed by property title so
    | each property can be billed under its own legal entity. These must
    | never be hardcoded in source — they are personal/fiscal data (name,
    | tax ID, address) and belong in the environment, not in version control.
    | Add one env-backed entry per property that needs invoicing, and fall
    | back to `default` for anything not explicitly listed.
    |
    */
    'billing_profiles' => [
        'El Galeon' => [
            'name' => env('BILLING_EL_GALEON_NAME'),
            'tax_id' => env('BILLING_EL_GALEON_TAX_ID'),
            // Fixed display name/address for this invoicing entity — intentionally
            // not derived from the property's title/location, matching prior behavior.
            'display_name' => env('BILLING_EL_GALEON_DISPLAY_NAME'),
            'address_line1' => env('BILLING_EL_GALEON_ADDRESS_LINE1'),
            'address_line2' => env('BILLING_EL_GALEON_ADDRESS_LINE2'),
        ],
        'default' => [
            'name' => env('BILLING_DEFAULT_NAME'),
            'tax_id' => env('BILLING_DEFAULT_TAX_ID'),
        ],
    ],
];
