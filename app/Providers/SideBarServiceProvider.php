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
                        'label' => 'Dashboard',
                        'route' => '/admin',
                        'icon'  => '🏠',
                    ],
                    [
                        'label' => 'Master Data',
                        'icon'  => '📦',
                        'children' => [
                            [
                                'label' => 'User',
                                'route' => '/admin/users',
                            ],
                            [
                                'label' => 'Role',
                                'route' => '/admin/roles',
                            ],
                        ],
                    ],
                ],

                'operator' => [
                    [
                        'label' => 'Dashboard',
                        'route' => '/petugas',
                        'icon'  => '📋',
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