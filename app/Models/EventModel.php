<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table            = 'events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'banner_path',
        'venue_name',
        'address',
        'city',
        'start_date',
        'end_date',
        'timezone',
        'event_type', // internal, external, hybrid
        'reg_start_date',
        'reg_end_date',
        'checkin_start_date',
        'checkin_end_date',
        'total_quota',
        'member_quota',
        'external_quota',
        'requires_approval',
        'is_registration_open',
        'allow_kta_checkin',
        'status', // draft, published, cancelled, completed
        'form_config',
        'confirmation_message',
        'privacy_consent_text',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Check if registration is open according to dates, flag, and status
     */
    public function isRegistrationActive(array $event): array
    {
        if ($event['status'] !== 'published') {
            return ['active' => false, 'reason' => 'Kegiatan belum dipublikasikan atau sudah ditutup.'];
        }

        if (! (bool) ($event['is_registration_open'] ?? true)) {
            return ['active' => false, 'reason' => 'Pendaftaran untuk kegiatan ini sedang dinonaktifkan.'];
        }

        $now = date('Y-m-d H:i:s');

        if (! empty($event['reg_start_date']) && $now < $event['reg_start_date']) {
            return [
                'active' => false, 
                'reason' => 'Pendaftaran belum dibuka (dibuka pada ' . date('d M Y H:i', strtotime($event['reg_start_date'])) . ').'
            ];
        }

        if (! empty($event['reg_end_date']) && $now > $event['reg_end_date']) {
            return [
                'active' => false, 
                'reason' => 'Batas waktu pendaftaran telah berakhir pada ' . date('d M Y H:i', strtotime($event['reg_end_date'])) . '.'
            ];
        }

        return ['active' => true, 'reason' => ''];
    }

    /**
     * Check if current time is within checkin window
     */
    public function isCheckinWindowOpen(array $event): array
    {
        if ($event['status'] === 'cancelled') {
            return ['allowed' => false, 'reason' => 'Kegiatan telah dibatalkan.'];
        }

        $now = date('Y-m-d H:i:s');

        if (! empty($event['checkin_start_date']) && $now < $event['checkin_start_date']) {
            return [
                'allowed' => false,
                'reason'  => 'Waktu presensi/check-in belum dibuka (dibuka pada ' . date('d M Y H:i', strtotime($event['checkin_start_date'])) . ').'
            ];
        }

        if (! empty($event['checkin_end_date']) && $now > $event['checkin_end_date']) {
            return [
                'allowed' => false,
                'reason'  => 'Waktu presensi/check-in telah ditutup pada ' . date('d M Y H:i', strtotime($event['checkin_end_date'])) . '.'
            ];
        }

        return ['allowed' => true, 'reason' => ''];
    }

    /**
     * Check remaining quota
     */
    public function checkQuotaAvailable(int $eventId, string $participantType): array
    {
        $db = \Config\Database::connect();
        $event = $this->find($eventId);

        if (! $event) {
            return ['available' => false, 'reason' => 'Kegiatan tidak ditemukan.'];
        }

        // Count confirmed and pending registrations that hold quota
        $totalRegistered = $db->table('event_registrations')
            ->where('event_id', $eventId)
            ->whereIn('status', ['confirmed', 'pending_approval', 'pending_verification'])
            ->countAllResults();

        if ($event['total_quota'] > 0 && $totalRegistered >= $event['total_quota']) {
            return ['available' => false, 'reason' => 'Kuota keseluruhan untuk kegiatan ini sudah habis.'];
        }

        if ($participantType === 'member' && $event['member_quota'] > 0) {
            $memberCount = $db->table('event_registrations')
                ->where('event_id', $eventId)
                ->where('participant_type', 'member')
                ->whereIn('status', ['confirmed', 'pending_approval', 'pending_verification'])
                ->countAllResults();

            if ($memberCount >= $event['member_quota']) {
                return ['available' => false, 'reason' => 'Kuota khusus Member KOMEO untuk kegiatan ini sudah penuh.'];
            }
        }

        if ($participantType === 'external' && $event['external_quota'] > 0) {
            $externalCount = $db->table('event_registrations')
                ->where('event_id', $eventId)
                ->where('participant_type', 'external')
                ->whereIn('status', ['confirmed', 'pending_approval', 'pending_verification'])
                ->countAllResults();

            if ($externalCount >= $event['external_quota']) {
                return ['available' => false, 'reason' => 'Kuota peserta umum untuk kegiatan ini sudah penuh.'];
            }
        }

        return ['available' => true, 'reason' => ''];
    }

    /**
     * Return participant & attendance summary counts
     */
    public function getEventStatistics(int $eventId): array
    {
        $db = \Config\Database::connect();

        $totalRegistrations = $db->table('event_registrations')->where('event_id', $eventId)->countAllResults();
        $memberRegistrations = $db->table('event_registrations')->where('event_id', $eventId)->where('participant_type', 'member')->countAllResults();
        $externalRegistrations = $db->table('event_registrations')->where('event_id', $eventId)->where('participant_type', 'external')->countAllResults();
        $confirmedCount = $db->table('event_registrations')->where('event_id', $eventId)->where('status', 'confirmed')->countAllResults();
        $pendingCount = $db->table('event_registrations')->where('event_id', $eventId)->whereIn('status', ['pending_verification', 'pending_approval'])->countAllResults();
        $cancelledCount = $db->table('event_registrations')->where('event_id', $eventId)->where('status', 'cancelled')->countAllResults();
        $checkedInCount = $db->table('event_attendance')->where('event_id', $eventId)->countAllResults();

        $notCheckedInCount = max(0, $confirmedCount - $checkedInCount);
        $attendancePercentage = $confirmedCount > 0 ? round(($checkedInCount / $confirmedCount) * 100, 1) : 0;

        return [
            'total_registrations'    => $totalRegistrations,
            'member_registrations'   => $memberRegistrations,
            'external_registrations' => $externalRegistrations,
            'confirmed'              => $confirmedCount,
            'pending'                => $pendingCount,
            'cancelled'              => $cancelledCount,
            'checked_in'             => $checkedInCount,
            'not_checked_in'         => $notCheckedInCount,
            'attendance_percentage'  => $attendancePercentage,
        ];
    }
}
