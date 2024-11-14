<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Peminjaman
        </h2>
    </x-slot>

    <main class="px-10 mt-10">

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Kepala Bidang</th>
                        <th>Petugas Peminjaman</th>
                        <th>Devisi</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>
    
    <x-modal name="detail-loan" :show="false" >
        <div class="p-5" x-data="{
            asset: null,
            setAsset(data) {
                this.asset = data;
            }
        }" @set-asset.window="setAsset($event.detail)">
            <h5 class="font-semibold text-md">Detail Peminjaman</h5>

            


            <table id="detail-loan" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nama Asset</th>
                        <th>Jumlah</th>
                        <th>Lama Peminjaman</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
            </table>
    </x-modal>

    {{-- <x-modal name="delete-Loan" :show="false">
        <div class="p-5" x-data="{
            Loan: null,
            setLoan(data) {
                this.Loan = data;
            }
        }" @set-Loan.window="setLoan($event.detail)">
            <h5 class="font-semibold text-md">Hapus Peminjaman</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus peminjaman <span class="font-bold text-red-600"
                    x-text="Loan?.name"></span>?</p>

            <form class="mt-5" id="deleteLoanForm" onsubmit="handleDeleteLoan(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="Loan_id" x-bind:value="Loan?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </x-modal> --}}


    <script>
        let dataTable;

        $(function() {
            dataTable = $('#detail-loan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,
                    ajax: "{{ route('loans.assets.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_asset',
                            name: 'nama_asset',
                            orderable: false
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'duration',
                            name: 'duration',
                            orderable: false
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            orderable: false
                        },
                    ],
                    
                }).columns.adjust()
                .responsive.recalc();
        });

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('loans.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'loan_number',
                            name: 'loan_number',
                            orderable: false
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'operation_head',
                            name: 'operation_head',
                            orderable: false
                        },
                        {
                            data: 'loan_officer',
                            name: 'loan_officer',
                            orderable: false
                        },
                        {
                            data: 'general_division',
                            name: 'general_division',
                            orderable: false
                        },
                        {
                            data: 'detail',
                            name: 'detail',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    
                }).columns.adjust()
                .responsive.recalc();
        });

        // function setLoading(button, isLoading, text, type = 'add') {
        //     if (isLoading) {
        //         button.prop('disabled', true);
        //         button.css('background-color', '#9CA3AF');
        //         button.html('Mohon Tunggu...');
        //     } else {
        //         button.prop('disabled', false);
        //         if (type === 'add') {
        //             button.css('background-color', '#15803D');
        //         } else if (type === 'edit') {
        //             button.css('background-color', '#C07F00');
        //         } else if (type === 'delete') {
        //             button.css('background-color', '#EF4444');
        //         }
        //         button.html(text);
        //     }
        // }

        // function handleAddLoan(event) {
        //     event.preventDefault();
        //     const form = event.target;
        //     const formData = new FormData(form);
        //     const submitButton = $(form).find('button[type="submit"]');

        //     setLoading(submitButton, true, 'Tambah Pegawai', 'add');

        //     $.ajax({
        //         url: "{{ route('loans.store') }}",
        //         type: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             dataTable.ajax.reload();
        //             form.reset();
        //             dispatchEvent(new CustomEvent('close-modal', {
        //                 detail: 'add-Loan'
        //             }));

        //             if (response.status == 'error') {
        //                 Toast.fire({
        //                     icon: 'error',
        //                     title: response.message
        //                 });
        //             } else {
        //                 Toast.fire({
        //                     icon: 'success',
        //                     title: 'Pegawai berhasil ditambahkan'
        //                 });
        //             }
        //         },
        //         error: function(xhr) {
        //             Toast.fire({
        //                 icon: 'error',
        //                 title: 'Terjadi kesalahan saat menambahkan data'
        //             });

        //             console.error(xhr);
        //         },
        //         complete: function() {
        //             setTimeout(() => {
        //                 setLoading(submitButton, false, 'Tambah Pegawai', 'add');
        //             }, 500);
        //         }
        //     });
        // }

        // function handleEditLoan(event) {
        //     event.preventDefault();
        //     const form = event.target;
        //     const formData = new FormData(form);
        //     const pegawaiId = formData.get('Loan_id');
        //     const submitButton = $(form).find('button[type="submit"]');

        //     setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

        //     $.ajax({
        //         url: `/admin/loans/${pegawaiId}`,
        //         type: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             dataTable.ajax.reload();
        //             dispatchEvent(new CustomEvent('close-modal', {
        //                 detail: 'edit-Loan'
        //             }));

        //             Toast.fire({
        //                 icon: 'success',
        //                 title: 'Data berhasil diubah'
        //             });
        //         },
        //         error: function(xhr) {
        //             alert('Terjadi kesalahan saat mengubah data');
        //         },
        //         complete: function() {
        //             setTimeout(() => {
        //                 setLoading(submitButton, false, 'Simpan Perubahan', 'edit');
        //             }, 500);
        //         }
        //     });
        // }

        // function handleDeleteLoan(event) {
        //     event.preventDefault();
        //     const form = event.target;
        //     const formData = new FormData(form);
        //     const pegawaiId = formData.get('Loan_id');
        //     const submitButton = $(form).find('button[type="submit"]');

        //     setLoading(submitButton, true, 'Hapus Pegawai', 'delete');

        //     $.ajax({
        //         url: `/admin/loans/${pegawaiId}`,
        //         type: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             dataTable.ajax.reload();
        //             dispatchEvent(new CustomEvent('close-modal', {
        //                 detail: 'delete-Loan'
        //             }));

        //             Toast.fire({
        //                 icon: 'success',
        //                 title: 'Pegawai berhasil dihapus'
        //             });
        //         },
        //         error: function(xhr) {
        //             alert('Terjadi kesalahan saat menghapus data');
        //         },
        //         complete: function() {
        //             setTimeout(() => {
        //                 setLoading(submitButton, false, 'Hapus Pegawai', 'delete');
        //             }, 500);
        //         }
        //     });
        // }
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

</x-app-layout>
