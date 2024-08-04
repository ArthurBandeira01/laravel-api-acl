<?php

use function Pest\Laravel\getJson;

it('show return status code 200', function () {
    getJson('/')->assertStatus(200);
});
