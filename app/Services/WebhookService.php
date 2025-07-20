<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Area;
use App\Models\Kanban;

class WebhookService
{
    /**
     * Send a webhook to Server #1.
     * This method is now designed to send a batch of data.
     *
     * @param string $type ('area' or 'kanban')
     * @param string $action ('batch_upsert' is recommended for batch operations)
     * @param array $dataArray An array of master record data (e.g., [Area::toArray(), Kanban::toArray()])
     * @return bool
     */
    public function sendMasterBatchUpdate(string $type, string $action, array $dataArray): bool
    {
        $webhooksConfig = config('app.server1_webhooks');

        if (!is_array($webhooksConfig)) {
            Log::error("WebhookService: 'app.server1_webhooks' configuration is missing or invalid.");
            return false;
        }

        $urlKey = "{$type}_url";
        $webhookUrl = $webhooksConfig[$urlKey] ?? null;
        $secretKey = $webhooksConfig['secret_key'] ?? null;

        if (empty($webhookUrl) || empty($secretKey)) {
            Log::error("WebhookService: Missing URL ({$urlKey}) or Secret Key for type: {$type}. Check .env and config/app.php.");
            return false;
        }

        $payload = [
            'action' => $action, // Misalnya 'batch_upsert'
            'data' => $dataArray, // Mengirimkan array data
            'secret_key' => $secretKey,
        ];

        try {
            $response = Http::timeout(30) // Tingkatkan timeout untuk batch besar
                            ->retry(3, 2000) // Tingkatkan retry delay
                            ->post($webhookUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success']) {
                    Log::info("Webhook batch sent successfully for {$type} {$action}. Count: " . count($dataArray) . ". Response: " . json_encode($responseData));
                    return true;
                } else {
                    $message = $responseData['message'] ?? 'No success key or success is false.';
                    Log::warning("Webhook batch to {$webhookUrl} failed for {$type} {$action}. Message: {$message}. Full response: " . $response->body());
                    return false;
                }
            } else {
                Log::error("Webhook batch to {$webhookUrl} failed for {$type} {$action}. HTTP Status: {$response->status()}. Response: " . $response->body());
                return false;
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("WebhookService: Connection error sending {$type} {$action} batch to {$webhookUrl}: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::error("WebhookService: General error sending {$type} {$action} batch to {$webhookUrl}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unsynced master records for a given type.
     * Records are those where updated_at is newer than synced_at, or synced_at is null.
     *
     * @param string $type 'area' or 'kanban'
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnsyncedMasterRecords(string $type)
    {
        if ($type === 'area') {
            return Area::whereRaw('updated_at > COALESCE(synced_at, "1970-01-01 00:00:00")')
                       ->orWhereNull('synced_at')
                       ->get();
        } elseif ($type === 'kanban') {
            // Pastikan relasi di-load jika diperlukan untuk toArray()
            return Kanban::with(['area', 'kanbanCategory']) // Load relasi untuk data lengkap
                         ->whereRaw('updated_at > COALESCE(synced_at, "1970-01-01 00:00:00")')
                         ->orWhereNull('synced_at')
                         ->get();
        }
        return collect();
    }

    /**
     * Update the synced_at timestamp for a given master record.
     *
     * @param string $type 'area' or 'kanban'
     * @param int $id The ID of the record to update.
     * @return bool
     */
    public function markMasterRecordAsSynced(string $type, int $id): bool
    {
        if ($type === 'area') {
            $record = Area::find($id);
        } elseif ($type === 'kanban') {
            $record = Kanban::find($id);
        } else {
            return false;
        }

        if ($record) {
            $record->synced_at = now();
            return $record->save();
        }
        return false;
    }
}