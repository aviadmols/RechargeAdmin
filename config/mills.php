<?php

return [

    'primary_color' => '#002642',
    'background_color' => '#d7ecff',

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
            'link' => '#',
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
        'default' => '/images/placeholder-product.svg',
        'Chicken' => '/images/products/chicken.png',
        'Lamb' => '/images/products/lamb.png',
        'Salmon' => '/images/products/salmon.png',
    ],

];
