<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function it_creates_tenant_on_registration(): void
    {
        $payload = [
            'name' => 'Google LTD',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post('/register', $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'name' => $payload['name']
        ]);

        $this->assertDatabaseHas('tenants', [
            'name' => strtolower(explode(' ', $payload['name'])[0])
        ]);
    }

    /**
     * @test
     */
    public function item_created_by_user_has_his_tenant_id()
    {
        $payload = [
            'name' => 'Google LTD',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post('/register', $payload);

        $response->assertStatus(302);

        $user = auth()->user();

        $payload = ['name' => 'item One'];
        $response = $this->actingAs($user)->post(route('items.store', ['tenant' => $user->tenant->name]), $payload);

        $this->assertDatabaseHas('items', [
            'name' => $payload['name'],
            'tenant_id' => $user->tenant_id
        ]);
    }
}
