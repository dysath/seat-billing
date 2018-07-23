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
            'uses' => 'BillingController@getBillingView',
            'middleware' => 'bouncer:billing.view'
        ]);
    });
});


