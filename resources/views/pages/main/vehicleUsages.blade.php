<x-app-layout>
    <x-slot name="nav">main</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Monitoring Penggunaan Kendaraan
        </h2>
    </x-slot>

    <main class="px-10 mt-10 relative">
        <x-spinner></x-spinner>

        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-usage')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Penggunaan Kendaraan
        </button>

        <x-modal name="add-usage" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Penggunaan kendaraan</h5>

                <form class="mt-5" id="addUsageForm" onsubmit="handleAddUsage(event)">
                    @csrf

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Nomor Polisi')" />
                        <select class="w-full select2" name="vehicle_id">
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->nopol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Nama Pegawai')" />
                        <select class="w-full select2" name="employee_id">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="date" :value="__('Tanggal')" />
                        <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="time_start" :value="__('Jam Awal')" />
                        <x-text-input id="time_start" class="block mt-1 w-full" type="time" name="time_start"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="time_end" :value="__('Jam Akhir')" />
                        <x-text-input id="time_end" class="block mt-1 w-full" type="time" name="time_end" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="purpose" :value="__('Tujuan')" />
                        <x-text-input id="purpose" class="block mt-1 w-full" type="text" name="purpose"
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="driver" :value="__('Nama Driver')" />
                        <x-text-input id="driver" class="block mt-1 w-full" type="text" name="driver"
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Penggunaan Kendaraan
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
                        <th>Nomor Polisi</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Jam Awal</th>
                        <th>Jam Akhir</th>
                        <th>Tujuan</th>
                        <th>Nama Driver</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-usage" :show="false">
        <div class="p-5" x-data="{
            usage: null,
            setUsage(data) {
                this.usage = data;
                console.log('open');
        
                if (data.invoice_status === 'sudah-invoice') {
                    $('#edit_invoice_date_wrapper').removeClass('hidden');
                }
        
                if (data.payment_status === 'sudah-dibayar') {
                    $('#edit_payment_date_wrapper').removeClass('hidden');
                }
            },
            resetForm() {
                setTimeout(() => {
                    this.usage = null;
        
                    $('#edit_invoice_date_wrapper').addClass('hidden');
                    $('#edit_payment_date_wrapper').addClass('hidden');
                }, 200);
            }
        }" @set-usage.window="setUsage($event.detail)"
            @click.outside="resetForm()">
            <h5 class="font-semibold text-md">Edit Penggunaan</h5>

            <form class="mt-5" id="editUsageForm" onsubmit="handleEditUsage(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="usage_id" x-bind:value="usage?.id">

                <div class="mb-4 w-full">
                    <x-input-label for="edit_name" :value="__('Nomor Polisi')" />
                    <select class="w-full select2" name="vehicle_id" x-bind:value="usage?.vehicle_id">
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->nopol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 w-full">
                    <x-input-label for="edit_name" :value="__('Nama Pegawai')" />
                    <select class="w-full select2" name="employee_id" x-bind:value="usage?.employee_id">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_date" :value="__('Tanggal')" />
                    <x-text-input id="edit_date" class="block mt-1 w-full" type="date" name="date" required
                        x-bind:value="usage?.date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_time_start" :value="__('Jam Awal')" />
                    <x-text-input id="edit_time_start" class="block mt-1 w-full" type="time" name="time_start"
                        required x-bind:value="usage?.time_start" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_time_end" :value="__('Jam Akhir')" />
                    <x-text-input id="edit_time_end" class="block mt-1 w-full" type="time" name="time_end"
                        required x-bind:value="usage?.time_end" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_purpose" :value="__('Tujuan')" />
                    <x-text-input id="edit_purpose" class="block mt-1 w-full" type="text" name="purpose"
                        x-bind:value="usage?.purpose" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_driver" :value="__('Nama Driver')" />
                    <x-text-input id="edit_driver" class="block mt-1 w-full" type="text" name="driver"
                        x-bind:value="usage?.driver" autofocus />
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

    <x-modal name="delete-usage" :show="false">
        <div class="p-5" x-data="{
            usage: null,
            setUsage(data) {
                this.usage = data;
            }
        }" @set-usage.window="setUsage($event.detail)">
            <h5 class="font-semibold text-md">Hapus Penggunaan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus Penggunaan <span class="font-bold text-red-600"
                    x-text="usage?.name"></span>?</p>

            <form class="mt-5" id="deleteUsageForm" onsubmit="handleDeleteUsage(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="usage_id" x-bind:value="usage?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Penggunaan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        let dataTable;
        let isInitialLoad = true;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('vehicleUsages.data') }}",
                        beforeSend: function() {
                            // Tampilkan spinner hanya saat initial load
                            if (isInitialLoad) {
                                $('#loading-spinner').show();
                            }
                        },
                        complete: function() {
                            // Sembunyikan spinner
                            $('#loading-spinner').hide();
                            // Set isInitialLoad menjadi false setelah load pertama
                            isInitialLoad = false;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'vehicle_id',
                            name: 'vehicle_id'
                        },
                        {
                            data: 'employee',
                            name: 'employee',
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'time_start',
                            name: 'time_start',
                        },
                        {
                            data: 'time_end',
                            name: 'time_end',
                        },
                        {
                            data: 'purpose',
                            name: 'purpose',
                        },
                        {
                            data: 'driver',
                            name: 'driver',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: [0, 3, 4, 5],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 8,
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

        function handleAddUsage(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Penggunaan', 'add');

            $.ajax({
                url: "{{ route('vehicle-usages.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-usage'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Penggunaan berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Penggunaan', 'add');
                    }, 500);
                }
            });
        }

        function handleEditUsage(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PenggunaanId = formData.get('usage_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/vehicle-usages/${PenggunaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-usage'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Penggunaan berhasil diubah'
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

        function handleDeleteUsage(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PenggunaanId = formData.get('usage_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Penggunaan', 'delete');

            $.ajax({
                url: `/vehicle-usages/${PenggunaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-usage'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Penggunaan berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Penggunaan', 'delete');
                    }, 500);
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

</x-app-layout>
