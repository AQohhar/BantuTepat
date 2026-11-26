<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoWorkspaceTest extends TestCase
{
    public function test_workspace_requires_demo_entry_and_can_be_closed(): void
    {
        $workspace = route('demo.workspace.index');
        $login = route('demo.workspace.login');

        $this->get($workspace)->assertRedirect($login);
        $this->post(route('demo.workspace.examples'))->assertRedirect($login);

        $this->get($login)
            ->assertOk()
            ->assertSeeText('Masuk sebagai warga contoh');

        $this->post(route('demo.workspace.enter'))->assertRedirect($workspace);
        $this->get($workspace)
            ->assertOk()
            ->assertSeeText('Belum ada tiket di sesi ini')
            ->assertSeeText('Muat tiket contoh');

        $this->post(route('demo.workspace.leave'))->assertRedirect($login);
        $this->get($workspace)->assertRedirect($login);
    }

    public function test_sample_tickets_share_the_existing_detail_and_revision_flow(): void
    {
        $workspace = route('demo.workspace.index');
        $this->post(route('demo.workspace.enter'));

        $this->post(route('demo.workspace.examples'))->assertRedirect($workspace);
        $this->post(route('demo.workspace.examples'))->assertRedirect($workspace);
        $tickets = collect(session('demo.tickets'));
        $this->assertCount(2, $tickets);

        $programTicket = $tickets->search(fn (array $record) => $record['type'] === 'PROGRAM_APPLICATION');
        $proposalTicket = $tickets->search(fn (array $record) => $record['type'] === 'DATA_PROPOSAL');

        $this->get($workspace)
            ->assertOk()
            ->assertSeeText('2 dari 2 tiket ditampilkan')
            ->assertSeeText('Tiket contoh')
            ->assertSeeText('Bantuan Pangan Keluarga A');

        $this->get(route('demo.ticket', $programTicket))
            ->assertOk()
            ->assertSeeText('Kembali ke Pengajuan Saya');

        $this->post(route('demo.operator.enter'));
        $unassignedTicket = 'DEMO-P-UNASSIGNED01';
        session()->put('demo.tickets.'.$unassignedTicket, [
            ...$tickets[$programTicket],
            'operator_id' => null,
        ]);
        $this->get(route('demo.operator.index'))
            ->assertOk()
            ->assertSeeText('1 dari 1 tiket ditampilkan')
            ->assertSeeText($programTicket)
            ->assertDontSeeText($proposalTicket)
            ->assertDontSeeText($unassignedTicket);

        $this->post(route('demo.operator.start', $unassignedTicket))->assertNotFound();
        $this->get(route('demo.operator.show', $proposalTicket))->assertNotFound();
        session()->forget('demo.tickets.'.$unassignedTicket);
        $this->post(route('demo.operator.start', $programTicket));
        $this->post(route('demo.operator.request-revision', $programTicket), [
            'reason_code' => 'DOMISILI',
            'deadline' => today('Asia/Jakarta')->addDays(3)->toDateString(),
        ])->assertRedirect(route('demo.operator.show', $programTicket));

        $this->post(route('demo.ticket.resubmit', $programTicket), ['revision_document' => '1'])
            ->assertRedirect(route('demo.ticket', $programTicket));

        $this->get($workspace.'?'.http_build_query([
            'q' => strtolower($programTicket),
            'type' => 'PROGRAM_APPLICATION',
            'status' => 'RESUBMITTED',
        ]))
            ->assertOk()
            ->assertSeeText('1 dari 2 tiket ditampilkan')
            ->assertSeeText($programTicket)
            ->assertDontSeeText('DEMO-D-');

        $this->get($workspace.'?q=tidak-ada-123')
            ->assertOk()
            ->assertSeeText('Tiket tidak ditemukan')
            ->assertSeeText('Lihat semua tiket');

        $this->post(route('demo.workspace.leave'))->assertRedirect(route('demo.workspace.login'));
        $this->post(route('demo.workspace.enter'))->assertRedirect($workspace);
        $this->get($workspace)->assertOk()->assertSeeText($programTicket);
    }
}
