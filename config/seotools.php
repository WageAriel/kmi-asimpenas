<?php
/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', true), // Ubah ke true untuk Inertia.js
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => "ASIMPENAS", // set false to total remove
            'titleBefore'  => false, // Put defaults.title before page title, like 'It's Over 9000! - Dashboard'
            'description'  => 'Aplikasi Seleksi Mitra dan Penawaran Komoditas - Perum BULOG Kantor Cabang Surakarta untuk pengelolaan data mitra gabah dan beras dalam negeri', // set false to total remove
            'separator'    => ' | ',
            'keywords'     => ['ASIMPENAS','asimpenas', 'Bulog', 'Mitra Pangan', 'Gabah', 'Beras', 'Surakarta', 'Sistem Informasi', 'Manajemen Mitra'],
            'canonical'    => 'current', // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'robots'       => 'index,follow', // Set to 'all', 'none' or any combination of index/noindex and follow/nofollow
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null, // Nanti diisi setelah verifikasi Google Search Console
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => 'ASIMPENAS - Sistem Informasi Manajemen Mitra Pangan', // set false to total remove
            'description' => 'Aplikasi Seleksi Mitra dan Penawaran Komoditas - Perum BULOG Kantor Cabang Surakarta untuk pengelolaan data mitra gabah dan beras dalam negeri', // set false to total remove
            'url'         => null, // Set null for using Url::current(), set false to total remove
            'type'        => 'website',
            'site_name'   => 'ASIMPENAS',
            'images'      => [],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            //'card'        => 'summary',
            //'site'        => '@LuizVinicius73',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title'       => 'Over 9000 Thousand!', // set false to total remove
            'description' => 'For those who helped create the Genki Dama', // set false to total remove
            'url'         => false, // Set to null or 'full' to use Url::full(), set to 'current' to use Url::current(), set false to total remove
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];
