<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoVerifierTest extends TestCase
{
    public function test_verifier_reviews_data_proposal_with_a_type_specific_outcome(): void
    {
        $wizard = route('demo.proposal');
        $this->post($wizard, ['step' => 1, 'profile' => 'household-a']);
        $draft = session('demo.drafts.proposal.id');
        $this->post($wizard, ['step' => 2, 'draft_id' => $draft, 'reason' => 'PERBAIKAN', 'document' => '1']);
        $this->post($wizard, ['step' => 3, 'draft_id' => $draft, 'confirm' => '1']);
        $ticket = 'DEMO-D-'.$draft;

        $this->post(route('demo.verifier.enter'));
        $this->get(route('demo.verifier.show', $ticket))->assertNotFound();

        $this->post(route('demo.operator.enter'));
        $this->post(route('demo.operator.start', $ticket));
        $this->post(route('demo.operator.recommend', $ticket), ['reviewed' => '1'])
            ->assertRedirect(route('demo.operator.show', $ticket));

        $review = route('demo.verifier.show', $ticket);
        $decision = route('demo.verifier.decide', $ticket);
        $this->from($review)->post($decision, ['result' => 'PROGRAM_READY', 'confirmed' => '1'])
            ->assertSessionHasErrors('result');

        $this->post($decision, ['result' => 'PROPOSAL_READY', 'confirmed' => '1'])->assertRedirect($review);
        $this->get(route('demo.ticket', $ticket))
            ->assertOk()
            ->assertSeeText('Menunggu penerusan simulasi')
            ->assertSeeText('Panduan pemeriksaan usulan data simulasi BantuTepat');
    }

    public function test_reviewer_cannot_be_the_same_operator_or_serve_another_territory(): void
    {
        $operator = config('bantutepat_demo_applications.operator');
        $verifier = config('bantutepat_demo_applications.verifier');
        $ticket = 'DEMO-P-REVIEWCHECK1';
        $record = [
            'status' => 'PENDING_APPROVAL',
            'profile' => ['territory' => 'Wilayah B'],
            'recommendation' => [
                'operator_id' => $operator['id'],
                'verifier_id' => $verifier['id'],
                'agency' => $verifier['agency'],
            ],
        ];
        $queue = route('demo.verifier.index');
        $review = route('demo.verifier.show', $ticket);
        $recordKey = 'demo.tickets.'.$ticket;

        $this->withSession(['demo' => ['verifier' => ['active' => true]]]);
        session()->put($recordKey, $record);
        $this->get($queue)->assertOk()->assertDontSeeText($ticket);
        $this->get($review)->assertNotFound();

        $record['profile']['territory'] = $verifier['territory'];
        $record['recommendation']['verifier_id'] = $operator['id'];
        config(['bantutepat_demo_applications.verifier.id' => $operator['id']]);
        session()->put($recordKey, $record);
        $this->get($review)->assertNotFound();
    }
}
