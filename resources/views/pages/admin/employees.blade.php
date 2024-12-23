<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pegawai
        </h2>
    </x-slot>

    <main class="px-10 mt-10">

        <div class="bg-white shadow-sm rounded-md p-5">
            <div class="w-full flex justify-between">
                <h5 class="font-semibold text-xl">Data Kepala Cabang</h5>
                <img id="toggle-head-office" class="w-8 rotate-180 cursor-pointer"
                    src="https://img.icons8.com/?size=100&id=85123&format=png&color=737373" alt="chevron-down" />
            </div>

            <form class="mt-5" id="addHeadOfficeForm" onsubmit="handleAddHeadOffice(event)">
                @csrf

                <div class="mb-4">
                    <x-input-label for="npp" :value="__('Nomor Pokok Pegawai (NPP)')" />
                    <x-text-input id="npp" class="block mt-1 w-full" type="text" name="npp" required value="{{ $headOffice->npp ?? '' }}"
                         />
                </div>
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nama Kepala Cabang')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                     value="{{ $headOffice->name ?? '' }}" />
                </div>

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <hr class="w-full border border-gray-200 my-10">

        <h5 class="font-semibold text-xl">Daftar Pegawai</h5>

        <button class="mt-5 flex w-full justify-center md:w-max md:justify-normal items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-employee')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Pegawai
        </button>

        <x-modal name="add-employee" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Pegawai</h5>

                <form class="mt-5" id="addEmployeeForm" onsubmit="handleAddEmployee(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="npp" :value="__('Nomor Pokok Pegawai (NPP)')" />
                        <x-text-input id="npp" class="block mt-1 w-full" type="text" name="npp" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Pegawai')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Posisi')" />
                        <select class="w-full select2" name="position_id">
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Pegawai
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
                        <th>NPP</th>
                        <th>Nama Pegawai</th>
                        <th>Posisi</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-employee" :show="false">
        <div class="p-5" x-data="{
            employee: null,
            setEmployee(data) {
                this.employee = data;
            }
        }" @set-employee.window="setEmployee($event.detail)">
            <h5 class="font-semibold text-md">Edit Pegawai</h5>

            <form class="mt-5" id="editEmployeeForm" onsubmit="handleEditEmployee(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="employee_id" x-bind:value="employee?.id">
                <div class="mb-4">
                    <x-input-label for="edit_npp" :value="__('Nomor Pokok Pegawai (NPP)')" />
                    <x-text-input id="edit_npp" class="block mt-1 w-full" type="text" name="npp" required
                        x-bind:value="employee?.npp" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Pegawai')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="employee?.name" autofocus />
                </div>

                <div class="mb-4 w-full">
                    <x-input-label for="name" :value="__('Posisi')" />
                    <select class="w-full select2" name="position_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2(); // Inisialisasi Select2
                        if (employee) {
                            $('.select2').val(employee.position_id).trigger('change');
                        }
                    })"
                        @set-employee.window="$nextTick(() => {
                                $('.select2').val($event.detail.position_id).trigger('change');
                            })">
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}"
                                x-bind:selected="employee?.position_id == {{ $position->id }}">
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
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

    <x-modal name="delete-employee" :show="false">
        <div class="p-5" x-data="{
            employee: null,
            setEmployee(data) {
                this.employee = data;
            }
        }" @set-employee.window="setEmployee($event.detail)">
            <h5 class="font-semibold text-md">Hapus Pegawai</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus pegawai <span class="font-bold text-red-600"
                    x-text="employee?.name"></span>?</p>

            <form class="mt-5" id="deleteEmployeeForm" onsubmit="handleDeleteEmployee(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="employee_id" x-bind:value="employee?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Pegawai
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
                    ajax: "{{ route('employees.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'npp',
                            name: 'npp',
                            orderable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'position',
                            name: 'position',
                            orderable: false
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

        function handleAddHeadOffice(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan', 'add');

            $.ajax({
                url: "{{ route('employees.store.headOffice') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Data berhasil ditambahkan'
                        });
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menyimpan data'
                    });

                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Simpan', 'add');
                    }, 500);
                }
            });
        }

        function handleAddEmployee(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Pegawai', 'add');

            $.ajax({
                url: "{{ route('employees.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-employee'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Pegawai berhasil ditambahkan'
                        });
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menambahkan data'
                    });

                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Pegawai', 'add');
                    }, 500);
                }
            });
        }

        function handleEditEmployee(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const pegawaiId = formData.get('employee_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/employees/${pegawaiId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-employee'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Data berhasil diubah'
                    });
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

        function handleDeleteEmployee(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const pegawaiId = formData.get('employee_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Pegawai', 'delete');

            $.ajax({
                url: `/admin/employees/${pegawaiId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-employee'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Pegawai berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Pegawai', 'delete');
                    }, 500);
                }
            });
        }

        document.getElementById('toggle-head-office').addEventListener('click', function() {
            const form = document.getElementById('addHeadOfficeForm');
            form.classList.toggle('hidden');
            this.classList.toggle('rotate-180');
        });
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

</x-app-layout>
