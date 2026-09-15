<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\DealLog;

class DealLogService
{
    public function logConvert(Deal $deal): void
    {
        $leadNo = $deal->lead ? $deal->lead->enquiry_no : 'N/A';
        $this->write(
            $deal->deal_id,
            'convert',
            'Lead converted to deal: ' . $deal->deal_name . ' (Lead No: ' . $leadNo . ')',
            $deal->toArray()
        );
    }

    public function logCreate(Deal $deal): void
    {
        $this->write(
            $deal->deal_id,
            'create',
            'Deal created: ' . $deal->deal_name,
            $deal->toArray()
        );
    }

    public function logUpdate(Deal $deal, array $before, array $after): void
    {
        $changed = $this->diff($before, $after);

        $this->write(
            $deal->deal_id,
            'update',
            'Deal updated: ' . $deal->deal_name,
            ['before' => $changed['before'], 'after' => $changed['after']]
        );
    }

    public function logDelete(Deal $deal): void
    {
        $this->write(
            $deal->deal_id,
            'delete',
            'Deal deleted: ' . $deal->deal_name,
            $deal->toArray()
        );
    }


    public function log(Deal $deal,$action,$description): void
    {
        $this->write(
            $deal->deal_id,
            $action,
            $description,
            $deal->toArray()
        );
    }
    public function logCustom(int $dealId, string $action, string $description, array $payload): void
    {
        $this->write($dealId, $action, $description, $payload);
    }

    private function write(int $dealId, string $action, string $description, array $payload): void
    {
        DealLog::create([
            'deal_id'      => $dealId,
            'action'       => $action,
            'description'  => $description,
            'payload'      => json_encode($payload),
            'performed_by' => getCreatedBy(),
            'performed_at' => now(),
        ]);
    }

    private function diff(array $before, array $after): array
    {
        $skip          = ['updated_at', 'created_at', 'updated_by'];
        $changedBefore = [];
        $changedAfter  = [];

        foreach ($after as $key => $newVal) {
            if (in_array($key, $skip)) {
                continue;
            }
            $oldVal = $before[$key] ?? null;
            if ((string) $oldVal !== (string) $newVal) {
                $changedBefore[$key] = $oldVal;
                $changedAfter[$key]  = $newVal;
            }
        }

        return ['before' => $changedBefore, 'after' => $changedAfter];
    }
}
