<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class DownloadController extends Controller
{
    /**
     * @var array<string, array<string, string>>
     */
    private const PLATFORMS = [
        'macos' => [
            'name' => 'MacOS',
            'headline' => 'Download Pelicon for MacOS',
            'copy' => 'MacOS Release.',
        ],
        'windows' => [
            'name' => 'Windows',
            'headline' => 'Download Pelicon for Windows',
            'copy' => 'Windows Release.',
        ],
        'linux' => [
            'name' => 'Linux',
            'headline' => 'Download Pelicon for Linux',
            'copy' => 'Linux Release.',
        ],
    ];

    /**
     * Display-only localized pricing for the future Stripe flow.
     *
     * @var array<string, array<string, mixed>>
     */
    private const CURRENCIES = [
        'usd' => [
            'code' => 'USD',
            'symbol' => '$',
            'label' => 'US Dollar',
            'business_yearly' => 15,
            'business_onetime' => 40,
            'tips' => [1, 5, 10, 25],
            'custom_step' => 1,
        ],
        'eur' => [
            'code' => 'EUR',
            'symbol' => '€',
            'label' => 'Euro',
            'business_yearly' => 13,
            'business_onetime' => 34,
            'tips' => [1, 4, 9],
            'custom_step' => 1,
        ],
        'gbp' => [
            'code' => 'GBP',
            'symbol' => '£',
            'label' => 'British Pound',
            'business_yearly' => 11,
            'business_onetime' => 29,
            'tips' => [1, 4, 8],
            'custom_step' => 1,
        ],
        'jpy' => [
            'code' => 'JPY',
            'symbol' => '¥',
            'label' => 'Japanese Yen',
            'business_yearly' => 2200,
            'business_onetime' => 5800,
            'tips' => [150, 700, 1500],
            'custom_step' => 1,
        ],
        'aed' => [
            'code' => 'AED',
            'symbol' => 'AED ',
            'label' => 'UAE Dirham',
            'business_yearly' => 49,
            'business_onetime' => 129,
            'tips' => [5, 20, 40],
            'custom_step' => 1,
        ],
    ];

    public function index(): View
    {
        return view('pages.download.index', [
            'platforms' => self::PLATFORMS,
        ]);
    }

    public function show(Request $request, string $platform): View
    {
        $platformConfig = Arr::get(self::PLATFORMS, strtolower($platform));

        abort_unless($platformConfig, 404);

        return view('pages.download.show', [
            'platform' => $platformConfig,
            'platformKey' => strtolower($platform),
            'platforms' => self::PLATFORMS,
            'currencies' => self::CURRENCIES,
            'defaultCurrency' => 'usd',
        ]);
    }
}
