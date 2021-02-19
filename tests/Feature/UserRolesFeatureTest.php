<?php

namespace Tests\Feature;

use Database\Factories\PermissionFactory;
use Faker\Factory;
use Faker\Generator;
use Tests\TestCase;

class UserRolesFeatureTest extends TestCase
{
    protected Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();

        $this->refreshDatabase();
    }

    public function test_can_get_permissions_assigned_to_roles(): void
    {
        $permission = (new PermissionFactory())->create();
        $roleName = $this->faker->word;
        $attributes = [
            'name' => $roleName,
            'role_permission' => [$permission->name]
        ];

        // Create new role and attach permission
        $response = $this->post('/api/roles/create', $attributes);
        $response->assertStatus(200);

        // Get permissions associated with role
        $response = $this->get('/api/user_roles/assigned?role_name=' . $roleName);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
            'permissions' => [
                0 => $permission->name
            ]
        ]);

    }

    public function test_get_permissions_assigned_fails_without_role(): void
    {
        $roleName = null;
        $response = $this->get('/api/user_roles/assigned?role_name=' . $roleName);
        $response->assertStatus(400);
    }
}
