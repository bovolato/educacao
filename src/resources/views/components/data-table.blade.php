@props(['empty' => 'Nenhum registro encontrado.'])

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    @if(isset($filters))
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            {{ $filters }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            @if(isset($head))
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        {{ $head }}
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-gray-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(isset($footer))
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
