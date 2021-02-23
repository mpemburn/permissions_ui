### File Structure

The **Permissions UI** project uses the same structure as current versions of Laravel (version 8.x at the moment).  In case you want to use these files in an existing Laravel project, here's where to find them:
```
project root
│   
└───app
│   │   
│   └───Http
|   |   |
|   |   └───Controllers
│   |       └───AdminController.php
│   |       └───PermissionsController.php
│   |       └───RolesController.php
|   |       └───UserRolesController.php
|   |       
|   └───Interfaces    
|   |   └───UiInterface.php
|   └───Models
|   |   └───PermissionUi.php
|   |   └───RoleUi.php
|   └───Services
|   |   └───AuthService.php
|   |   └───PermissionsAssociationService.php
|   |   └───PermissionsCrudService.php
|   |   └───RolesService.php
|   |   └───UserRolesService.php
|   |   └───ValidationService.php
|   └───resources
|   |   └───css
|   |   |   └───app.css *
|   |   |   └───permissions.css
|   |   └───js
|   |   |   └───app.js *
|   |   |   └───comparator.js
|   |   |   └───confirmation.js
|   |   |   └───modal.js
|   |   |   └───permissions-manager.js
|   |   |   └───request-ajax.js
|   |   |   └───user-roles-manager.js
|   |   └───components
|   |   |   └───confirmation-dialog.blade.php
|   |   |   └───token-form.blade.php
|   |   └───layouts
|   |   |   └───navigation.blade.php *
|   |   └───permissions
|   |   |   └───edit.blade.php
|   |   |   └───index.blade.php
|   |   └───roles
|   |   |   └───edit.blade.php
|   |   |   └───index.blade.php
|   |   └───user-roles
|   |       └───edit.blade.php
|   |       └───index.blade.php
|   └───routes
|       └───api.php * 
|       └───web.php * 
└───.env.example *
└───composer.json *
└───package.json *
```
**NOTES:** Most files above are unique to this project. Files flagged with an asterisk (*) are Laravel files that were edited for use with **Permissions UI**.

- `resources/css/app.css`:

    By default, this file is empty.  Everything else found here is required.
    
- `resources/app.js`:

    By default, this file only contains `require('./bootstrap');`.  Everything else found here is required.
    
- `navigation.blade.php`:

    The dropdown **Admin** menu is the only thing added to this file:
    ```
    <!-- Admin Dropdown -->
    <div class="hidden sm:flex sm:items-center sm:ml-6 pt-1">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                    <div>Admin</div>
    
                    <div class="ml-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>
    
            <x-slot name="content">
                <x-dropdown-link :href="route('roles')" active="request()->routeIs('roles')">
                    {{ __('Roles') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('permissions')" active="request()->routeIs('permissions')">
                    {{ __('Permissions') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('user_roles')" active="request()->routeIs('user_roles')">
                    {{ __('User Roles') }}
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
    ```
- `api.php`:

    All `middleware` API routes are part of this project:

    ```
    Route::middleware('auth:api')->group( function () {
        Route::post('/roles/create', RolesController::class . '@create');
        Route::put('/roles/update', RolesController::class . '@update');
        Route::delete('/roles/delete', RolesController::class . '@delete');
        Route::get('/roles/permissions', RolesController::class . '@getPermissions');
    
        Route::post('/permissions/create', PermissionsController::class . '@create');
        Route::put('/permissions/update', PermissionsController::class . '@update');
        Route::delete('/permissions/delete', PermissionsController::class . '@delete');
    
        Route::post('/user_roles', UserRolesController::class . '@edit');
        Route::get('/user_roles/assigned', UserRolesController::class . '@getAssigned');
    });
    
    ```
- `web.api`:

    The `middleware` web routes, with the exception of `/dashboard` are part of this project:

    ```
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    
        Route::get('/roles', AdminController::class . '@roles')->name('roles');
        Route::get('/permissions', AdminController::class . '@permissions')->name('permissions');
        Route::get('/user_roles', AdminController::class . '@userRoles')->name('user_roles');
    });
    ```
- `.env.example`:

    This should be copied to `.env` as noted in the [**Installation Notes**](installation.md).  Any Roles that you want to protect from being renamed should be added to the `PROTECTED_ROLES` environment variable as a comma-delimited list.

- `composer.json`:

    The following items have been added or modified for this project:
    ```
      "require": {
          "php": "7.4",
          "laravel/passport": "^10.1",
          "laravelcollective/html": "^6.2",
          "spatie/laravel-permission": "^4.0"
      },
      "require-dev": {
          "laravel/breeze": "^1.1",
      },

    ```
- `package.json`:

    The following items have been added or modified for this project:
    ```
      "scripts": {
          "dev": "npm run development",
          "development": "mix",
          "watch": "mix watch",
          "watch-poll": "mix watch -- --watch-options-poll=1000",
          "hot": "mix watch --hot",
          "prod": "npm run production",
          "production": "mix --production"
      },
      "devDependencies": {
          "@tailwindcss/forms": "^0.2.1",
          "alpinejs": "^2.7.3",
          "autoprefixer": "^10.1.0",
          "axios": "^0.21",
          "jquery": "^3.5.1",
          "laravel-mix": "^6.0.6",
          "lodash": "^4.17.19",
          "postcss": "^8.2.1",
          "postcss-import": "^12.0.1",
          "tailwindcss": "^2.0.2"
      },
    ```
