<!--Confirmation-->
<main class="confirm opacity-0 pointer-events-none w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="confirm-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="confirm-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">

        <div class="confirm-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
            <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
            </svg>
            <span class="text-sm">(Esc)</span>
        </div>

        <div class="confirm-content py-4 text-left px-6">
            <!--Title-->
            <section class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold">Confirmation</p>
                <div class="confirm-close cursor-pointer z-50">
                    <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </section>

            <!--Body-->
            <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                <div id="confirm_message"></div>
            </section>

            <!--Footer-->
            <footer class="flex justify-end px-8 pb-8 pt-4">
                <button id="confirm" class="rounded px-3 py-1 bg-blue-700 hover:bg-blue-500 text-white focus:shadow-outline focus:outline-none">
                    Confirm
                </button>
                <button id="cancel" class="confirm-close ml-3 rounded px-3 py-1 bg-gray-300 hover:bg-gray-200 focus:shadow-outline focus:outline-none">
                    Cancel
                </button>
            </footer>

        </div>
    </div>
</main>
