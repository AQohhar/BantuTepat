
<?php

return [
    'bantuan-pangan-a' => [
        'code' => 'DEMO-BP-001',
        'schedule' => '15 November - 18 Desember 2026',
        'policy_source' => 'Ketentuan Program Simulasi BP-A/2026',
        'description' => 'Program contoh untuk membantu kebutuhan pangan keluarga dalam wilayah sasaran berdasarkan proses pengajuan dan pemeriksaan administratif.',
        'requirements' => [
            'Berdomisili di Wilayah A sesuai aturan program simulasi.',
            'Mengisi data rumah tangga yang diperlukan.',
            'Menyampaikan informasi yang benar dan relevan.',
            'Mengikuti proses pemeriksaan petugas berwenang.',
        ],
        'documents' => [
            'Informasi identitas simulasi.',
            'Informasi rumah tangga.',
            'Dokumen pendukung apabila diperlukan.',
        ],
    ],

    'dukungan-pendidikan-b' => [
        'code' => 'DEMO-DP-002',
        'schedule' => '1 - 31 Desember 2026',
        'policy_source' => 'Ketentuan Program Simulasi DP-B/2026',
        'description' => 'Program contoh untuk mendukung kebutuhan pendidikan peserta didik dalam wilayah sasaran.',
        'requirements' => [
            'Termasuk cakupan Wilayah B.',
            'Memenuhi persyaratan pendidikan program simulasi.',
            'Mengisi informasi yang diminta saat periode dibuka.',
        ],
        'documents' => [
            'Informasi identitas simulasi.',
            'Bukti status pendidikan simulasi.',
            'Dokumen pendukung sesuai persyaratan.',
        ],
    ],

    'dukungan-lansia-c' => [
        'code' => 'DEMO-DL-003',
        'schedule' => '15 November - 15 Desember 2026',
        'policy_source' => 'Ketentuan Program Simulasi DL-C/2026',
        'description' => 'Program contoh untuk mendukung pelayanan warga lanjut usia dengan mekanisme pengusulan melalui petugas yang memiliki kewenangan.',
        'requirements' => [
            'Termasuk wilayah sasaran program.',
            'Memenuhi ketentuan sasaran lansia yang berlaku pada simulasi.',
            'Diusulkan melalui petugas yang ditugaskan.',
        ],
        'documents' => [
            'Data calon penerima simulasi.',
            'Informasi keluarga atau pendamping bila relevan.',
            'Dokumen pendukung sesuai prosedur.',
        ],
    ],

    'informasi-bantuan-d' => [
        'code' => 'DEMO-IB-004',
        'schedule' => 'Tidak membuka pendaftaran umum',
        'policy_source' => 'Ketentuan Program Simulasi IB-D/2026',
        'description' => 'Contoh program yang hanya menyediakan informasi layanan tanpa membuka pendaftaran mandiri melalui portal.',
        'requirements' => [
            'Mengikuti prosedur yang ditetapkan penyelenggara.',
            'Memeriksa informasi mekanisme resmi program.',
        ],
        'documents' => [
            'Dokumen mengikuti mekanisme penyelenggara.',
        ],
    ],
];
