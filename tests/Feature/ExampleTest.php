<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('shows a custom 404 page for missing urls', function () {
    $this->get('/missing-page-for-book-store')
        ->assertNotFound()
        ->assertSee('Страница не найдена')
        ->assertSee('404')
        ->assertSee('Открыть каталог');
});
