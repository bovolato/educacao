<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documento — {{ $doc->tipo_documento }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; color: #111; }
        h1 { font-size: 1.25rem; margin-bottom: 1.5rem; }
        .meta { font-size: 0.875rem; color: #444; margin-bottom: 2rem; }
        .box { border: 1px solid #ddd; border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 1rem;">
        <a href="{{ route('escola.documentos.index') }}" style="color: #4f46e5;">← Voltar à lista</a>
        <button type="button" onclick="window.print()" style="margin-left: 1rem; padding: 0.5rem 1rem; cursor: pointer;">Imprimir</button>
    </div>

    <h1>{{ str_replace('_', ' ', ucfirst($doc->tipo_documento)) }}</h1>
    <p class="meta">
        Emitido em {{ $doc->emitido_em?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
        @if($doc->emitidoPor)
            · {{ $doc->emitidoPor->name }}
        @endif
    </p>

    <div class="box">
        <p><strong>Aluno(a):</strong> {{ $doc->aluno?->usuario?->nome ?? '—' }}</p>
        @if($doc->matricula)
            <p><strong>Escola:</strong> {{ $doc->matricula->escola?->nome ?? '—' }}</p>
            <p><strong>Turma:</strong> {{ $doc->matricula->turma?->nome ?? '—' }}
                @if($doc->matricula->turma?->serie)
                    — {{ $doc->matricula->turma->serie->nome }}
                @endif
            </p>
            <p><strong>Ano letivo:</strong> {{ $doc->matricula->anoLetivo?->descricao ?? '—' }}</p>
            <p><strong>Situação da matrícula:</strong> {{ ucfirst($doc->matricula->situacao) }}</p>
        @endif
    </div>

    <p style="font-size: 0.875rem; color: #666;">
        Documento emitido pelo sistema SIGEM para fins escolares. A validade depende da conferência junto à secretaria da unidade.
    </p>
</body>
</html>
