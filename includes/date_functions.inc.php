<?php 

function deutscheMonate(): array
{
    return [
        1 => 'Januar',
        2 => 'Februar',
        3 => 'März',
        4 => 'April',
        5 => 'Mai',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'August',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember'
    ];
}

function formatiereMonatJahrDeutsch(DateTimeInterface $datum): string
{
    $monate = [
        1 => 'Januar',
        2 => 'Februar',
        3 => 'März',
        4 => 'April',
        5 => 'Mai',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'August',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember'
    ];

    $monat = $monate[(int)$datum->format('n')] ?? '';
    $jahr = $datum->format('Y');

    return "$monat $jahr";
}

function formatiereDatumDeutsch(string $datum): string
{
    $datumObjekt = DateTime::createFromFormat('Y-m-d', $datum);

    return $datumObjekt
        ? $datumObjekt->format('d.m.Y')
        : $datum;
}

function formatiereDatumDeutschLang(string $datum): string
{
    $dt = DateTime::createFromFormat('Y-m-d', $datum);

    if (!$dt) {
        return '';
    }

    $monate = deutscheMonate();

    $tag = $dt->format('j');
    $monat = $monate[(int)$dt->format('n')] ?? '';
    $jahr = $dt->format('Y');

    return "$tag. $monat $jahr";
}