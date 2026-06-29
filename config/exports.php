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
];
