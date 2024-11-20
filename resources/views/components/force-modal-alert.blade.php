{{-- resources/views/components/force-modal-alert.blade.php --}}
@props(['show' => false, 'redirectUrl'])

<div x-data="{
    show: @js($show),
    async checkEmployee() {
        try {
            const response = await fetch('/admin/employees/isExist', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            console.log('Response:', response);
            this.show = response.status !== 200;
        } catch (error) {
            console.error('Error checking employee existence:', error);
            this.show = true; // Show modal on error as a precaution
        }
    }
}" x-init="checkEmployee()" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">

    {{-- Non-clickable backdrop --}}
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    {{-- Modal Content --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Peringatan!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Anda belum mengatur data pegawai. Silakan atur data pegawai terlebih dahulu sebelum
                                melanjutkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <a href="{{ $redirectUrl }}"
                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                    Atur Pegawai Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
