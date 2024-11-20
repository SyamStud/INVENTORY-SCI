<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Barang
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex w-full justify-center md:w-max md:justify-normal items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-item')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Barang
        </button>

        <x-modal name="add-item" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Barang</h5>

                <form class="mt-5" id="addItemForm" onsubmit="handleAddItem(event)" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Barang')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Satuan Unit')" />
                        <select class="w-full select2" name="unit_id">
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Barang
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
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Unit</th>
                        <th>Stok</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-item" :show="false">
        <div class="p-5" x-data="{
            item: null,
            setItem(data) {
                this.item = data;
            }
        }" @set-item.window="setItem($event.detail)">
            <h5 class="font-semibold text-md">Edit Barang</h5>

            <form class="mt-5" id="editItemForm" onsubmit="handleEditItem(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="item_id" x-bind:value="item?.id">
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Barang')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="item?.name" autofocus />
                </div>

                {{-- <div class="mb-4">
                    <x-input-label for="edit_price" :value="__('Harga')" />
                    <x-text-input id="edit_price" class="block mt-1 w-full" type="text" name="price" required
                        x-bind:value="item?.price" autofocus />
                </div> --}}

                <div class="mb-4 w-full">
                    <x-input-label for="name" :value="__('Satuan Unit')" />
                    <select class="w-full select2" name="unit_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2(); // Inisialisasi Select2
                        if (item) {
                            $('.select2').val(item.unit_id).trigger('change');
                        }
                    })"
                        @set-asset.window="$nextTick(() => {
                                $('.select2').val($event.detail.item_id).trigger('change');
                            })">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" x-bind:selected="item?.unit_id == {{ $unit->id }}">
                                {{ $unit->name }}
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

    <x-modal name="delete-item" :show="false">
        <div class="p-5" x-data="{
            item: null,
            setItem(data) {
                this.item = data;
            }
        }" @set-item.window="setItem($event.detail)">
            <h5 class="font-semibold text-md">Hapus Barang</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus barang <span class="font-bold text-red-600"
                    x-text="item?.name"></span>?</p>

            <form class="mt-5" id="deleteItemForm" onsubmit="handleDeleteItem(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="item_id" x-bind:value="item?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Barang
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
                    ajax: "{{ route('items.data') }}",
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
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'unit',
                            name: 'unit',
                            orderable: false
                        },
                        {
                            data: 'stock',
                            name: 'stock',
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
                            targets: [0, 2, 3, 4],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 5,
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

        function handleAddItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Barang', 'add');

            $.ajax({
                url: "{{ route('items.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-item'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Barang berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Barang', 'add');
                    }, 500);
                }
            });
        }

        function handleEditItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const itemId = formData.get('item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/items/${itemId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-item'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Data berhasil diubah'
                    });

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

        function handleDeleteItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const itemId = formData.get('item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Barang', 'delete');

            $.ajax({
                url: `/admin/items/${itemId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-item'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Barang berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Barang', 'delete');
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
