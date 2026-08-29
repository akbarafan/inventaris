<?php

namespace App\Support;

class SatuanParser
{
    private const MAP = [
        'pcs' => 'pcs', 'buah' => 'pcs', 'biji' => 'pcs', 'bh' => 'pcs', 'unit' => 'pcs',
        'dus' => 'dus', 'kardus' => 'dus', 'karton' => 'dus', 'box' => 'dus', 'kotak' => 'dus',
        'pack' => 'pack', 'pak' => 'pack', 'bendel' => 'pack', 'bendle' => 'pack', 'bundle' => 'pack',
        'lusin' => 'lusin', 'lsn' => 'lusin', 'dozen' => 'lusin',
        'kodi' => 'kodi',
        'rim' => 'rim',
        'gross' => 'gross',
        'set' => 'set', 'stel' => 'set',
        'lembar' => 'lembar', 'lbr' => 'lembar',
        'roll' => 'roll', 'gulung' => 'roll',
        'meter' => 'meter', 'm' => 'meter',
        'karung' => 'karung', 'sak' => 'karung',
        'kaleng' => 'kaleng',
        'botol' => 'botol', 'btl' => 'botol',
        'batang' => 'batang', 'btg' => 'batang',
    ];

    public static function parse(string $raw): array
    {
        $s = trim($raw);
        $isApprox = false;

        if (preg_match('/^(Lebih dari|lebih dari|±|~|>)\s*(.*)$/u', $s, $m)) {
            $isApprox = true;
            $s = trim($m[2]);
        }

        if ($s === '' || $s === '-' || $s === '—') {
            return ['jumlah' => 1, 'satuan' => 'pcs', 'is_approx' => true, 'raw' => $raw];
        }

        if (preg_match('/^(\d+(?:[.,]\d+)?)\s*([a-zA-Z]+)?$/u', $s, $m)) {
            $jumlah = (int) str_replace(',', '', $m[1]);
            $satuanRaw = strtolower(trim($m[2] ?? 'pcs'));
            $satuan = self::MAP[$satuanRaw] ?? ($satuanRaw !== '' ? $satuanRaw : 'pcs');
            return ['jumlah' => max(1, $jumlah), 'satuan' => $satuan, 'is_approx' => $isApprox, 'raw' => $raw];
        }

        if (preg_match('/(\d+)/', $s, $m)) {
            $jumlah = (int) $m[1];
            $after = strtolower(trim(str_replace($m[1], '', $s)));
            $satuanRaw = preg_replace('/[^a-z]/', '', $after);
            $satuan = self::MAP[$satuanRaw] ?? ($satuanRaw !== '' ? $satuanRaw : 'pcs');
            return ['jumlah' => max(1, $jumlah), 'satuan' => $satuan, 'is_approx' => true, 'raw' => $raw];
        }

        return ['jumlah' => 1, 'satuan' => 'pcs', 'is_approx' => true, 'raw' => $raw];
    }

    public static function normalize(string $satuan): string
    {
        $key = strtolower(trim($satuan));
        return self::MAP[$key] ?? ($key !== '' ? $key : 'pcs');
    }
}
