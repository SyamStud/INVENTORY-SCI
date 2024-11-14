<x-app-layout>
    <x-slot name="nav">super-admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Admin Kantor Cabang
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-admin')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Admin Kantor Cabang
        </button>

        <x-modal name="add-admin" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Admin Kantor Cabang</h5>

                <form class="mt-5" id="addAdminForm" onsubmit="handleAddAdmin(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autofocus />
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="admin_id" :value="__('Kantor Cabang')" />
                        <select class="w-full select2" name="admin_id" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Admin Kantor Cabang
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
                        <th>Email</th>
                        <th>Kantor Cabang</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-admin" :show="false">
        <div class="p-5" x-data="{
            admin: null,
            setAdmin(data) {
                this.admin = data;
            }
        }" @set-admin.window="setAdmin($event.detail)">
            <h5 class="font-semibold text-md">Edit Satuan Admin</h5>

            <form class="mt-5" id="editAdminForm" onsubmit="handleEditAdmin(event)">
                @method('PUT')
                @csrf

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                        autofocus x-bind:value="admin?.name" />
                </div>

                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required
                        autofocus x-bind:value="admin?.email" />
                </div>

                <div class="mb-4 w-full">
                    <x-input-label for="branch" :value="__('Kantor Cabang')" />
                    <select class="w-full select2" name="branch_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2(); // Inisialisasi Select2
                        if (admin) {
                            $('.select2').val(admin.branch_id).trigger('change');
                        }
                    })"
                        @set-admin.window="$nextTick(() => {
                                $('.select2').val($event.detail.branch_id).trigger('change');
                            })">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                x-bind:selected="admin?.branch_id == {{ $branch->id }}">
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="admin_id" x-bind:value="admin?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-[#C07F00] rounded-md text-white font-medium text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-admin" :show="false">
        <div class="p-5" x-data="{
            admin: null,
            setAdmin(data) {
                this.admin = data;
            }
        }" @set-admin.window="setAdmin($event.detail)">
            <h5 class="font-semibold text-md">Hapus Satuan Admin</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus satuan admin <span class="font-bold text-red-600"
                    x-text="admin?.name"></span>?</p>

            <form class="mt-5" id="deleteAdminForm" onsubmit="handleDeleteAdmin(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="admin_id" x-bind:value="admin?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Satuan
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
                    ajax: "{{ route('admins.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'branch',
                            name: 'branch'
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

        function handleAddAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Admin Kantor Cabang', 'add');

            $.ajax({
                url: "{{ route('admins.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-admin'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Admin Kantor cabang berhasil ditambahkan'
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Admin Kantor Cabang', 'add');
                    }, 500);
                }
            });
        }

        function handleEditAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const adminId = formData.get('admin_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');
            console.log(adminId);

            $.ajax({
                url: `/super-admin/admins/${adminId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        dataTable.ajax.reload();
                        dispatchEvent(new CustomEvent('close-modal', {
                            detail: 'edit-admin'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: 'Admin Kantor cabang berhasil diubah'
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

        function handleDeleteAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const adminId = formData.get('admin_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Admin Kantor Cabang', 'delete');

            console.log(adminId);

            $.ajax({
                url: `/super-admin/admins/${adminId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-admin'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Admin Kantor cabang berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Admin Kantor Cabang', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
