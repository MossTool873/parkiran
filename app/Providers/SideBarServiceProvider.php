<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class SidebarServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('components.sidebar', function ($view) {
            $role = Auth::check() ? Auth::user()->role->role : null;

            $menus = [
                'admin' => [
                    [
                        'label' => 'Master Data',
                        'icon'  => 'bi bi-stack',
                        'children' => [
                            [
                                'label' => 'User',
                                'route' => '/admin/users',
                                'icon'  => 'bi bi-person'
                            ],
                            [
                                'label' => 'Tipe Kendaraan',
                                'route' => '/admin/tipeKendaraan',
                                'icon'  => 'bi bi-truck'
                            ],
                            [
                                'label' => 'Area Parkir',
                                'route' => '/admin/areaParkir',
                                'icon'  => 'bi bi-geo-alt'
                            ],


                            [
                                'label' => 'Kendaraan',
                                'route' => '/admin/kendaraan',
                                'icon'  => 'bi bi-car-front'
                            ],
                            [
                                'label' => 'Metode Pembayaran',
                                'route' => '/admin/metodePembayaran',
                                'icon'  => 'bi bi-credit-card'
                            ],
                        ],
                    ],
                    [
                        'label' => 'Tarif',
                        'icon'  => 'bi bi-cash-stack',
                        'children' => [
                            [
                                'label' => 'Tarif Dasar',
                                'route' => '/admin/tarifTipeKendaraan',
                                'icon'  => 'bi bi-currency-dollar'
                            ],
                            [
                                'label' => 'Tarif Durasi',
                                'route' => '/admin/tarif-durasi',
                                'icon'  => 'bi bi-clock'
                            ],
                            [
                                'label' => 'Konfigurasi Tarif',
                                'route' => '/admin/konfigurasi-tarif',
                                'icon'  => 'bi bi-gear'
                            ],
                            [
                                'label' => 'Diskon Membership',
                                'route' => '/admin/membership-tier',
                                'icon'  => 'bi bi-award'
                            ],
                        ],
                    ],

                    [
                        'label' => 'Membership',
                        'icon'  => 'bi bi-stars',
                        'children' => [
                            [
                                'label' => 'Membership Tier',
                                'route' => '/admin/membership-tier',
                                'icon'  => 'bi bi-award'
                            ],
                            [
                                'label' => 'Daftar Membership',
                                'route' => '/admin/membership',
                                'icon'  => 'bi bi-list'
                            ],
                            [
                                'label' => 'Kendaraan Membership',
                                'route' => '/admin/membership-kendaraan',
                                'icon'  => 'bi bi-car-front'
                            ],
                        ],
                    ],
                    [
                        'label' => 'Laporan',
                        'icon'  => 'bi bi-file-earmark-text',
                        'children' => [
                            [
                                'label' => 'Riwayat Transaksi',
                                'route' => '/laporan/riwayatTransaksi',
                                'icon'  => 'bi bi-clock-history'
                            ],
                            [
                                'label' => 'Transaksi Harian',
                                'route' => '/laporan/harian',
                                'icon'  => 'bi bi-calendar-day'
                            ],
                            [
                                'label' => 'Transaksi Periode',
                                'route' => '/laporan/periode',
                                'icon'  => 'bi bi-calendar'
                            ],
                            [
                                'label' => 'Occupancy Area Parkir',
                                'route' => '/laporan/occupancy',
                                'icon'  => 'bi bi-bar-chart'
                            ],
                            [
                                'label' => 'Tracking Kendaraan',
                                'route' => '/tracking-kendaraan',
                                'icon'  => 'bi bi-geo-alt'
                            ],
                        ],
                    ],
                    [
                        'label' => 'Backup-Restore Database',
                        'route' => '/admin/database/index',
                        'icon'  => 'bi bi-database'
                    ],
                    [
                        'label' => 'Log Aktivitas',
                        'route' => '/admin/log-aktivitas',
                        'icon'  => 'bi bi-clock-history'
                    ],
                ],

                'petugas' => [
                    [
                        'label' => 'Transaksi',
                        'route' => '/petugas/transaksi',
                        'icon'  => 'bi bi-receipt'
                    ],
                    [
                        'label' => 'Riwayat Transaksi',
                        'route' => '/laporan/riwayatTransaksi',
                        'icon'  => 'bi bi-clock-history'
                    ],
                    [
                        'label' => 'Occupancy Area Parkir',
                        'route' => '/laporan/occupancy',
                        'icon'  => 'bi bi-bar-chart'
                    ],
                    [
                        'label' => 'Tracking Kendaraan',
                        'route' => '/tracking-kendaraan',
                        'icon'  => 'bi bi-geo-alt'
                    ],
                ],

                'owner' => [
                    [
                        'label' => 'Riwayat Transaksi',
                        'route' => '/laporan/riwayatTransaksi',
                        'icon'  => 'bi bi-clock-history'
                    ],
                    [
                        'label' => 'Transaksi Harian',
                        'route' => '/laporan/harian',
                        'icon'  => 'bi bi-calendar-day'
                    ],
                    [
                        'label' => 'Transaksi Periode',
                        'route' => '/laporan/periode',
                        'icon'  => 'bi bi-calendar'
                    ],
                    [
                        'label' => 'Occupancy Area Parkir',
                        'route' => '/laporan/occupancy',
                        'icon'  => 'bi bi-bar-chart'
                    ],
                ],
            ];

            $view->with('menus', $menus[$role] ?? []);
        });
    }
}
