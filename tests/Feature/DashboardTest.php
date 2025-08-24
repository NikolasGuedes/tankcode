<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/students');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the students', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/students');
    $response->assertStatus(200);
});