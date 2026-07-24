<?php

use LaraPkgs\Validation\Tests\TestSupport\FormRequestTestController;

describe('Controller Method Injection', function () {
    beforeEach(function () {
        Route::post('/test', FormRequestTestController::class);
    });

    it('fails on invalid data', function () {
        $this->postJson('/test', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['property']);
    });

    it('passes validation when given valid payload', function () {
        $this->postJson('/test', ['property' => 'value'])
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => ['property' => 'value'],
            ]);
    });
});
