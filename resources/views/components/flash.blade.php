<div class="flash-messages" aria-live="polite">
@foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'warning' => 'border-amber-200 bg-amber-50 text-amber-900', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $type => $classes)
    @if(session($type))<div role="status" class="my-5 rounded-lg border px-4 py-3 text-sm leading-6 {{ $classes }}">{{ session($type) }}</div>@endif
@endforeach
@if($errors->any())<div role="alert" class="my-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Periksa kembali kolom yang ditandai di bawah.</div>@endif
</div>
