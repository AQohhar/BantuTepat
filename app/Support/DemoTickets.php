<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;

class DemoTickets
{
    public static function find(Session $session, string $ticket): array
    {
        $record = $session->get('demo.tickets.'.$ticket);
        abort_unless(is_array($record), 404);

        return $record;
    }

    public static function mark(array $record, string $status, string $actor): array
    {
        $record['status'] = $status;
        $record['history'][] = [
            'status' => $status,
            'at' => now('Asia/Jakarta')->toIso8601String(),
            'actor' => $actor,
        ];

        return $record;
    }

    public static function assignment(array $profile): ?string
    {
        $operator = config('bantutepat_demo_applications.operator');

        return $profile['territory'] === $operator['territory'] ? $operator['id'] : null;
    }

    public static function isAssignedToOperator(array $record, array $operator): bool
    {
        // Tiket dari sesi demo sebelum Ruang Petugas memakai aturan wilayah yang sama.
        $assignedOperator = array_key_exists('operator_id', $record)
            ? $record['operator_id']
            : self::assignment($record['profile']);

        return $record['profile']['territory'] === $operator['territory']
            && $assignedOperator === $operator['id'];
    }

    public static function revisionRequest(array $record): array
    {
        $options = config('bantutepat_demo_applications.scenarios.'.$record['type'].'.revision_options');

        // Tiket lama hanya menyimpan status perbaikan dan memakai pilihan bukti pertama.
        return $record['revision_request'] ?? $options[array_key_first($options)];
    }

    public static function canRecommend(array $record): bool
    {
        return in_array($record['status'], ['UNDER_REVIEW', 'RESUBMITTED'], true);
    }

    public static function isAssignedToVerifier(array $record, array $verifier): bool
    {
        $recommendation = $record['recommendation'] ?? null;

        return $recommendation !== null
            && $recommendation['verifier_id'] === $verifier['id']
            && $recommendation['operator_id'] !== $verifier['id']
            && $recommendation['agency'] === $verifier['agency']
            && $record['profile']['territory'] === $verifier['territory'];
    }
}
