<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicFrontendTest extends TestCase
{
    public function test_home_only_features_programs_with_an_active_demo_period(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeText('Bantuan Pangan Keluarga A')
            ->assertSeeText('Dukungan Lansia Wilayah C')
            ->assertDontSeeText('Dukungan Pendidikan Wilayah B');
    }

    public function test_catalog_filters_can_be_combined_and_reset(): void
    {
        $this->get('/program')
            ->assertOk()
            ->assertSeeText('4 program ditemukan');

        $this->get('/program?'.http_build_query([
            'q' => 'PANGAN',
            'territory' => 'Wilayah A',
            'category' => 'Pangan',
            'status' => 'OPEN',
            'mode' => 'BOTH',
        ]))
            ->assertOk()
            ->assertSeeText('1 program ditemukan')
            ->assertSeeText('Bantuan Pangan Keluarga A')
            ->assertDontSeeText('Dukungan Pendidikan Wilayah B');

        $this->get('/program?mode=OFFICER_ONLY')
            ->assertOk()
            ->assertSeeText('Dukungan Lansia Wilayah C')
            ->assertSeeText('1 program ditemukan');

        $this->get('/program?status=INFO_ONLY')
            ->assertOk()
            ->assertSeeText('Informasi Bantuan Wilayah D')
            ->assertSeeText('Tidak membuka pendaftaran umum')
            ->assertSeeText('1 program ditemukan');

        $this->get('/program?q=tidakditemukan')
            ->assertOk()
            ->assertSeeText('Program tidak ditemukan')
            ->assertSeeText('Hapus semua filter');
    }

    public function test_invalid_filter_preserves_the_other_selection_with_inline_feedback(): void
    {
        $this->get('/program?status[]=OPEN&mode=OFFICER_ONLY')
            ->assertOk()
            ->assertSee('id="status-error"', false)
            ->assertSeeText('Pilih salah satu pilihan yang tersedia.')
            ->assertSeeText('1 program ditemukan')
            ->assertSeeText('Dukungan Lansia Wilayah C');
    }

    #[DataProvider('programDetails')]
    public function test_program_details_explain_the_registration_path(string $slug, string $explanation): void
    {
        $this->get('/program/'.$slug)
            ->assertOk()
            ->assertSeeText('Persyaratan program')
            ->assertSeeText('Dokumen pendukung')
            ->assertSeeText($explanation);
    }

    public static function programDetails(): array
    {
        return [
            ['bantuan-pangan-a', 'dua jalur, yaitu pengajuan mandiri dan pengusulan melalui petugas'],
            ['dukungan-pendidikan-b', 'Periode pendaftaran simulasi belum dibuka'],
            ['dukungan-lansia-c', 'petugas yang berwenang mengusulkan warga'],
            ['informasi-bantuan-d', 'Program tidak membuka pendaftaran umum'],
        ];
    }

    public function test_unknown_program_has_a_catalog_recovery_link(): void
    {
        $this->get('/program/tidak-ada')
            ->assertNotFound()
            ->assertSeeText('Buka katalog program');
    }

    public function test_catalog_and_detail_show_the_same_program_schedule(): void
    {
        $schedule = '20 November - 18 Desember 2026';
        config(['bantutepat_demo_program_details.bantuan-pangan-a.schedule' => $schedule]);

        $this->get('/program?q=pangan')
            ->assertOk()
            ->assertSeeText('Periode pendaftaran')
            ->assertSeeText($schedule);

        $this->get('/program/bantuan-pangan-a')
            ->assertOk()
            ->assertSeeText($schedule);
    }

    public function test_guide_and_help_describe_the_public_services(): void
    {
        $this->get('/panduan')
            ->assertOk()
            ->assertSeeText('Usulan atau koreksi data')
            ->assertSeeText('Pendaftaran program tertentu');

        $this->get('/bantuan')
            ->assertOk()
            ->assertSeeText('Pertanyaan umum')
            ->assertSee('href="'.url('/privasi').'"', false);
    }

    public function test_public_information_pages_are_available_from_the_footer(): void
    {
        $home = $this->get('/')->assertOk();

        foreach ([
            '/privasi' => 'Privasi',
            '/syarat-layanan' => 'Syarat layanan',
            '/aksesibilitas' => 'Aksesibilitas',
        ] as $path => $heading) {
            $home->assertSee('href="'.url($path).'"', false);

            $this->get($path)
                ->assertOk()
                ->assertSee('<h1>'.$heading.'</h1>', false);
        }
    }

    public function test_transparency_excludes_small_groups_from_combined_and_local_totals(): void
    {
        $this->get('/transparansi')
            ->assertOk()
            ->assertViewHas('summary', [
                'Dalam pemeriksaan' => 43,
                'Perlu perbaikan' => 23,
                'Penanganan selesai' => 61,
            ]);

        $this->get('/transparansi?period=2026-09&territory=a')
            ->assertOk()
            ->assertViewHas('summary', [
                'Dalam pemeriksaan' => 21,
                'Perlu perbaikan' => 10,
                'Penanganan selesai' => 29,
            ]);

        $this->get('/transparansi?period=2026-09&territory=c')
            ->assertOk()
            ->assertViewHas('summary', [])
            ->assertSeeText('Statistik wilayah belum dapat ditampilkan')
            ->assertDontSeeText('Total kasus ditampilkan');
    }
}
