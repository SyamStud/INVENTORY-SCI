<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengaturan Hak Akses Pengguna
        </h2>
    </x-slot>

    <main class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('permissions.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- User Selection -->
                        <div class="mb-6">
                            <x-input-label for="user_id" :value="__('Nama Pengguna')"
                                class="text-sm font-medium text-gray-700 mb-2" />
                            <div class="relative">
                                <select
                                    class="w-full select2 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    name="user_id" id="user_id">
                                    <option value="">Pilih Pengguna</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Permissions Table -->
                        <div class="mt-6">
                            <div class="overflow-x-auto rounded-lg shadow">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-800">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                                Hak Akses
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                                Keterangan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($permissions as $permission)
                                            <tr class="hover:bg-gray-50">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ Str::title(str_replace('-', ' ', $permission->name)) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            id="permission_{{ $permission->id }}"
                                                            class="permission-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <span class="ml-2">Aktif</span>
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 w-full flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with custom styling
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: 'Pilih Pengguna',
            });

            // Handle user selection change
            $('#user_id').on('change', function() {
                const userId = $(this).val();
                if (userId) {
                    // Reset checkboxes
                    $('.permission-checkbox').prop('checked', false);

                    // Fetch user permissions
                    $.ajax({
                        url: `/admin/get-user-permissions/${userId}`,
                        method: 'GET',
                        success: function(permissions) {
                            permissions.forEach(function(permission) {
                                $(`input[value="${permission}"]`).prop('checked', true);
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            // Show error toast/notification
                            alert('Terjadi kesalahan saat memuat data permissions');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
