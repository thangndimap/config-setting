
Route::group(['middleware' => ['auth:web','keycloak-web-can']], function () use ($router) {
    Route::resources([
        'configuration' => 'ConfigurationController'
    ]); 
});
