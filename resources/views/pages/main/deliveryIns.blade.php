<x-app-layout>
    <x-slot name="nav">main</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Surat / Paket Masuk
        </h2>
    </x-slot>

    <main class="px-10 mt-10 relative">
        <x-spinner></x-spinner>

        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-delivery')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Surat / Paket Masuk
        </button>

        <x-modal name="add-delivery" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Surat / Paket Masuk</h5>

                <form class="mt-5" id="addDeliveryForm" onsubmit="handleAddDelivery(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="date" :value="__('Tanggal')" />
                        <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="sender" :value="__('Nama Pengirim')" />
                        <x-text-input id="sender" class="block mt-1 w-full" type="text" name="sender" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="receiver" :value="__('Nama Penerima')" />
                        <x-text-input id="receiver" class="block mt-1 w-full" type="text" name="receiver" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="received_date" :value="__('Tanggal Diterima')" />
                        <x-text-input id="received_date" class="block mt-1 w-full" type="date" name="received_date"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="received_by" :value="__('Diterima Oleh')" />
                        <x-text-input id="received_by" class="block mt-1 w-full" type="text" name="received_by"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="photo" :value="__('Unggah Foto')" />
                        <label for="photo" id="photoLabel"
                            class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                            <img class="w-6"
                                src="https://img.icons8.com/?size=100&id=ubpIBvM5kGB0&format=png&color=000000"
                                alt="">
                            <span id="text-photo">Pilih Foto</span>
                        </label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden"
                            onchange="showFileName(event, 'photoLabel')" />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Surat / Paket Masuk
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
                        <th>Tanggal Masuk</th>
                        <th>Nama Pengirim</th>
                        <th>Nama Penerima</th>
                        <th>Tanggal Diterima</th>
                        <th>Diterima Oleh</th>
                        <th>Foto</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-delivery" :show="false">
        <div class="p-5" x-data="{
            delivery: null,
            setDelivery(data) {
                this.delivery = data;
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
                    this.delivery = null;
        
                    $('#edit_invoice_date_wrapper').addClass('hidden');
                    $('#edit_payment_date_wrapper').addClass('hidden');
                }, 200);
            }
        }" @set-delivery.window="setDelivery($event.detail)"
            @click.outside="resetForm()">
            <h5 class="font-semibold text-md">Edit Penggunaan</h5>

            <form class="mt-5" id="editDeliveryForm" onsubmit="handleEditDelivery(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="delivery_id" x-bind:value="delivery?.id">

                <div class="mb-4">
                    <x-input-label for="edit_date" :value="__('Tanggal')" />
                    <x-text-input id="edit_date" class="block mt-1 w-full" type="date" name="date" required
                        x-bind:value="delivery?.date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_sender" :value="__('Nama Pengirim')" />
                    <x-text-input id="edit_sender" class="block mt-1 w-full" type="text" name="sender" required
                        x-bind:value="delivery?.sender" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_receiver" :value="__('Nama Penerima')" />
                    <x-text-input id="edit_receiver" class="block mt-1 w-full" type="text" name="receiver" required
                        x-bind:value="delivery?.receiver" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_received_date" :value="__('Tanggal Diterima')" />
                    <x-text-input id="edit_received_date" class="block mt-1 w-full" type="date"
                        name="received_date" required x-bind:value="delivery?.received_date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_received_by" :value="__('Diterima Oleh')" />
                    <x-text-input id="edit_received_by" class="block mt-1 w-full" type="text" name="received_by"
                        required x-bind:value="delivery?.received_by" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_photo" :value="__('Unggah Foto')" />
                    <label for="edit_photo" id="edit_photoLabel"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-6"
                            src="https://img.icons8.com/?size=100&id=ubpIBvM5kGB0&format=png&color=000000"
                            alt="">
                        <span id="text-photo">Pilih Foto</span>
                    </label>
                    <input type="file" id="edit_photo" name="photo" accept="image/*" class="hidden"
                        onchange="showFileName(event, 'edit_photoLabel')" />
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

    <x-modal name="delete-delivery" :show="false">
        <div class="p-5" x-data="{
            delivery: null,
            setDelivery(data) {
                this.delivery = data;
            }
        }" @set-delivery.window="setDelivery($event.detail)">
            <h5 class="font-semibold text-md">Hapus Penggunaan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus Penggunaan <span class="font-bold text-red-600"
                    x-text="delivery?.name"></span>?</p>

            <form class="mt-5" id="deleteDeliveryForm" onsubmit="handleDeleteDelivery(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="delivery_id" x-bind:value="delivery?.id">

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
                        url: "{{ route('deliveryIns.data') }}",
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
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'sender',
                            name: 'sender'
                        },
                        {
                            data: 'receiver',
                            name: 'receiver'
                        },
                        {
                            data: 'received_date',
                            name: 'received_date'
                        },
                        {
                            data: 'received_by',
                            name: 'received_by'
                        },
                        {
                            data: 'photo',
                            name: 'photo'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: [0, 1, 4],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 7,
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

        function handleAddDelivery(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Surat / Paket Masuk', 'add');

            $.ajax({
                url: "{{ route('delivery-ins.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-delivery'
                    }));

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
                        title: 'Terjadi kesalahan saat menambahkan data'
                    });

                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Surat / Paket Masuk', 'add');
                    }, 500);
                }
            });
        }

        function handleEditDelivery(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PenggunaanId = formData.get('delivery_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/delivery-ins/${PenggunaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-delivery'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Data berhasil diubah'
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

        function handleDeleteDelivery(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PenggunaanId = formData.get('delivery_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Surat / Paket Masuk', 'delete');

            $.ajax({
                url: `/delivery-ins/${PenggunaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-delivery'
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
                        setLoading(submitButton, false, 'Hapus Surat / Paket Masuk', 'delete');
                    }, 500);
                }
            });
        }

        function showFileName(event, elementId) {
            const input = event.target;
            const label = document.getElementById(elementId);
            const span = label.querySelector('span');

            console.log(elementId);

            if (input.files.length > 0) {
                span.textContent = input.files[0].name;
            } else {
                span.textContent = elementId === 'calibrationLabel' ? 'Pilih Dokumen' : 'Pilih Foto';
            }
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
