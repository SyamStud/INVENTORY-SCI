<x-app-layout>
    <x-slot name="nav">main</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Perizinan
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-permit')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Perizinan
        </button>

        <x-modal name="add-permit" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Perizinan</h5>

                <form class="mt-5" id="addPermitForm" onsubmit="handleAddPermit(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Dokumen')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="number" :value="__('Nomor Dokumen')" />
                        <x-text-input id="number" class="block mt-1 w-full" type="text" name="number" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="institution" :value="__('Nama Instansi')" />
                        <x-text-input id="institution" class="block mt-1 w-full" type="text" name="institution"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="due_date" :value="__('Masa Akhir Berlaku')" />
                        <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="file" :value="__('Unggah Dokumen Perizinan')" />
                        <label for="file" id="fileLabel"
                            class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                            <img class="w-5"
                                src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                                alt="">
                            <span id="text-document">Pilih Dokumen</span>
                        </label>
                        <input type="file" id="file" name="file" accept=".pdf" class="hidden"
                            onchange="showFileName(event, 'fileLabel')" />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Perizinan
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
                        <th>Nama Dokumen</th>
                        <th>Nomor Dokumen</th>
                        <th>Nama Instansi</th>
                        <th>Masa Akhir Berlaku</th>
                        <th>Dokumen</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-permit" :show="false">
        <div class="p-5" x-data="{
            permit: null,
            setPermit(data) {
                this.permit = data;
            }
        }" @set-permit.window="setPermit($event.detail)">
            <h5 class="font-semibold text-md">Edit Perizinan</h5>

            <form class="mt-5" id="editPermitForm" onsubmit="handleEditPermit(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="permit_id" x-bind:value="permit?.id">
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Perizinan')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="permit?.name" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_number" :value="__('Nomor Dokumen')" />
                    <x-text-input id="edit_number" class="block mt-1 w-full" type="text" name="number" required
                        x-bind:value="permit?.number" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_institution" :value="__('Nama Instansi')" />
                    <x-text-input id="edit_institution" class="block mt-1 w-full" type="text" name="institution"
                        required x-bind:value="permit?.institution" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_due_date" :value="__('Masa Akhir Berlaku')" />
                    <x-text-input id="edit_due_date" class="block mt-1 w-full" type="date" name="due_date" required
                        x-bind:value="permit?.due_date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_file" :value="__('Unggah Dokumen Perizinan')" />
                    <label for="edit_file" id="edit_fileLabel"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-5"
                            src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                            alt="">
                        <span id="text-document">Pilih Dokumen</span>
                    </label>
                    <input type="file" id="edit_file" name="file" accept=".pdf" class="hidden"
                        onchange="showFileName(event, 'edit_fileLabel')" />
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

    <x-modal name="delete-permit" :show="false">
        <div class="p-5" x-data="{
            permit: null,
            setPermit(data) {
                this.permit = data;
            }
        }" @set-permit.window="setPermit($event.detail)">
            <h5 class="font-semibold text-md">Hapus Perizinan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus Perizinan <span class="font-bold text-red-600"
                    x-text="permit?.name"></span>?</p>

            <form class="mt-5" id="deletePermitForm" onsubmit="handleDeletePermit(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="permit_id" x-bind:value="permit?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Perizinan
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
                    ajax: "{{ route('permits.data') }}",
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
                            data: 'number',
                            name: 'number'
                        },
                        {
                            data: 'institution',
                            name: 'institution',
                        },
                        {
                            data: 'due_date',
                            name: 'due_date',
                        },
                        {
                            data: 'file',
                            name: 'file',
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
                            targets: 6,
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

        function handleAddPermit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Perizinan', 'add');

            $.ajax({
                url: "{{ route('permits.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-permit'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Perizinan berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Perizinan', 'add');
                    }, 500);
                }
            });
        }

        function handleEditPermit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PerizinanId = formData.get('permit_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/permits/${PerizinanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-permit'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Perizinan berhasil diubah'
                    });

                    form.reset();
                    $('#edit_fileLabel span').text('Pilih Dokumen');
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Simpan Perubahan', 'edit');
                    }, 500);
                }
            });
        }

        function handleDeletePermit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PerizinanId = formData.get('permit_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Perizinan', 'delete');

            $.ajax({
                url: `/permits/${PerizinanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-permit'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Perizinan berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Perizinan', 'delete');
                    }, 500);
                }
            });
        }

        function showFileName(event, elementId) {
            const input = event.target;
            const label = document.getElementById(elementId);
            const span = label.querySelector('span');

            if (input.files.length > 0) {
                span.textContent = input.files[0].name;
            } else {
                span.textContent = elementId === 'calibrationLabel' ? 'Pilih Dokumen' : 'Pilih Foto';
            }
        }
    </script>

</x-app-layout>
