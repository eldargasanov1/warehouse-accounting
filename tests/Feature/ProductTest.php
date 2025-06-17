<?php

describe('products', function () {
    test('user can get products list', function () {
        $res = $this->json('GET', '/api/products');

        expect($res)
            ->isOk()
            ->and($res)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'price', 'stocks']],
            ]);
    });
})->group('products');
