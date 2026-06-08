<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bobot sub-indikator KPI v2 (komposit)
    |--------------------------------------------------------------------------
    | KPI% = quality*Quality% + ontime*OnTime% + completion*Completion%
    | Total bobot sebaiknya = 1.0
    */
    'weights' => [
        'quality'    => 0.5,
        'ontime'     => 0.3,
        'completion' => 0.2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bobot prioritas untuk rata-rata mutu (Quality)
    |--------------------------------------------------------------------------
    | Tugas prioritas tinggi berkontribusi lebih besar pada skor mutu.
    */
    'priority_weight' => [
        'high'   => 1.5,
        'medium' => 1.0,
        'low'    => 0.75,
    ],

    /*
    |--------------------------------------------------------------------------
    | Penalti revisi pada skor mutu per tugas
    |--------------------------------------------------------------------------
    | mutu_tugas = max(0, 10 - revision_penalty * jumlah_revisi)
    */
    'revision_penalty' => 2,

    /*
    |--------------------------------------------------------------------------
    | Target KPI default (persen)
    |--------------------------------------------------------------------------
    */
    'target' => 85,

    /*
    |--------------------------------------------------------------------------
    | Periode perhitungan
    |--------------------------------------------------------------------------
    | 'monthly' (default). Disediakan untuk pengembangan periode lain nanti.
    */
    'period' => 'monthly',

    /*
    |--------------------------------------------------------------------------
    | Ambang pita warna (band)
    |--------------------------------------------------------------------------
    */
    'bands' => [
        'excellent' => 90, // >= 90
        'good'      => 75, // >= 75
        'fair'      => 60, // >= 60  (di bawah ini = poor)
    ],
];
