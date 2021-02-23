<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>
    <div id="acl_wrapper" data-context="role" class="report">
        <div class="controls">
            <button id="edit_role" class="modal-open rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 text-white focus:shadow-outline focus:outline-none">
                Add Role
            </button>
        </div>
        <table id="role-table" class="stripe">
            <thead>
            <th>
                Role Name
            </th>
            <th>
                Permissions
            </th>
            <th>

            </th>
            </thead>
            @foreach ($roles as $role)
                <tr class="" id="{!! $role->id !!}" data-name="{!! $role->name !!}">
                    <td>
                        {!! $role->name !!}
                    </td>
                    <td>
                        <ul>
                            @foreach($role->permissions()->pluck('name') as $permission)
                                <li data-type="permission" data-entity-name="{!! $permission !!}" class="list-disc">{!! $permission !!}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="dt-right">
                        <button data-edit="{!! $role->id !!}" class="w-20 ml-3 rounded px-3 py-1 bg-green-300 hover:bg-green-700 hover:text-white focus:shadow-outline focus:outline-none">
                            Edit
                        </button>
                        <button data-delete="{!! $role->id !!}" data-name="{!! $role->name !!}"
                                class="w-20 ml-3 rounded px-3 py-1 bg-red-300 hover:bg-red-700 hover:text-white focus:shadow-outline focus:outline-none">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="role-modal">
        <!--Modal-->
        <div class="modal opacity-0 pointer-events-none w-full h-full top-0 left-0 flex items-center justify-center">
            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
            @include('components.token-form')
            @include('roles.edit')
        </div>
        <div>
            @include('components.confirmation-dialog')
        </div>
    </div>
    </div>

</x-app-layout>
