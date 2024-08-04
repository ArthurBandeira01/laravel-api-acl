<?php

use App\Models\User;
use function Pest\Laravel\postJson;

test('show auth user', function () {
    $user = User::factory()->create();
    $data = [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'e2e_test',
    ];
    postJson(route('auth.login'), $data)
        ->assertOk()
        ->assertJsonStructure(['token']);
});

test('should fail auth - with password', function () {
    $user = User::factory()->create();
    $data = [
        'email' => $user->email,
        'password' => 'asasdas',
        'device_name' => 'e2e_test',
    ];
    postJson(route('auth.login'), $data)
        ->assertStatus(422);
});

test('should fail auth - with email', function () {
    $data = [
        'email' => 'fake@email.com',
        'password' => 'password',
        'device_name' => 'e2e_test',
    ];
    postJson(route('auth.login'), $data)
        ->assertStatus(422);
});

describe('validations', function () {
    it('should require email', function () {
        postJson(route('auth.login'), [
            'password' => 'password',
            'device_name' => 'e2e_test',
        ])
        ->assertJsonValidationErrors([
            'email' => trans('validation.required', ['attribute' => 'email'])
        ])
        ->assertStatus(422);
    });
    it('should require password', function () {
        $user = User::factory()->create();
        postJson(route('auth.login'), [
            'email' => $user->email,
            'device_name' => 'e2e_test',
        ])
        ->assertJsonValidationErrors([
            'password' => trans('validation.required', ['attribute' => 'senha'])
        ])
        ->assertStatus(422);
    });
    it('should require device name', function () {
        $user = User::factory()->create();
        postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertJsonValidationErrors([
            'device_name' => trans('validation.required', ['attribute' => 'nome do dispositivo'])
        ])
        ->assertStatus(422);
    });
});
