<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="ms-4 block w-16" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('manage-inventories'))
                        <a class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out cursor-pointer"
                            href="{{ route('inbounds.index') }}">
                            {{ __('Halamanan Utama') }}
                        </a>
                    @endif

                    @if (Auth::user()->hasRole('admin'))
                        <x-nav-link class="hidden md:flex" :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->can('sign-documents'))
                        <x-dropdown-wrapper width="content" name="Persetujuan Dokumen">
                            <x-dropdown-link :href="route('documents.loans.index')">
                                Dokumen Peminjaman Aset
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('documents.outbounds.index')">
                                Dokumen Barang Keluar
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('employee') || Auth::user()->can('monitoring'))
                        <x-nav-link class="hidden md:flex" :href="route('monitoring.assets.index')" :active="request()->routeIs('monitoring.assets.index')">
                            {{ __('Monitoring Aset') }}
                        </x-nav-link>
                        <x-nav-link class="hidden md:flex" :href="route('monitoring.loanAssets.index')" :active="request()->routeIs('monitoring.loanAssets.index')">
                            {{ __('Monitoring Aset Terpinjam') }}
                        </x-nav-link>
                        <x-nav-link class="hidden md:flex" :href="route('monitoring.procurements.index')" :active="request()->routeIs('monitoring.procurements.index')">
                            {{ __('Monitoring Pengadaan') }}
                        </x-nav-link>
                        <x-nav-link class="hidden md:flex" :href="route('monitoring.permits.index')" :active="request()->routeIs('monitoring.permits.index')">
                            {{ __('Monitoring Perizinan') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->hasRole('admin'))
                        <x-dropdown-wrapper name="Kendaraan">
                            <x-dropdown-link :href="route('vehicles.index')">
                                Daftar Kendaraan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('fuels.index')">
                                Pengisian Bahan Bakar
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('manage-assets'))
                        <x-dropdown-wrapper name="Aset">
                            <x-dropdown-link :href="route('assets.index')">
                                Daftar Aset
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('brands.index')">
                                Daftar Merek
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('manage-items'))
                        <x-dropdown-wrapper name="Barang">
                            <x-dropdown-link :href="route('items.index')">
                                Daftar Barang
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('units.index')">
                                Daftar Unit
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('manage-employees'))
                        <x-dropdown-wrapper name="Pegawai">
                            <x-dropdown-link :href="route('employees.index')">
                                Daftar Pegawai
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('positions.index')">
                                Daftar Posisi
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('manage-users'))
                        <x-dropdown-wrapper name="Pengguna">
                            <x-dropdown-link :href="route('users.index')">
                                Daftar Pengguna
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('permissions.index')">
                                Hak Akses Pengguna
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif

                    @if (Auth::user()->hasRole('admin') || Auth::user()->can('view-histories'))
                        <x-dropdown-wrapper name="Riwayat Aset">
                            <x-dropdown-link :href="route('admin.loans.index')">
                                Riwayat Peminjaman
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.returns.index')">
                                Riwayat Pengembalian
                            </x-dropdown-link>
                        </x-dropdown-wrapper>

                        <x-dropdown-wrapper name="Riwayat Barang">
                            <x-dropdown-link :href="route('admin.inbounds.index')">
                                Riwayat Penerimaan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.outbounds.index')">
                                Riwayat Pengeluaran
                            </x-dropdown-link>
                        </x-dropdown-wrapper>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('inbounds.index')">
                {{ __('Halaman Utama') }}
            </x-responsive-nav-link>

            <x-responsive-dropdown title="Persetujuan Dokumen">
                <x-responsive-nav-link :href="route('documents.loans.index')">
                    Dokumen Peminjaman Aset
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.outbounds.index')">
                    Dokumen Pengeluaran Barang
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Aset">
                <x-responsive-nav-link :href="route('assets.index')">
                    Daftar Aset
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('brands.index')">
                    Daftar Merek
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Barang">
                <x-responsive-nav-link :href="route('items.index')">
                    Daftar Barang
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('units.index')">
                    Daftar Unit
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Pegawai">
                <x-responsive-nav-link :href="route('employees.index')">
                    Daftar Pegawai
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('positions.index')">
                    Daftar Posisi
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Pengguna">
                <x-responsive-nav-link :href="route('users.index')">
                    Daftar Pengguna
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('permissions.index')">
                    Hak Akses Pengguna
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Riwayat Barang">
                <x-responsive-nav-link :href="route('admin.inbounds.index')">
                    Riwayat Penerimaan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.outbounds.index')">
                    Riwayat Pengeluaran
                </x-responsive-nav-link>
            </x-responsive-dropdown>

            <x-responsive-dropdown title="Riwayat Aset">
                <x-responsive-nav-link :href="route('admin.loans.index')">
                    Riwayat Peminjaman
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.returns.index')">
                    Riwayat Pengembalian
                </x-responsive-nav-link>
            </x-responsive-dropdown>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
