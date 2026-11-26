<?php

return [
    'operator' => [
        'id' => 'operator-demo-a',
        'name' => 'Operator Contoh A',
        'territory' => 'Wilayah A',
        'revision_days' => 3,
    ],
    'verifier' => [
        'id' => 'verifier-demo-a',
        'name' => 'Verifikator Contoh A',
        'territory' => 'Wilayah A',
        'agency' => 'Instansi Simulasi Wilayah A',
    ],
    'profiles' => [
        'household-a' => ['name' => 'Rumah Tangga Contoh A', 'territory' => 'Wilayah A'],
        'household-b' => ['name' => 'Rumah Tangga Contoh B', 'territory' => 'Wilayah B'],
        'household-c' => ['name' => 'Rumah Tangga Contoh C', 'territory' => 'Wilayah C'],
        'household-d' => ['name' => 'Rumah Tangga Contoh D', 'territory' => 'Wilayah D'],
    ],
    'proposal_reasons' => [
        'BELUM_TERDATA' => 'Rumah tangga belum tercatat',
        'PERBAIKAN' => 'Data rumah tangga perlu dikoreksi',
        'PERUBAHAN_KONDISI' => 'Kondisi rumah tangga berubah',
    ],
    'workspace_examples' => [
        [
            'type' => 'PROGRAM_APPLICATION',
            'program_slug' => 'bantuan-pangan-a',
            'profile' => 'household-a',
            'reason' => null,
            'history' => ['SUBMITTED'],
        ],
        [
            'type' => 'DATA_PROPOSAL',
            'program_slug' => null,
            'profile' => 'household-b',
            'reason' => 'PERBAIKAN',
            'history' => ['SUBMITTED'],
        ],
    ],
    'scenarios' => [
        'PROGRAM_APPLICATION' => [
            'label' => 'Pendaftaran program',
            'ticket_prefix' => 'DEMO-P-',
            'initial_document' => 'Ringkasan rumah tangga simulasi',
            'revision_options' => [
                'DOMISILI' => [
                    'reason' => 'Ringkasan rumah tangga belum memuat bukti domisili di wilayah sasaran.',
                    'document' => 'Bukti domisili simulasi',
                ],
                'RINGKASAN_RUMAH_TANGGA' => [
                    'reason' => 'Informasi anggota rumah tangga pada ringkasan perlu dilengkapi.',
                    'document' => 'Ringkasan rumah tangga simulasi yang diperbarui',
                ],
            ],
        ],
        'DATA_PROPOSAL' => [
            'label' => 'Usulan atau koreksi data',
            'ticket_prefix' => 'DEMO-D-',
            'initial_document' => 'Ringkasan usulan data simulasi',
            'revision_options' => [
                'CATATAN_PEMBARUAN' => [
                    'reason' => 'Catatan pendukung diperlukan untuk mencocokkan usulan dengan data rumah tangga contoh.',
                    'document' => 'Catatan pembaruan data simulasi',
                ],
                'RINGKASAN_KONDISI' => [
                    'reason' => 'Ringkasan kondisi awal dan kondisi terbaru perlu dilengkapi.',
                    'document' => 'Ringkasan perubahan kondisi simulasi',
                ],
            ],
        ],
    ],
    'states' => [
        'SUBMITTED' => [
            'label' => 'Tiket dibuat',
            'description' => 'Pilihan formulir disimpan dalam sesi demo ini.',
        ],
        'UNDER_REVIEW' => [
            'label' => 'Dalam pemeriksaan',
            'description' => 'Dokumen contoh ditinjau dalam skenario pemeriksaan.',
        ],
        'REVISION_REQUIRED' => [
            'label' => 'Perlu perbaikan',
            'description' => 'Lengkapi bukti contoh yang diminta pada tiket ini.',
        ],
        'RESUBMITTED' => [
            'label' => 'Perbaikan dikirim ulang',
            'description' => 'Bukti tambahan tersimpan pada tiket yang sama.',
        ],
        'PENDING_APPROVAL' => [
            'label' => 'Menunggu tinjauan verifikator',
            'description' => 'Operator merekomendasikan pengajuan untuk ditinjau.',
        ],
        'RECOMMENDED_FOR_FORWARD' => [
            'label' => 'Direkomendasikan untuk diteruskan',
            'description' => 'Verifikator mencatat rekomendasi tindak lanjut untuk program simulasi.',
        ],
        'FORWARDING_PENDING' => [
            'label' => 'Menunggu penerusan simulasi',
            'description' => 'Verifikator telah meninjau usulan. Penerusan menunggu tahap selanjutnya.',
        ],
        'CLOSED_UNPROCESSABLE' => [
            'label' => 'Belum dapat diproses',
            'description' => 'Verifikator mencatat alasan administratif pada hasil tinjauan simulasi.',
        ],
    ],
    'review_options' => [
        'PROGRAM_APPLICATION' => [
            'PROGRAM_READY' => [
                'status' => 'RECOMMENDED_FOR_FORWARD',
                'reason' => 'Berkas contoh sesuai dengan informasi dasar pada ketentuan program simulasi.',
                'next' => 'Simpan nomor tiket untuk melihat tindak lanjut pada tahap berikutnya.',
            ],
            'PROGRAM_EVIDENCE_INCOMPLETE' => [
                'status' => 'CLOSED_UNPROCESSABLE',
                'reason' => 'Berkas contoh yang ditinjau belum cukup untuk memastikan persyaratan administratif program simulasi.',
                'next' => 'Baca ketentuan program dan panduan hasil pemeriksaan untuk mengetahui jalur tindak lanjut.',
            ],
        ],
        'DATA_PROPOSAL' => [
            'PROPOSAL_READY' => [
                'status' => 'FORWARDING_PENDING',
                'reason' => 'Catatan usulan dan berkas contoh sudah cukup untuk tahap penerusan simulasi.',
                'next' => 'Simpan nomor tiket untuk memantau status penerusan pada tahap berikutnya.',
            ],
            'PROPOSAL_EVIDENCE_INCOMPLETE' => [
                'status' => 'CLOSED_UNPROCESSABLE',
                'reason' => 'Berkas contoh belum cukup untuk mencocokkan kondisi rumah tangga pada usulan ini.',
                'next' => 'Baca panduan usulan data dan tinjau kembali informasi pendukung yang diperlukan.',
            ],
        ],
    ],
];
