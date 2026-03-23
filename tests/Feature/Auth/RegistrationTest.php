<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('new users can not register from a public route', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertStatus(404);
});
