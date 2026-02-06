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
                        'icon'  => '📦',
                        'children' => [
                            [
                                'label' => 'User',
                                'route' => '/admin/users',
                            ],
                            [
                                'label' => 'Area Parkir',
                                'route' => '/admin/areaParkir',
                            ],
                            [
                                'label' => 'Tipe Kendaraan',
                                'route' => '/admin/tipeKendaraan',
                            ],
                            [
                                'label' => 'Tarif Tipe Kendaraan',
                                'route' => '/admin/tarifTipeKendaraan',
                            ],
                            [
                                'label' => 'Kendaraan',
                                'route' => '/admin/kendaraan',
                            ],

                            [
                                'label' => 'Metode Pembayaran',
                                'route' => '/admin/metodePembayaran',
                            ],
                            
                        ],
                    ],

                    [
                        'label' => 'Membership',
                        'icon'  => '⭐',
                        'children' => [
                            [
                                'label' => 'Membership Tier',
                                'route' => '/admin/membership-tier',
                            ],
                            [
                                'label' => 'Daftar Membership',
                                'route' => '/admin/membership',
                            ],
                        ],
                    ],

                    [
                        'label' => 'Laporan',
                        'icon'  => '📃',
                        'children' => [
                                                    ['label' => 'Riwayat Transaksi',
                        'route' => '/laporan/riwayatTransaksi',],
                            [
                                'label' => 'Transaksi Harian',
                                'route' => '/laporan/harian',
                            ],
                            [
                                'label' => 'Transaksi Periode',
                                'route' => '/laporan/periode',
                            ],
                            [
                                'label' => 'Occupancy Area Parkir',
                                'route' => '/laporan/occupancy',
                            ],
                        ],
                            
                    ],
                    [
                                'label' => 'Tracking Kendaraan',
                                'route' => '/tracking-kendaraan',
                            ],
                ],


                'petugas' => [
                    [
                        'label' => 'Transaksi',
                        'route' => '/petugas/transaksi',
                        'icon'  => '📋',
                    ],
                    [
                        'label' => 'Riwayat Transaksi',
                        'route' => '/laporan/riwayatTransaksi',
                        'icon'  => '⌛',
                    ],
                                                [
                                'label' => 'Occupancy Area Parkir',
                                'route' => '/laporan/occupancy',
                            ],
                                                [
                                'label' => 'Tracking Kendaraan',
                                'route' => '/tracking-kendaraan',
                            ],
                ],

                'owner' => [
                                                    ['label' => 'Riwayat Transaksi',
                        'route' => '/laporan/riwayatTransaksi',],
                            [
                                'label' => 'Transaksi Harian',
                                'route' => '/laporan/harian',
                            ],
                            [
                                'label' => 'Transaksi Periode',
                                'route' => '/laporan/periode',
                            ],
                            [
                                'label' => 'Occupancy Area Parkir',
                                'route' => '/laporan/occupancy',
                            ],
                ],
            ];

            $view->with('menus', $menus[$role] ?? []);
        });
    }
}
