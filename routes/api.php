Route::group(['middleware' => ['auth:api']], function () use ($router) {
    Route::apiResource('configurations', 'Api\ConfigurationController')->only(['index', 'show']);
});
