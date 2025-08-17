<?php 
if (!defined('ABSPATH')) { exit; }

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class DepixTablesWP
{

    public function executeInitialTable()
    {
        try {
            global $wpdb;
            $table = $wpdb->prefix . 'depixwp_transactions';
            $charset = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                tx_id VARCHAR(64) NOT NULL UNIQUE,
                amount_cents BIGINT UNSIGNED NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'created',
                async TINYINT(1) NOT NULL DEFAULT 0,
                qr_copy_paste TEXT NULL,
                qr_image_url TEXT NULL,
                meta TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (status),
                INDEX (created_at)
            ) $charset;";

            return dbDelta($sql);
        } catch (Exception $e) {
            error_log('Erro ao criar tabela: ' . $e->getMessage());
        }
    }

    public function storeTransaction($data, $async, $amountInCents)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'depixwp_transactions';

        $tx_id = $data['qrId'] ?? $data['id'] ?? '';
        if ($tx_id === '') {
            error_log('[Depix][DB][Warn] storeTransaction sem id/qrId no payload.');
        }
        
        $sanitizedData = $this->sanitizeData($data, $amountInCents);

        $wpdb->insert(
            $table,
            [
                'tx_id' => $sanitizedData['tx_id'],
                'amount_cents' => $sanitizedData['amount'],
                'status' => $sanitizedData['status'],
                'async' => $async ? 1 : 0,
                'qr_copy_paste' => $sanitizedData['qr_copy'],
                'qr_image_url' => $sanitizedData['qr_img'],
                'meta' => $sanitizedData['metaJson'],
            ],
            [
                '%s',
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
            ]
        );
    }

    private function sanitizeData(array $data, int $amountInCents): array
    {
        error_log('[Depix][DB] Sanitizing data for transaction storage.' . print_r($data, true));
        $tx_id = $this->clamp(sanitize_text_field($data['id']), 64);
        $status = isset($data['status']) ? sanitize_key($data['status']) : 'created';
        $amount = isset($data['amountInCents']) ? (int)$data['amountInCents'] : (isset($data['valueInCents']) ? (int)$data['valueInCents'] : (int)$amountInCents);
        if ($amount < 0) { $amount = 0; }
        $qr_copy = isset($data['qrCopyPaste']) ? sanitize_textarea_field($data['qrCopyPaste']) : null;
        $qr_img  = isset($data['qrImageUrl']) ? esc_url_raw($data['qrImageUrl']) : null;
        $metaJson = isset($data['meta']) ? wp_json_encode($data['meta']) : wp_json_encode($data);
        $metaJson = $this->clamp((string)$metaJson, 65000);

        return [
            'tx_id' => $tx_id,
            'status' => $status,
            'amount' => $amount,
            'qr_copy' => $qr_copy,
            'qr_img' => $qr_img,
            'metaJson' => $metaJson,
        ];
    }

    private function clamp(string $value, int $max): string
    {
        return mb_substr($value, 0, $max);
    }

    public function getTransactionStatus(string $tx_id): ?string
    {
        global $wpdb;
        $table = $wpdb->prefix . 'depixwp_transactions';
        $row = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table WHERE tx_id = %s", $tx_id));
        return $row ?: null;
    }

    public function updateTransaction(array $data): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'depixwp_transactions';

        if (isset($data['qrId'])) {
            $data['id'] = $data['qrId'];
        }
        if (empty($data['id'])) {
            error_log('[Depix][DB][Warn] updateTransaction sem id/qrId.');
            return false;
        }

        $fields = [];
        $formats = [];

        if (!empty($data['status'])) {
            $fields['status'] = sanitize_key($data['status']);
            $formats[] = '%s';
        }
        if (isset($data['amountInCents']) || isset($data['valueInCents'])) {
            $fields['amount_cents'] = isset($data['amountInCents']) ? (int)$data['amountInCents'] : (int)$data['valueInCents'];
            $formats[] = '%d';
        }

        $fields['meta'] = $this->clamp((string)wp_json_encode($data), 65000);
        $formats[] = '%s';

        if (empty($fields)) {
            return false;
        }

        $lookup_id = $this->clamp(sanitize_text_field($data['id']), 64);
        $updated = $wpdb->update(
            $table,
            $fields,
            ['tx_id' => $lookup_id],
            $formats,
            ['%s']
        );
        if (!$updated && !empty($data['qrId']) && $data['qrId'] !== $data['id']) {
            $lookup_qr = $this->clamp(sanitize_text_field($data['qrId']), 64);
            $updated = $wpdb->update(
                $table,
                $fields,
                ['tx_id' => $lookup_qr],
                $formats,
                ['%s']
            );
        }
        if (!$updated) {
            error_log('[Depix][DB][Info] Nenhuma linha atualizada para tx_id='.$data['id']);
        }
        return (bool)$updated;    
    }

}