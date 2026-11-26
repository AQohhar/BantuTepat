<?php

return [
    'minimum_group_size' => 10,
    'periods' => [
        '2026-10' => 'Oktober 2026',
        '2026-09' => 'September 2026',
    ],
    'territories' => [
        'all' => 'Semua wilayah yang ditampilkan',
        'a' => 'Wilayah A',
        'b' => 'Wilayah B',
        'c' => 'Wilayah C',
    ],
    // Kelompok kecil dikecualikan dari angka per wilayah dan ringkasan gabungan.
    'snapshots' => [
        '2026-10' => [
            'a' => [24, 12, 36],
            'b' => [19, 11, 25],
            'c' => null,
        ],
        '2026-09' => [
            'a' => [21, 10, 29],
            'b' => [16, 13, 18],
            'c' => null,
        ],
    ],
    'status_labels' => ['Dalam pemeriksaan', 'Perlu perbaikan', 'Penanganan selesai'],
];
