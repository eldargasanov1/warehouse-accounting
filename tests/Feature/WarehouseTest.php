<?php

describe('warehouses', function () {
    test('user can get warehouses list', function () {
        $res = $this->json('GET', '/api/warehouses');

        expect($res)
            ->isOk()
            ->and($res)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'created_at', 'updated_at']],
            ]);
    });
})->group('warehouses');
