<?php

return [

    'primary_color' => '#002642',
    'background_color' => '#d7ecff',
    'logo_url' => 'https://millsdailypacks.com/cdn/shop/files/logo.svg?v=1730033744&width=200',

    /*
    |--------------------------------------------------------------------------
    | Promoted products (shown at top of account dashboard)
    |--------------------------------------------------------------------------
    */
    'promoted_products' => [
        [
            'title' => 'Your Dog\'s Magic Daily Pack',
            'description' => 'Customized, pre-portioned, premium food — every day.',
            'cta' => 'Customize Your Pack',
            'link' => 'https://millsdailypacks.com/pages/quiz-main?flavors=open',
            'accent' => 'violet', // violet, pink, emerald
        ],
        [
            'title' => '50% Off First Box',
            'description' => 'Get your first box of Daily Packs at half price.',
            'cta' => 'Redeem Now',
            'link' => '#',
            'accent' => 'pink',
        ],
        [
            'title' => 'Perfect Daily Portions',
            'description' => 'No guesswork. Just the right amount, every day.',
            'cta' => 'Learn More',
            'link' => '#',
            'accent' => 'emerald',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Product images fallback (product title or key -> image URL)
    |--------------------------------------------------------------------------
    | Used when order line_items have no image. Key can be product title substring.
    */
    'product_images' => [
        'default' => 'https://cdn.shopify.com/s/files/1/0723/6000/1791/files/Screenshot_2025-07-15_at_11.34.41.webp?v=1752568560',
        'Chicken' => 'https://cdn.shopify.com/s/files/1/0723/6000/1791/files/chickenMDN.webp?v=1754822127',
        'Lamb' => 'https://cdn.shopify.com/s/files/1/0723/6000/1791/files/lambSMN.webp?v=1754822130',
        'Salmon' => 'https://cdn.shopify.com/s/files/1/0723/6000/1791/files/Screenshot_2025-07-15_at_11.34.41.webp?v=1752568560',
    ],

];
