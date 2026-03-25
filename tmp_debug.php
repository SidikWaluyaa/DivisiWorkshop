<?php
$start = '2026-03-25 00:00:00';
$end = '2026-03-25 23:59:59';
$user = \App\Models\User::where('name', 'LIKE', '%Vina%')->first();

if (!$user) {
    echo "User Vina not found\n";
    exit;
}

$spkIds = \App\Models\CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
    ->where('cs_leads.cs_id', $user->id)
    ->where('cs_spk.status', '!=', \App\Models\CsSpk::STATUS_DRAFT)
    ->whereBetween('cs_spk.created_at', [$start, $end])
    ->pluck('cs_spk.id');

$incomingItems = \App\Models\CsSpkItem::whereIn('spk_id', $spkIds)->count();

$cohortPhones = \App\Models\CsSpk::join('cs_leads', 'cs_spk.cs_lead_id', '=', 'cs_leads.id')
    ->whereIn('cs_spk.id', $spkIds)
    ->pluck('cs_leads.customer_phone')
    ->filter()
    ->toArray();

echo "Items In: " . $incomingItems . "\n";
echo "Phones Count: " . count($cohortPhones) . "\n";
echo "Phones: " . implode(", ", $cohortPhones) . "\n";

$workOrdersQuery = \App\Models\WorkOrder::whereBetween('entry_date', [$start, $end])
    ->whereIn('customer_phone', $cohortPhones);

if (!empty($user->cs_code)) {
    $workOrdersQuery->where('spk_number', 'LIKE', '%-' . $user->cs_code);
} else {
    $workOrdersQuery->where('created_by', $user->id);
}

$wos = $workOrdersQuery->get();
echo "Matched WorkOrders: " . $wos->count() . "\n";

foreach ($wos as $wo) {
    echo "- " . $wo->spk_number . " (" . $wo->customer_phone . ")\n";
}

// Cek apakah ada WorkOrder untuk HP ini tapi entry_date-nya BUKAN hari ini
$wosOtherDays = \App\Models\WorkOrder::whereIn('customer_phone', $cohortPhones)
    ->whereNotIn('id', $wos->pluck('id'))
    ->get();
echo "WorkOrders for these phones on OTHER days: " . $wosOtherDays->count() . "\n";
foreach ($wosOtherDays as $wod) {
    echo "- " . $wod->spk_number . " (" . $wod->entry_date . ")\n";
}
