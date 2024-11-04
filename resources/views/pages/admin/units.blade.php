<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Satuan Unit
        </h2>
    </x-slot>

    <main class="px-10 mt-10">

        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-unit')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Unit
        </button>

        <x-modal name="add-unit" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Satuan Unit</h5>

                <form class="mt-5" method="POST" action="{{ route('units.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Satuan')" />

                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">Tambah
                            Satuan</button>
                    </div>
                </form>
            </div>
        </x-modal>

        <div class="mt-8">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class=" text-white">
                        <th class="w-10">No</th>
                        <th>Nama Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-unit" :show="false">
        <div class="p-5" x-data="{
            unit: null,
            setUnit(data) {
                this.unit = data;
            }
        }" @set-unit.window="setUnit($event.detail)">
            <h5 class="font-semibold text-md">Edit Satuan Unit</h5>

            <form class="mt-5" method="POST" :action="`/admin/units/${unit?.id}`">
                @method('PUT')
                @csrf

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nama Satuan')" />

                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                        :value="''" x-bind:value="unit?.name" autofocus />
                </div>

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-unit" :show="false">
        <div class="p-5" x-data="{
            unit: null,
            setUnit(data) {
                this.unit = data;
            }
        }" @set-unit.window="setUnit($event.detail)">
            <h5 class="font-semibold text-md">Hapus Satuan Unit</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus satuan unit <span x-text="unit?.name"></span>?</p>

            <form class="mt-5" method="POST" :action="`/admin/units/${unit?.id}`">
                @method('DELETE')
                @csrf

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
        $(function() {
            $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('units.data') }}",
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
                            targets: 1,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('w-full');
                            }
                        },
                        {
                            targets: 2,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center gap-2 w-max');
                            }
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>
</x-app-layout>
