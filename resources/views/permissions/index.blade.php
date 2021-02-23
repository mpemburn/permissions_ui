<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permissions') }}
        </h2>
    </x-slot>
    <div id="acl_wrapper" data-context="permission" class="report">
        <div class="controls">
            <button id="edit_permission" class="modal-open rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 text-white focus:shadow-outline focus:outline-none">
                Add Permission
            </button>
        </div>
        <table id="permission-table" class="stripe">
            <thead>
                <th>
                    Permission Name
                </th>
                <th>

                </th>
            </thead>
            @foreach ($permissions as $permission)
                <tr class="" id="{!! $permission->id !!}" data-name="{!! $permission->name !!}">
                    <td>
                        {!! $permission->name !!}
                    </td>
                    <td class="dt-right">
                        <button data-edit="{!! $permission->id !!}" class="w-20 ml-3 rounded px-3 py-1 bg-green-300 hover:bg-green-700 hover:text-white focus:shadow-outline focus:outline-none">
                            Edit
                        </button>
                        <button data-delete="{!! $permission->id !!}" data-name="{!! $permission->name !!}"
                                class="w-20 ml-3 rounded px-3 py-1 bg-red-300 hover:bg-red-700 hover:text-white focus:shadow-outline focus:outline-none">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="permission-modal">
        <!--Modal-->
        <div class="permui-edit modal opacity-0 pointer-events-none w-full h-full top-0 left-0 flex items-center justify-center">
            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
            @include('components.token-form')
            @include('permissions.edit')
        </div>
        <div>
            @include('components.confirmation-dialog')
        </div>
    </div>

</x-app-layout>
