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
                                'label' => 'Membership Tier',
                                'route' => '/admin/membership-tier',

                            ],
                            [
                                'label' => 'Metode Pembayaran',
                                'route' => '/admin/metodePembayaran',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Membership',
                        'route' => 'admin/membership',
                        'icon'  => '⭐',
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
                        'route' => '/petugas/riwayatTransaksi',
                        'icon'  => '⌛',
                    ],
                ],

                'user' => [
                    [
                        'label' => 'Dashboard',
                        'route' => '/owner',
                        'icon'  => '👤',
                    ],
                ],
            ];

            $view->with('menus', $menus[$role] ?? []);
        });
    }
}
