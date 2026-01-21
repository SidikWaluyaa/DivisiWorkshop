@props(['performers'])

<div class="space-y-4">
    @foreach($performers as $index => $user)
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-teal-50 transition-colors">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                {{ $index + 1 }}
            </div>
            <div>
                <div class="font-bold text-gray-800">{{ $user->name }}</div>
                <div class="text-xs text-gray-500">{{ $user->email }}</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-lg font-black text-teal-600">{{ $user->completed_count }}</div>
            <div class="text-xs text-gray-400 uppercase tracking-wide">Orders</div>
        </div>
    </div>
    @endforeach
</div>
