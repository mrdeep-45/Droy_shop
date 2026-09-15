<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationLog;

class QuotationLogService
{
    public function logCreate(Quotation $quotation): void
    {
        $this->write(
            $quotation->quotation_id,
            'create',
            'Quotation created: ' . $quotation->quotation_no,
            $quotation->toArray()
        );
    }

    public function logUpdate(Quotation $quotation, array $before, array $after): void
    {
        $changed = $this->diff($before, $after);

        $this->write(
            $quotation->quotation_id,
            'update',
            'Quotation updated: ' . $quotation->quotation_no,
            ['before' => $changed['before'], 'after' => $changed['after']]
        );
    }

    public function logDelete(Quotation $quotation): void
    {
        $this->write(
            $quotation->quotation_id,
            'delete',
            'Quotation deleted: ' . $quotation->quotation_no,
            $quotation->toArray()
        );
    }

    public function logRevision(Quotation $quotation): void
    {
        $this->write(
            $quotation->quotation_id,
            'revision',
            'Quotation revised: ' . $quotation->quotation_no . ' (Revision ' . $quotation->revision_no . ')',
            $quotation->toArray()
        );
    }

    public function logItemsSync(int $quotationId, array $created, array $updated, array $deleted): void
    {
        if (empty($created) && empty($updated) && empty($deleted)) {
            return;
        }

        $this->write(
            $quotationId,
            'update',
            'Quotation items synced.',
            ['items_created' => $created, 'items_updated' => $updated, 'items_deleted' => $deleted]
        );
    }

    public function log(Quotation $quotation, string $action, string $description): void
    {
        $this->write(
            $quotation->quotation_id,
            $action,
            $description,
            $quotation->toArray()
        );
    }

    public function logCustom(int $quotationId, string $action, string $description, array $payload): void
    {
        $this->write($quotationId, $action, $description, $payload);
    }

    private function write(int $quotationId, string $action, string $description, array $payload): void
    {
        QuotationLog::create([
            'quotation_id' => $quotationId,
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
