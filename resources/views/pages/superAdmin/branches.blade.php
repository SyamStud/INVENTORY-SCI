<x-app-layout>
    <x-slot name="nav">super-admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Kantor Cabang
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-branch')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Kantor Cabang
        </button>

        <x-modal name="add-branch" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Kantor Cabang</h5>

                <form class="mt-5" id="addBranchForm" onsubmit="handleAddBranch(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Daerah Administrasi Cabang')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus placeholder="Contoh: Cilacap, Semarang, Cirebon" />
                        <p class="text-sm text-gray-400 mt-1">Nama akan digunakan untuk administrasi seperti
                            kop surat, dll.</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="code" :value="__('Kode Kantor Cabang')" />
                        <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required
                            autofocus />
                        <p class="text-sm text-gray-400 mt-1">Kode akan digunakan untuk nomor surat, dll.</p>
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="province_id" :value="__('Provinsi')" />
                        <select class="w-full select2" name="province_id" id="province_id" x-data
                            x-init="$nextTick(() => {
                                $('.select2').select2(); // Inisialisasi Select2
                            })">
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 w-full regency-wrapper" style="display: none;">
                        <x-input-label for="regency_id" :value="__('Kabupaten/Kota')" />
                        <select class="w-full select2" name="regency_id" id="regency_id" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                    </div>

                    <div class="mb-4 w-full district-wrapper" style="display: none;">
                        <x-input-label for="district_id" :value="__('Kecamatan')" />
                        <select class="w-full select2" name="district_id" id="district_id" x-data
                            x-init="$nextTick(() => {
                                $('.select2').select2(); // Inisialisasi Select2
                            })">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>

                    <div class="mb-4 w-full village-wrapper" style="display: none;">
                        <x-input-label for="village_id" :value="__('Desa/Kelurahan')" />
                        <select class="w-full select2" name="village_id" id="village_id" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Kantor Cabang
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
                        <th>Daerah Administrasi</th>
                        <th>Kode Cabang</th>
                        <th>Provinsi</th>
                        <th>Kabupaten/Kota</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan/Desa</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-branch" :show="false">
        <div class="p-5" x-data="{
            branch: null,
            setBranch(data) {
                this.branch = data;
            }
        }" @set-branch.window="setBranch($event.detail)">
            <h5 class="font-semibold text-md">Edit Satuan Branch</h5>

            <form class="mt-5" id="editBranchForm" onsubmit="handleEditBranch(event)">
                @method('PUT')
                @csrf

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nama Daerah Administrasi Cabang')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                        autofocus placeholder="Contoh: Cilacap, Semarang, Cirebon" x-bind:value="branch?.name"/>
                    <p class="text-sm text-gray-400 mt-1">Nama akan digunakan untuk administrasi seperti
                        kop surat, dll.</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="code" :value="__('Kode Kantor Cabang')" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required
                        autofocus x-bind:value="branch?.code"/>
                    <p class="text-sm text-gray-400 mt-1">Kode akan digunakan untuk nomor surat, dll.</p>
                </div>

                <input type="hidden" name="branch_id" x-bind:value="branch?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-[#C07F00] rounded-md text-white font-medium text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-branch" :show="false">
        <div class="p-5" x-data="{
            branch: null,
            setBranch(data) {
                this.branch = data;
            }
        }" @set-branch.window="setBranch($event.detail)">
            <h5 class="font-semibold text-md">Hapus Satuan Branch</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus satuan branch <span class="font-bold text-red-600"
                    x-text="branch?.name"></span>?</p>

            <form class="mt-5" id="deleteBranchForm" onsubmit="handleDeleteBranch(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="branch_id" x-bind:value="branch?.id">

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
        $(document).ready(function() {
            $('#province_id').on('change', function() {
                var provinceId = $('#province_id').val();
                console.log(provinceId);

                // Reset dan sembunyikan dropdown dibawahnya
                $('#regency_id').empty().append('<option value="">Pilih Kabupaten/Kota</option>');
                $('#district_id').empty().append('<option value="">Pilih Kecamatan</option>');
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                $('.district-wrapper, .village-wrapper').hide();

                if (provinceId) {
                    $('.regency-wrapper').show();
                    $.ajax({
                        url: '/super-admin/get-regencies',
                        type: 'POST',
                        data: {
                            province_id: provinceId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            console.log(data);
                            $.each(data, function(key, value) {
                                $('#regency_id').append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            $('#regency_id').select2('destroy').select2();
                        }
                    });
                } else {
                    $('.regency-wrapper').hide();
                }
            });

            // Event handler untuk Regency
            $('#regency_id').on('change', function() {
                var regencyId = $(this).val();

                // Reset dan sembunyikan dropdown dibawahnya
                $('#district_id').empty().append('<option value="">Pilih Kecamatan</option>');
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                $('.village-wrapper').hide();

                if (regencyId) {
                    $('.district-wrapper').show();
                    $.ajax({
                        url: '/super-admin/get-districts',
                        type: 'POST',
                        data: {
                            regency_id: regencyId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            console.log(data);
                            $.each(data, function(key, value) {
                                $('#district_id').append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            $('#district_id').select2('destroy').select2();
                        }
                    });
                } else {
                    $('.district-wrapper').hide();
                }
            });

            // Event handler untuk District
            $('#district_id').on('change', function() {
                var districtId = $(this).val();

                // Reset dropdown desa
                $('#village_id').empty().append('<option value="">Pilih Desa/Kelurahan</option>');

                if (districtId) {
                    $('.village-wrapper').show();
                    $.ajax({
                        url: '/super-admin/get-villages',
                        type: 'POST',
                        data: {
                            district_id: districtId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#village_id').append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                            $('#village_id').select2('destroy').select2();
                        }
                    });
                } else {
                    $('.village-wrapper').hide();
                }
            });
        });
    </script>

    <script>
        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('branches.data') }}",
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'province',
                            name: 'province'
                        },
                        {
                            data: 'regency',
                            name: 'regency'
                        },
                        {
                            data: 'district',
                            name: 'district'
                        },
                        {
                            data: 'village',
                            name: 'village'
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

        function handleAddBranch(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Kantor Cabang', 'add');

            $.ajax({
                url: "{{ route('branches.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-branch'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Kantor cabang berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Kantor Cabang', 'add');
                    }, 500);
                }
            });
        }

        function handleEditBranch(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const branchId = formData.get('branch_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');
            console.log(branchId);

            $.ajax({
                url: `/super-admin/branches/${branchId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-branch'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Kantor cabang berhasil diubah'
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

        function handleDeleteBranch(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const branchId = formData.get('branch_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Kantor Cabang', 'delete');

            console.log(branchId);

            $.ajax({
                url: `/super-admin/branches/${branchId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-branch'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Kantor cabang berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Kantor Cabang', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
