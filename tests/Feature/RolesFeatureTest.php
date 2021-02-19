<?php

namespace Tests\Feature;

use Database\Factories\PermissionFactory;
use Faker\Factory;
use Faker\Generator;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RolesFeatureTest extends TestCase
{
    protected Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();

        $this->refreshDatabase();
    }

    public function test_can_create_new_role(): void
    {
        $attributes = [
            'name' => $this->faker->word
        ];

        $response = $this->post('/api/roles/create', $attributes);

        $response->assertStatus(200);

        $this->assertDatabaseHas((new Role())->getTable(), $attributes);
    }

    public function test_can_create_role_with_permissions(): void
    {
        $permission = (new PermissionFactory())->create();
        $roleName = $this->faker->word;
        $attributes = [
            'name' => $roleName,
            'role_permission' => [$permission->name]
        ];

        // Create new role and permission
        $response = $this->post('/api/roles/create', $attributes);
        $response->assertStatus(200);

        // Get permissions associated with role
        $response = $this->get('/api/roles/permissions?role_name=' . $roleName);
        $response->assertStatus(200);
        // Test for correct response
        $response->assertJsonFragment([
            'success' => true,
            'permissions' => [
                0 => $permission->name
            ]
        ]);

        // Make sure permission exists in database
        $this->assertDatabaseHas((new Permission())->getTable(), ['name' => $permission->name]);
    }

    public function test_can_update_role_with_permissions(): void
    {
        $roleName = $this->faker->word;
        $attributes = [
            'name' => $roleName
        ];

        // Create new role and permission
        $response = $this->post('/api/roles/create', $attributes);
        $permission = (new PermissionFactory())->create();

        $attributes = [
            'id' => $response->json('id'),
            'name' => $roleName,
            'role_permission' => [$permission->name]
        ];

        // Attach permission to role
        $response = $this->put('/api/roles/update', $attributes);
        $response->assertStatus(200);

        // Get permissions associated with role
        $response = $this->get('/api/roles/permissions?role_name=' . $roleName);
        $response->assertStatus(200);

        // Make sure permission exists in database
        $this->assertDatabaseHas((new Permission())->getTable(), ['name' => $permission->name]);

    }

    public function test_can_remove_permissions_from_role(): void
    {
        $roleName = $this->faker->word;
        $attributes = [
            'name' => $roleName
        ];

        // Create new role and permissions
        $response = $this->post('/api/roles/create', $attributes);
        $permissions = (new PermissionFactory())->count(4)->create();
        $roleId = $response->json('id');
        $permissionNames = $permissions->map(static function ($item) {
            return $item->name;
        });
        $attributes = [
            'id' => $roleId,
            'name' => $roleName,
            'role_permission' => $permissionNames->toArray()
        ];
        // Attach role to permission
        $response = $this->put('/api/roles/update', $attributes);
        $response->assertStatus(200);

        // Detach a permission from the role
        $permissionNames->shift();
        $updatedAttributes = [
            'id' => $roleId,
            'name' => $roleName,
            'role_permission' => $permissionNames->toArray()
        ];
        $response = $this->put('/api/roles/update', $updatedAttributes);
        $response->assertStatus(200);
    }

    public function test_can_update_role(): void
    {
        $attributes = [
            'name' => $this->faker->word
        ];
        $response = $this->post('/api/roles/create', $attributes);
        $response->assertStatus(200);
        $roleId = $response->json('id');

        $attributes['id'] = $roleId;
        $this->assertDatabaseHas((new Role())->getTable(), $attributes);

        $newAttributes = [
            'id' => $roleId,
            'name' => $this->faker->word
        ];

        $response = $this->put('/api/roles/update', $newAttributes);
        $response->assertStatus(200);

        $this->assertDatabaseHas((new Role())->getTable(), $newAttributes);
    }

    public function test_can_delete_role(): void
    {
        $attributes = [
            'name' => $this->faker->word
        ];
        $response = $this->post('/api/roles/create', $attributes);
        $response->assertStatus(200);
        $roleId = $response->json('id');
        $attributes['id'] = $roleId;

        $this->assertDatabaseHas((new Role())->getTable(), $attributes);

        $response = $this->delete('/api/roles/delete', $attributes);
        $response->assertStatus(200);

        $this->assertDatabaseMissing((new Role())->getTable(), $attributes);
    }
}
