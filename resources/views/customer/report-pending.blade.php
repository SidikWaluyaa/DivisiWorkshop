<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sedang Diproses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 text-center text-slate-800">

    <div class="max-w-md">
        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl">
            <div class="text-6xl mb-6">🛠️</div>
            <h1 class="text-2xl font-extrabold mb-4 uppercase tracking-tight">Laporan Dalam Proses</h1>
            <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                Halo Kak <b>{{ $order->customer_name }}</b>, tim workshop kami sedang memproses dokumentasi hasil akhir untuk nomor SPK <b>{{ $order->spk_number }}</b>.
            </p>
            
            <div class="bg-amber-100/50 border border-amber-200 text-amber-700 p-5 rounded-2xl mb-8">
                <p class="text-sm font-bold">Status Saat Ini:</p>
                <p class="text-lg font-black uppercase tracking-widest">{{ $order->status->label() }}</p>
            </div>

            <p class="text-sm font-medium text-slate-400">Silahkan cek kembali beberapa saat lagi ya kak!</p>
        </div>
        
        <p class="mt-8 text-[10px] font-bold text-slate-300 uppercase tracking-[4px]">ShoeWorkshop.id</p>
    </div>

</body>
</html>
