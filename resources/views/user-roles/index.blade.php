<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Roles') }}
        </h2>
    </x-slot>
    <div id="acl_wrapper" class="permissions-table">
        <table id="user_roles_table" class="stripe">
            <thead>
            <th>
                User
            </th>
            <th>
                Roles
            </th>
            <th>
                Permissions
            </th>
            <th>

            </th>
            </thead>
            @foreach ($users as $user)
                <tr class="cursor-pointer" id="{!! $user->id !!}" data-name="{!! $user->name !!}">
                    <td>
                        {!! $user->name !!}
                    </td>
                    <td>
                        <ul>
                            @foreach($user->roles()->pluck('name') as $role)
                                <li data-type="role" data-entity-name="{!! $role !!}" class="list-disc">{!! $role !!}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <ul>
                            @foreach($user->permissions()->pluck('name') as $permission)
                                <li data-type="permission" data-entity-name="{!! $permission !!}" class="list-disc">{!! $permission !!}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">
                        <button data-edit="{!! $user->id !!}" data-name="{!! $user->name !!}" class="w-20 ml-3 rounded px-3 py-1 bg-green-300 hover:bg-green-700 hover:text-white focus:shadow-outline focus:outline-none">
                            Edit
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="user-modal">
        <!--Modal-->
        <div class="modal opacity-0 pointer-events-none w-full h-full top-0 left-0 flex items-center justify-center">
            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
            @include('components.token-form')
            @include('user-roles.edit')
        </div>
    </div>

</x-app-layout>
