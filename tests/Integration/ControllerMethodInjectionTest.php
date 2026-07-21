<?php

use LaraPkgs\Validation\Tests\TestSupport\MethodInjectionTestController;

describe('Controller Method Injection', function () {
    beforeEach(function () {
        Route::post('/test', MethodInjectionTestController::class);
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
                'data' => ['property' => 'value']
            ]);
    });
});
