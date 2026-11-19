<?php

namespace App\Services;

class ItemReuseAdjustmentService
{
    public static function normalize(array $data): array
    {
        $quantity = max(0, (int) ($data['quantity'] ?? 0));
        $reuseQuantity = max(0, (int) ($data['reuse_quantity'] ?? 0));
        $scrapQuantity = max(0, (int) ($data['scrap_quantity'] ?? 0));
        $transferredQuantity = max(0, (int) ($data['transferred_quantity'] ?? 0));
        $weight = max(0, (float) ($data['weight'] ?? 0));
        $reuseWeight = max(0, (float) ($data['reuse_weight'] ?? 0));
        $scrapWeight = max(0, (float) ($data['scrap_weight'] ?? 0));
        // Ensure reuse + scrap + transferred do not exceed totals. Prefer visible reuse/scrap as-is.
        $reuseQuantity = min($reuseQuantity, $quantity);
        $scrapQuantity = min($scrapQuantity, max(0, $quantity - $reuseQuantity));
        $transferredQuantity = min($transferredQuantity, max(0, $quantity - $reuseQuantity - $scrapQuantity));

        $reuseWeight = min($reuseWeight, $weight);
        $scrapWeight = min($scrapWeight, max(0.0, $weight - $reuseWeight));

        return [
            'quantity' => $quantity,
            'reuse_quantity' => $reuseQuantity,
            'scrap_quantity' => $scrapQuantity,
            'transferred_quantity' => $transferredQuantity,
            'weight' => $weight,
            'reuse_weight' => $reuseWeight,
            'scrap_weight' => $scrapWeight,
            'net_quantity' => max(0, $quantity - ($reuseQuantity + $scrapQuantity + $transferredQuantity)),
            'net_weight' => max(0.0, $weight - ($reuseWeight + $scrapWeight)),
        ];
    }
}
