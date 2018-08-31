<?php

Route::group([
    'namespace' => 'Denngarr\Seat\Billing\Http\Controllers',
    'prefix' => 'billing'
], function () {
    Route::group([
        'middleware' => ['web', 'auth'],
    ], function () {
        Route::get('/', [
            'as'   => 'billing.view',
            'uses' => 'BillingController@getLiveBillingView',
            'middleware' => 'bouncer:billing.view'
        ]);

        Route::get('/alliance/{alliance_id}', [
            'as'   => 'billing.allianceview',
            'uses' => 'BillingController@getLiveBillingView',
            'middleware' => 'bouncer:billing.view'
        ]);

        Route::get('/settings', [
            'as'   => 'billing.settings',
            'uses' => 'BillingController@getBillingSettings',
            'middleware' => 'bouncer:billing.settings'
        ]);

        Route::post('/settings', [
            'as'   => 'billing.savesettings',
            'uses' => 'BillingController@saveBillingSettings',
            'middleware' => 'bouncer:billing.settings'
        ]);

        Route::get('/getindbilling/{id}', [
            'as'   => 'billing.getindbilling',
            'uses' => 'BillingController@getUserBilling',
            'middleware' => 'bouncer:billing.view'
        ]);

        Route::get('/pastbilling/{year}/{month}', [
            'as'   => 'billing.pastbilling',
            'uses' => 'BillingController@previousBillingCycle',
            'middleware' => 'bouncer:billing.view'
        ]);

        Route::get('/getindpastbilling/{id}/{year}/{month}', [
            'as'   => 'billing.getindbilling',
            'uses' => 'BillingController@getPastUserBilling',
            'middleware' => 'bouncer:billing.view'
        ]);
    });
});
