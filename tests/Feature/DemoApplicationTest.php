<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoApplicationTest extends TestCase
{
    public function test_program_application_keeps_one_ticket_through_a_revision(): void
    {
        $slug = 'bantuan-pangan-a';
        $wizard = route('demo.program', $slug);

        $this->get(route('program.show', $slug))
            ->assertOk()
            ->assertSee('href="'.$wizard.'"', false);

        $this->get($wizard.'?langkah=3')->assertRedirect($wizard.'?langkah=1');
        $this->get($wizard)->assertOk()->assertSeeText('Pilih profil rumah tangga');

        $this->post($wizard, ['step' => 1, 'profile' => 'household-a'])
            ->assertRedirect($wizard.'?langkah=2');
        $draftId = session('demo.drafts.program.'.$slug.'.id');

        $this->get($wizard.'?langkah=2')->assertOk()->assertSeeText('Siapkan berkas contoh');

        $this->post($wizard, ['step' => 2, 'draft_id' => $draftId, 'document' => '1'])
            ->assertRedirect($wizard.'?langkah=3');

        $this->get($wizard.'?langkah=3')
            ->assertOk()
            ->assertSeeText('Periksa ringkasan')
            ->assertSeeText('Wilayah A');

        $ticket = 'DEMO-P-'.$draftId;
        $ticketUrl = route('demo.ticket', $ticket);
        $confirmation = ['step' => 3, 'draft_id' => $draftId, 'confirm' => '1'];

        $this->post($wizard, $confirmation)->assertRedirect($ticketUrl);
        $this->post($wizard, $confirmation)->assertRedirect($ticketUrl);
        $this->assertCount(1, session('demo.tickets'));

        $this->get($ticketUrl)
            ->assertOk()
            ->assertSeeText('Tiket dibuat')
            ->assertDontSeeText('Simulasikan pemeriksaan');

        $operatorDetail = route('demo.operator.show', $ticket);
        $startReview = route('demo.operator.start', $ticket);
        $requestRevision = route('demo.operator.request-revision', $ticket);
        $this->post($startReview)->assertRedirect(route('demo.operator.login'));
        $this->post(route('demo.operator.enter'))->assertRedirect(route('demo.operator.index'));
        $this->get($operatorDetail)->assertOk()->assertSeeText('Mulai pemeriksaan');
        $this->post($requestRevision, [])->assertRedirect($operatorDetail);
        $this->post($startReview)->assertRedirect($operatorDetail);
        $this->get($operatorDetail)->assertOk()->assertSeeText('Minta perbaikan');

        $this->from($operatorDetail)->post($requestRevision, [
            'reason_code' => 'DOMISILI',
            'deadline' => today('Asia/Jakarta')->subDay()->toDateString(),
        ])->assertSessionHasErrors('deadline');

        $deadline = today('Asia/Jakarta')->addDays(3);
        $this->post($requestRevision, [
            'reason_code' => 'RINGKASAN_RUMAH_TANGGA',
            'deadline' => $deadline->toDateString(),
        ])->assertRedirect($operatorDetail);

        $this->get($ticketUrl)
            ->assertOk()
            ->assertSeeText('Perlu perbaikan')
            ->assertSeeText('Informasi anggota rumah tangga pada ringkasan perlu dilengkapi.')
            ->assertSeeText($deadline->format('d/m/Y'))
            ->assertSeeText('Operator Contoh A');

        $revisionUrl = route('demo.ticket.revision', $ticket);
        $this->get($revisionUrl)->assertOk()->assertSeeText('Lengkapi bukti contoh');

        $this->from($revisionUrl)->post($revisionUrl, [])->assertSessionHasErrors('revision_document');
        $this->post($revisionUrl, ['revision_document' => '1'])->assertRedirect($ticketUrl);
        $this->post($revisionUrl, ['revision_document' => '1'])->assertRedirect($ticketUrl);

        $this->get($ticketUrl)
            ->assertOk()
            ->assertSeeText($ticket)
            ->assertSeeText('Perbaikan dikirim ulang')
            ->assertSeeText('Ringkasan rumah tangga simulasi yang diperbarui');
        $this->assertSame(
            ['SUBMITTED', 'UNDER_REVIEW', 'REVISION_REQUIRED', 'RESUBMITTED'],
            array_column(session('demo.tickets.'.$ticket.'.history'), 'status'),
        );

        $this->post(route('demo.workspace.enter'))->assertRedirect(route('demo.workspace.index'));
        $this->get(route('demo.workspace.index'))
            ->assertOk()
            ->assertSeeText($ticket)
            ->assertSeeText('Bantuan Pangan Keluarga A');

        $recommend = route('demo.operator.recommend', $ticket);
        $this->from($operatorDetail)->post($recommend, [])->assertSessionHasErrors('reviewed');
        $this->post($recommend, ['reviewed' => '1'])->assertRedirect($operatorDetail);
        $this->get($ticketUrl)->assertOk()->assertSeeText('Menunggu tinjauan verifikator');

        $review = route('demo.verifier.show', $ticket);
        $decision = route('demo.verifier.decide', $ticket);
        $option = ['result' => 'PROGRAM_EVIDENCE_INCOMPLETE', 'confirmed' => '1'];
        $this->post($decision, $option)->assertRedirect(route('demo.verifier.login'));
        $this->post(route('demo.verifier.enter'))->assertRedirect(route('demo.verifier.index'));
        $this->get(route('demo.verifier.index'))->assertOk()->assertSeeText($ticket);
        $this->get($review)
            ->assertOk()
            ->assertSeeText('Rekomendasi operator')
            ->assertSeeText('Ringkasan rumah tangga simulasi yang diperbarui');
        $this->post($decision, $option)->assertRedirect($review);
        $this->post($decision, ['result' => 'PROGRAM_READY', 'confirmed' => '1'])->assertRedirect($review);
        $this->get($ticketUrl)
            ->assertOk()
            ->assertSeeText('Hasil tinjauan simulasi')
            ->assertSeeText('Belum dapat diproses')
            ->assertSeeText('Ketentuan Program Simulasi BP-A/2026')
            ->assertSeeText('Verifikator Contoh A');
        $this->get(route('demo.verifier.index', ['tampilan' => 'hasil']))->assertOk()->assertSeeText($ticket);
        $this->assertSame(
            ['SUBMITTED', 'UNDER_REVIEW', 'REVISION_REQUIRED', 'RESUBMITTED', 'PENDING_APPROVAL', 'CLOSED_UNPROCESSABLE'],
            array_column(session('demo.tickets.'.$ticket.'.history'), 'status'),
        );

        $this->flushSession();
        $this->get($ticketUrl)->assertNotFound();
        $this->post(route('demo.operator.enter'));
        $this->post($startReview)->assertNotFound();
    }

    public function test_catalog_program_rules_block_unavailable_routes_and_outside_profiles(): void
    {
        foreach (['dukungan-pendidikan-b', 'dukungan-lansia-c', 'informasi-bantuan-d'] as $slug) {
            $detail = route('program.show', $slug);
            $wizard = route('demo.program', $slug);
            $this->get($detail)->assertOk()->assertDontSeeText('Coba simulasi pendaftaran');
            $this->get($wizard)->assertRedirect($detail);
            $this->post($wizard, ['step' => 1, 'profile' => 'household-a'])->assertRedirect($detail);
        }

        $wizard = route('demo.program', 'bantuan-pangan-a');

        $this->from($wizard)->post($wizard, ['step' => 1, 'profile' => 'household-b'])
            ->assertRedirect($wizard)
            ->assertSessionHasErrors('profile')
            ->assertSessionMissing('demo.drafts.program.bantuan-pangan-a');

        $this->get($wizard)
            ->assertOk()
            ->assertSeeText('Profil ini berada di luar wilayah sasaran.');

        $this->post($wizard, ['step' => 3, 'draft_id' => 'ABCDEFGHIJKL', 'confirm' => '1'])
            ->assertRedirect($wizard.'?langkah=1')
            ->assertSessionMissing('demo.tickets');
    }

    public function test_data_proposal_uses_a_separate_form_and_ticket(): void
    {
        $wizard = route('demo.proposal');

        $this->get(route('guide'))
            ->assertOk()
            ->assertSee('href="'.$wizard.'"', false);

        $this->post($wizard, ['step' => 1, 'profile' => 'household-b'])
            ->assertRedirect($wizard.'?langkah=2');
        $draftId = session('demo.drafts.proposal.id');

        $this->get($wizard.'?langkah=2')->assertOk()->assertSeeText('Alasan usulan');
        $this->post($wizard, [
            'step' => 2,
            'draft_id' => $draftId,
            'reason' => 'PERBAIKAN',
            'document' => '1',
        ])->assertRedirect($wizard.'?langkah=3');

        $ticket = 'DEMO-D-'.$draftId;
        $ticketUrl = route('demo.ticket', $ticket);
        $this->post($wizard, ['step' => 3, 'draft_id' => $draftId, 'confirm' => '1'])
            ->assertRedirect($ticketUrl)
            ->assertSessionHas('demo.tickets.'.$ticket.'.type', 'DATA_PROPOSAL');

        $this->get($ticketUrl)
            ->assertOk()
            ->assertSeeText('Data rumah tangga perlu dikoreksi')
            ->assertDontSeeText('Bantuan Pangan Keluarga A');

        $this->post(route('demo.operator.enter'));
        $this->get(route('demo.operator.show', $ticket))->assertNotFound();
    }
}
