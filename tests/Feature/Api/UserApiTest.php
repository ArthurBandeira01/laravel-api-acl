<?php

use App\Http\Middleware\ACLMiddleware;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\withoutMiddleware;

beforeEach(function () {
    withoutMiddleware(ACLMiddleware::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test_e2e')->plainTextToken;
});

test('shoud return 200', function () {
    getJson(
        route('users.index'),
        [
            'Authorization' => 'Bearer ' . $this->token
        ]
    )
    ->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email', 'permissions' => []]
        ]
    ])
    ->assertOk();
});

test('shoud return 200 - with many users', function () {
    User::factory()->count(20)->create();
    $response = getJson(
        route('users.index'),
        [
            'Authorization' => 'Bearer ' . $this->token
        ]
    )
    ->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email', 'permissions' => []]
        ]
    ])
    ->assertOk();

    expect(count($response['data']))->toBe(15);
    expect($response['meta']['total'])->toBe(21);
});

test('shoud return users page 2', function () {
    User::factory()->count(22)->create();
    $response = getJson(
        route('users.index') . '?page=2',
        [
            'Authorization' => 'Bearer ' . $this->token
        ]
    )
    ->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email', 'permissions' => []]
        ]
    ])
    ->assertOk();

    expect(count($response['data']))->toBe(8);
    expect($response['meta']['total'])->toBe(23);
});

test('shoud return users with total_per_page', function () {
    User::factory()->count(16)->create();
    $response = getJson(
        route('users.index') . '?total_per_page=4',
        [
            'Authorization' => 'Bearer ' . $this->token
        ]
    )
    ->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email', 'permissions' => []]
        ]
    ])
    ->assertOk();

    expect(count($response['data']))->toBe(4);
    expect($response['meta']['total'])->toBe(17);
    expect($response['meta']['per_page'])->toBe(4);
});

test('shoud return users with filter', function () {
    User::factory()->count(10)->create();
    User::factory()->count(10)->create(['name' => 'custom_user_name']);
    $response = getJson(
        route('users.index') . '?filter=custom_user_name',
        [
            'Authorization' => 'Bearer ' . $this->token
        ]
    )
    ->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email', 'permissions' => []]
        ]
    ])
    ->assertOk();

    expect(count($response['data']))->toBe(10);
    expect($response['meta']['total'])->toBe(10);
});

test('should create new user', function () {
    $response = postJson(route('users.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ],
        [
            'Authorization' => 'Bearer ' . $this->token
        ])->assertCreated();

        assertDatabaseHas('users', [
            'id' => $response['data']['id']
        ]);
});

describe('validations', function () {
    test('should validate create new user', function () {
        postJson(route('users.store'), [], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });
    test('should validate update new user', function () {
        putJson(route('users.update', [$this->user->id]), [], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });
    test('should validate update new user - with password less 3 characters', function () {
        putJson(route('users.update', [$this->user->id]), [
            'name' => 'John Doe',
            'password' => 'pw3',
        ], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password' => trans('validation.min.string', ['attribute' => 'senha', 'min' => 6])
            ]);
    });
});

test('should return user', function () {
    getJson(route('users.show', $this->user->id),
        [
            'Authorization' => 'Bearer ' . $this->token
        ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'permissions' => []]
        ]);
});

test('should return 404 when user not found', function () {
    getJson(route('users.show', 'fake_id'), ['Authorization' => 'Bearer ' . $this->token])
        ->assertNotFound();
});

test('should update user', function () {
    putJson(route('users.update', $this->user->id), [
        'name' => 'John Doe',
    ], [
        'Authorization' => 'Bearer ' . $this->token
    ])
    ->assertOk();

    assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'John Doe',
    ]);
});

test('should return 404 when not update user', function () {
    putJson(route('users.update', 'fake_id'), [
        'name' => 'John Doe',
    ], [
        'Authorization' => 'Bearer ' . $this->token
    ])
    ->assertNotFound();
});

test('should delete user', function () {
    deleteJson(route('users.destroy', $this->user->id), [], [
        'Authorization' => 'Bearer ' . $this->token
    ])->assertNoContent();

    assertDatabaseMissing('users', [
        'id' => $this->user->id
    ]);
});

test('should return 404 when not exists user - delete', function () {
    deleteJson(route('users.destroy', 'fake_id'), [], [
        'Authorization' => 'Bearer ' . $this->token
    ])->assertNotFound();
});
