<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-reservation-info')
    ->dailyAt('09:00');

//  When id do the development * * * * * cd /var/www/bookingWeb && php artisan schedule:run >> /dev/null 2>&1
