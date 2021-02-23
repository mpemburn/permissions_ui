<div class="w-2/3">
    <main class="container mx-auto max-w-screen-sm h-full">
        <!-- permission edit modal -->
        <article id="editor" aria-label="Permission Edit Modal"
                 class="relative h-full flex flex-col bg-white shadow-xl rounded-md">

            <!-- scroll area -->
            <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                <div
                    class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                         viewBox="0 0 18 18">
                        <path
                            d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
                <form id="permission_edit_form" action="{!! $action !!}">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="form-group font-bold">
                            <div>Permission Name:</div>
                            {{ Form::hidden('id', 0) }}
                            {{ Form::text('name', null, ['class' => 'form-control']) }}
                            <div id="name_caution" class="w-3/4 text-red-600 font-normal hidden">
                                CAUTION: Changing the <b>Permission Name</b> may affect existing permissions.
                            </div>

                        </div>
                    </div>
                </form>
            </section>

            <!-- sticky footer -->
            <footer class="flex justify-end px-8 pb-8 pt-4">
                <div id="permission_error" class="px-3 text-red-600 opacity-0">Error message</div>
                <button id="update_permission"
                        class="rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 disabled:opacity-50 text-white focus:shadow-outline focus:outline-none">
                    Update
                </button>
                <button id="save_permission"
                        class="rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 disabled:opacity-50 text-white focus:shadow-outline focus:outline-none">
                    Save
                </button>
                <button id="cancel"
                        class="modal-close ml-3 rounded px-3 py-1 bg-gray-300 hover:bg-gray-200 focus:shadow-outline focus:outline-none">
                    Cancel
                </button>
            </footer>
        </article>
    </main>
</div>

