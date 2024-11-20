<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pengguna
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex w-full justify-center md:w-max md:justify-normal items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-user')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Pengguna
        </button>

        <x-modal name="add-user" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Pengguna</h5>

                <form class="mt-5" id="addUserForm" onsubmit="handleAddUser(event)">
                    @csrf

                    <div class="mb-4 w-full">
                        <x-input-label for="employee_id" :value="__('Nama Pengguna')" />
                        <select class="w-full select2" name="employee_id" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nama</th>
                        <th>NPP</th>
                        <th>Email</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-user" :show="false">
        <div class="p-5" x-data="{
            user: null,
            setUser(data) {
                this.user = data;
            }
        }" @set-user.window="setUser($event.detail)">
            <h5 class="font-semibold text-md">Edit Pengguna</h5>

            <form class="mt-5" id="editUserForm" onsubmit="handleEditUser(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="user_id" x-bind:value="user?.id">

                <div class="mb-4 w-full">
                    <x-input-label for="name" :value="__('Nama Pegawai')" />
                    <select class="w-full select2" name="employee_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2(); // Inisialisasi Select2
                        if (employee) {
                            $('.select2').val(employee.employee_id).trigger('change');
                        }
                    })"
                        @set-employee.window="$nextTick(() => {
                                $('.select2').val($event.detail.employee_id).trigger('change');
                            })">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                x-bind:selected="employee?.employee_id == {{ $employee->id }}">
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-4">
                    <x-input-label for="edit_email" :value="__('Email')" />
                    <x-text-input id="edit_email" class="block mt-1 w-full" type="email" name="email" required
                        x-bind:value="user?.email" autofocus />
                </div>

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-[#C07F00] rounded-md text-white font-medium text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-user" :show="false">
        <div class="p-5" x-data="{
            user: null,
            setUser(data) {
                this.user = data;
            }
        }" @set-user.window="setUser($event.detail)">
            <h5 class="font-semibold text-md">Hapus Pengguna</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus pengguna <span class="font-bold text-red-600"
                    x-text="user?.name"></span>?</p>

            <form class="mt-5" id="deleteUserForm" onsubmit="handleDeleteUser(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="user_id" x-bind:value="user?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Pengguna
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('users.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'employee',
                            name: 'employee'
                        },
                        {
                            data: 'npp',
                            name: 'npp'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 4,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center gap-2 w-max');
                            }
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();
        });

        function setLoading(button, isLoading, text, type = 'add') {
            if (isLoading) {
                button.prop('disabled', true);
                button.css('background-color', '#9CA3AF');
                button.html('Mohon Tunggu...');
            } else {
                button.prop('disabled', false);
                if (type === 'add') {
                    button.css('background-color', '#15803D');
                } else if (type === 'edit') {
                    button.css('background-color', '#C07F00');
                } else if (type === 'delete') {
                    button.css('background-color', '#EF4444');
                }
                button.html(text);
            }
        }

        function handleAddUser(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Pengguna', 'add');

            $.ajax({
                url: "{{ route('users.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-user'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Pengguna berhasil ditambahkan'
                        });
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menambahkan data'
                    });
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Pengguna', 'add');
                    }, 500);
                }
            });
        }

        function handleEditUser(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const penggunaId = formData.get('user_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/users/${penggunaId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status = 'success') {
                        dataTable.ajax.reload();
                        dispatchEvent(new CustomEvent('close-modal', {
                            detail: 'edit-user'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat mengubah data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Simpan Perubahan', 'edit');
                    }, 500);
                }
            });
        }

        function handleDeleteUser(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const penggunaId = formData.get('user_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Pengguna', 'delete');

            $.ajax({
                url: `/admin/users/${penggunaId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-user'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Pengguna', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
