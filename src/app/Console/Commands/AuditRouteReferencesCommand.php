<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AuditRouteReferencesCommand extends Command
{
    protected $signature = 'route:audit-views
                            {--path=resources/views : Caminho base (relativo ao projeto) para varrer arquivos .blade.php}
                            {--also-controllers : Incluir também app/Http/Controllers (uso de ->route)}';

    protected $description = 'Verifica se os nomes usados em route(\'...\') nas views existem nas rotas registradas';

    public function handle(): int
    {
        $base = base_path();
        $viewRoot = $base.DIRECTORY_SEPARATOR.trim($this->option('path'), '/\\');

        if (! File::isDirectory($viewRoot)) {
            $this->error('Diretório não encontrado: '.$viewRoot);

            return self::FAILURE;
        }

        $pattern = "/route\\(\\s*['\"]([^'\"]+)['\"]/";

        $found = [];
        foreach (File::allFiles($viewRoot) as $file) {
            if (! Str::endsWith($file->getFilename(), '.blade.php')) {
                continue;
            }
            $content = File::get($file->getPathname());
            if (preg_match_all($pattern, $content, $m)) {
                foreach ($m[1] as $name) {
                    $found[$name][] = str_replace($base.DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        if ($this->option('also-controllers')) {
            $ctrlRoot = $base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers';
            if (File::isDirectory($ctrlRoot)) {
                foreach (File::allFiles($ctrlRoot) as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }
                    $content = File::get($file->getPathname());
                    if (preg_match_all("/->route\\(\\s*['\"]([^'\"]+)['\"]/", $content, $m2)) {
                        foreach ($m2[1] as $name) {
                            if (str_contains($name, '$')) {
                                continue;
                            }
                            $found[$name][] = str_replace($base.DIRECTORY_SEPARATOR, '', $file->getPathname());
                        }
                    }
                }
            }
        }

        ksort($found);

        $missing = [];
        foreach (array_keys($found) as $name) {
            if ($name === '' || str_contains($name, '$')) {
                continue;
            }
            if (! Route::has($name)) {
                $missing[$name] = array_unique($found[$name]);
            }
        }

        $this->info('Referências únicas encontradas: '.count($found));
        if ($missing === []) {
            $this->info('Nenhum nome de rota órfão (todas as referências estáticas existem em Route::has).');

            return self::SUCCESS;
        }

        $this->error('Nomes referenciados mas não registrados:');
        foreach ($missing as $name => $files) {
            $this->line('  <fg=red>'.$name.'</>');
            foreach ($files as $f) {
                $this->line('    — '.$f);
            }
        }

        $this->newLine();
        $this->warn('Se você alterou routes/web.php recentemente, rode: php artisan route:clear (e em produção regenere route:cache após o deploy).');

        return self::FAILURE;
    }
}
