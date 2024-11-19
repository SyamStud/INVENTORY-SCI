<x-app-layout>
    <x-slot name="nav">main</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pengadaan
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-procurement')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Pengadaan
        </button>

        <x-modal name="add-procurement" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Pengadaan</h5>

                <form class="mt-5" id="addProcurementForm" onsubmit="handleAddProcurement(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Barang / Jasa')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="user_name" :value="__('Nama User')" />
                        <x-text-input id="user_name" class="block mt-1 w-full" type="text" name="user_name" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="status" :value="__('Status Pengadaan')" />
                        <select name="status"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="proses-anggaran">Proses Anggaran</option>
                            <option value="proses-pengadaan">Proses Pengadaan</option>
                            <option value="penerbitan-po">Penerbitan PO</option>
                            <option value="sudah-diterima">Barang / Jasa Sudah Diterima</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="invoice_status" :value="__('Status Invoice')" />
                        <select id="invoice_status" name="invoice_status"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="belum-invoice">Belum Invoice</option>
                            <option value="sudah-invoice">Sudah Invoice</option>
                        </select>
                    </div>

                    <div id="invoice_date_wrapper" class="mb-4 hidden">
                        <x-input-label for="invoice_date" :value="__('Tanggal Invoice')" />
                        <x-text-input id="invoice_date" class="block mt-1 w-full" type="date" name="invoice_date"
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="payment_status" :value="__('Status Pembayaran')" />
                        <select id="payment_status" name="payment_status"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="belum-dibayar">Belum Dibayar</option>
                            <option value="sudah-dibayar">Sudah Dibayar</option>
                        </select>
                    </div>

                    <div id="payment_date_wrapper" class="mb-4 hidden">
                        <x-input-label for="payment_date" :value="__('Tanggal Pembayaran')" />
                        <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date"
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Pengadaan
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
                        <th>Nama Barang / Jasa</th>
                        <th>Nama User</th>
                        <th>Status Pengadaaan</th>
                        <th>Tanggal Proses</th>
                        <th>Status Invoice</th>
                        <th>Tanggal Invoice</th>
                        <th>Status Pembayaran</th>
                        <th>Tanggal Pembayaran</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-procurement" :show="false">
        <div class="p-5" x-data="{
            procurement: null,
            setProcurement(data) {
                this.procurement = data;
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
                    this.procurement = null;
        
                    $('#edit_invoice_date_wrapper').addClass('hidden');
                    $('#edit_payment_date_wrapper').addClass('hidden');
                }, 200);
            }
        }" @set-procurement.window="setProcurement($event.detail)"
            @click.outside="resetForm()">
            <h5 class="font-semibold text-md">Edit Pengadaan</h5>

            <form class="mt-5" id="editProcurementForm" onsubmit="handleEditProcurement(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="procurement_id" x-bind:value="procurement?.id">
                <!-- Rest of the form fields remain the same -->
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Barang / Jasa')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="procurement?.name" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_user_name" :value="__('Nama User')" />
                    <x-text-input id="edit_user_name" class="block mt-1 w-full" type="text" name="user_name" required
                        x-bind:value="procurement?.user_name" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_status" :value="__('Status Pengadaan')" />
                    <select name="status" x-bind:value="procurement?.status"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                        <option value="proses-anggaran">Proses Anggaran</option>
                        <option value="proses-pengadaan">Proses Pengadaan</option>
                        <option value="penerbitan-po">Penerbitan PO</option>
                        <option value="sudah-diterima">Barang / Jasa Sudah Diterima</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_invoice_status" :value="__('Status Invoice')" />
                    <select id="edit_invoice_status" name="invoice_status" x-bind:value="procurement?.invoice_status"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                        <option value="belum-invoice">Belum Invoice</option>
                        <option value="sudah-invoice">Sudah Invoice</option>
                    </select>
                </div>

                <div id="edit_invoice_date_wrapper" class="mb-4 hidden">
                    <x-input-label for="edit_invoice_date" :value="__('Tanggal Invoice')" />
                    <x-text-input id="edit_invoice_date" class="block mt-1 w-full" type="date"
                        name="invoice_date" x-bind:value="procurement?.invoice_date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_payment_status" :value="__('Status Pembayaran')" />
                    <select id="edit_payment_status" name="payment_status" x-bind:value="procurement?.payment_status"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                        <option value="belum-dibayar">Belum Dibayar</option>
                        <option value="sudah-dibayar">Sudah Dibayar</option>
                    </select>
                </div>

                <div id="edit_payment_date_wrapper" class="mb-4 hidden">
                    <x-input-label for="edit_payment_date" :value="__('Tanggal Pembayaran')" />
                    <x-text-input id="edit_payment_date" class="block mt-1 w-full" type="date"
                        name="payment_date" x-bind:value="procurement?.payment_date" autofocus />
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

    <x-modal name="delete-procurement" :show="false">
        <div class="p-5" x-data="{
            procurement: null,
            setProcurement(data) {
                this.procurement = data;
            }
        }" @set-procurement.window="setProcurement($event.detail)">
            <h5 class="font-semibold text-md">Hapus Pengadaan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus Pengadaan <span class="font-bold text-red-600"
                    x-text="procurement?.name"></span>?</p>

            <form class="mt-5" id="deleteProcurementForm" onsubmit="handleDeleteProcurement(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="procurement_id" x-bind:value="procurement?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Pengadaan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        $('#invoice_status').on('change', function() {
            const invoiceStatus = $(this).val();
            const invoiceDateWrapper = $('#invoice_date_wrapper');

            if (invoiceStatus === 'sudah-invoice') {
                invoiceDateWrapper.removeClass('hidden');
            } else {
                invoiceDateWrapper.addClass('hidden');
            }
        });

        $('#payment_status').on('change', function() {
            const paymentStatus = $(this).val();
            const paymentDateWrapper = $('#payment_date_wrapper');

            if (paymentStatus === 'sudah-dibayar') {
                paymentDateWrapper.removeClass('hidden');
            } else {
                paymentDateWrapper.addClass('hidden');
            }
        });

        $('#edit_invoice_status').on('change', function() {
            const invoiceStatus = $(this).val();
            const invoiceDateWrapper = $('#edit_invoice_date_wrapper');

            if (invoiceStatus === 'sudah-invoice') {
                invoiceDateWrapper.removeClass('hidden');
            } else {
                invoiceDateWrapper.addClass('hidden');
            }
        });

        $('#edit_payment_status').on('change', function() {
            const paymentStatus = $(this).val();
            const paymentDateWrapper = $('#edit_payment_date_wrapper');

            if (paymentStatus === 'sudah-dibayar') {
                paymentDateWrapper.removeClass('hidden');
            } else {
                paymentDateWrapper.addClass('hidden');
            }
        });


        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('procurements.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'entry_date',
                            name: 'entry_date'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'user_name',
                            name: 'user_name',
                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                        {
                            data: 'process_date',
                            name: 'process_date',
                        },
                        {
                            data: 'invoice_status',
                            name: 'invoice_status',
                        },
                        {
                            data: 'invoice_date',
                            name: 'invoice_date',
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status',
                        },
                        {
                            data: 'payment_date',
                            name: 'payment_date',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: [0, 1, 4, 5, 6, 7, 8, 9],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 10,
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

        function handleAddProcurement(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Pengadaan', 'add');

            $.ajax({
                url: "{{ route('procurements.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-procurement'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Pengadaan berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Pengadaan', 'add');
                    }, 500);
                }
            });
        }

        function handleEditProcurement(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PengadaanId = formData.get('procurement_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/procurements/${PengadaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-procurement'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Pengadaan berhasil diubah'
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

        function handleDeleteProcurement(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const PengadaanId = formData.get('procurement_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Pengadaan', 'delete');

            $.ajax({
                url: `/procurements/${PengadaanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-procurement'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Pengadaan berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Pengadaan', 'delete');
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
