<?php

namespace App\Services;

use App\Models\PerformaInvoice;
use App\Models\PerformaInvoiceLog;

class PerformaInvoiceLogService
{
    public function logCreate(PerformaInvoice $invoice): void
    {
        $this->write(
            $invoice->invoice_id,
            'create',
            'Performa Invoice created: ' . $invoice->invoice_no,
            $invoice->toArray()
        );
    }

    public function logUpdate(PerformaInvoice $invoice, array $before, array $after, array $created = [], array $updated = [], array $deleted = []): void
    {
        $changed = $this->diff($before, $after);

        if (empty($changed['before']) && empty($created) && empty($updated) && empty($deleted)) {
            return;
        }

        $payload = [
            'before' => $changed['before'],
            'after' => $changed['after']
        ];

        if (!empty($created) || !empty($updated) || !empty($deleted)) {
            $payload['items_created'] = $created;
            $payload['items_updated'] = $updated;
            $payload['items_deleted'] = $deleted;
        }

        $this->write(
            $invoice->invoice_id,
            'update',
            'Performa Invoice updated: ' . $invoice->invoice_no,
            $payload
        );
    }

    public function logDelete(PerformaInvoice $invoice): void
    {
        $this->write(
            $invoice->invoice_id,
            'delete',
            'Performa Invoice deleted: ' . $invoice->invoice_no,
            $invoice->toArray()
        );
    }

    public function logItemsSync(int $invoiceId, array $created, array $updated, array $deleted): void
    {
        if (empty($created) && empty($updated) && empty($deleted)) {
            return;
        }

        $this->write(
            $invoiceId,
            'update',
            'Performa Invoice items synced.',
            [
                'items_created' => $created,
                'items_updated' => $updated,
                'items_deleted' => $deleted
            ]
        );
    }

    public function log(PerformaInvoice $invoice, string $action, string $description): void
    {
        $this->write(
            $invoice->invoice_id,
            $action,
            $description,
            $invoice->toArray()
        );
    }

    public function logCustom(int $invoiceId, string $action, string $description, array $payload): void
    {
        $this->write($invoiceId, $action, $description, $payload);
    }

    private function write(int $invoiceId, string $action, string $description, array $payload): void
    {
        PerformaInvoiceLog::create([
            'invoice_id' => $invoiceId,
            'action' => $action,
            'description' => $description,
            'payload' => json_encode($payload),
            'performed_by' => getCreatedBy(),
            'performed_at' => now(),
        ]);
    }

    private function diff(array $before, array $after): array
    {
        $skip = ['updated_at', 'created_at', 'updated_by'];

        $changedBefore = [];
        $changedAfter = [];

        foreach ($after as $key => $newVal) {
            if (in_array($key, $skip)) {
                continue;
            }

            $oldVal = $before[$key] ?? null;

            if ((string) $oldVal !== (string) $newVal) {
                $changedBefore[$key] = $oldVal;
                $changedAfter[$key] = $newVal;
            }
        }

        return [
            'before' => $changedBefore,
            'after' => $changedAfter
        ];
    }
}
