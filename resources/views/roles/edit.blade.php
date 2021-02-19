<div class="" style="width: 90%;">
    <main class="container mx-auto max-w-screen-lg h-full">
        <!-- role edit modal -->
        <article id="editor" aria-label="Role Edit Modal" class="relative h-full flex flex-col bg-white shadow-xl rounded-md" >

            <!-- scroll area -->
            <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                <form id="role_edit_form" action="{!! $action !!}">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="form-group font-bold">
                            <div>Role Name:</div>
                            {{ Form::hidden('id', 0) }}
                            {{ Form::text('name', null, ['class' => 'form-control']) }}
                            <div id="name_caution" class="text-red-600 font-normal hidden">
                                CAUTION: Changing the <b>Role Name</b><br>may affect existing permissions.
                            </div>
                        </div>
                        <div class="form-group font-bold" id="permissions_for_role">
                            Permissions:
                            <div class="border-solid border-gray-300 border-2 overflow-scroll">
                                <ul id="role_permissions" class="p-4">
                                    @foreach($permissions as $permission)
                                        <li><input data-type="role_permission" type="checkbox" name="role_permission[]" value="{!! $permission->name !!}"> {!! $permission->name !!}</li>
                                    @endforeach
                                    <!-- We need this bogus value in order to remove the last item when calling PermissionsAssociationService -->
                                    <input type="hidden" name="role_permission[]" value="false">
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <!-- sticky footer -->
            <footer class="flex justify-end px-8 pb-8 pt-4">
                <div id="role_error" class="px-3 text-red-600 opacity-0">Error message</div>
                <button id="update_role" class="rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 disabled:opacity-50 text-white focus:shadow-outline focus:outline-none">
                    Update
                </button>
                <button id="save_role" class="rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 text-white focus:shadow-outline focus:outline-none">
                    Save
                </button>
                <button id="cancel" class="modal-close ml-3 rounded px-3 py-1 bg-gray-300 hover:bg-gray-200 focus:shadow-outline focus:outline-none">
                    Cancel
                </button>
            </footer>
        </article>
    </main>
</div>

