<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use eftec\bladeone\BladeOne;

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        $config = app('config.' . $file);

        if (!is_array($config)) {
            return $default;
        }

        foreach ($parts as $part) {
            if (!is_array($config) || !isset($config[$part])) {
                return $default;
            }
            $config = $config[$part];
        }

        return $config;
    }
}

if (!function_exists('response')) {
    /**
     * @param array<string, string> $headers
     */
    function response(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    /**
     * @param array<string, string> $headers
     */
    function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }
}

if (!function_exists('request')) {
    function request(?string $key = null, mixed $default = null): mixed
    {
        /** @var \Illuminate\Http\Request $instance */
        $instance = app('request');

        if (is_null($key)) {
            return $instance;
        }

        return $instance->input($key, $default);
    }
}

if (!function_exists('session')) {
    /**
     * @param array<string, mixed>|string|null $key
     */
    function session(array|string|null $key = null, mixed $default = null): mixed
    {
        /** @var \App\Core\Session $session */
        $session = app(\App\Core\Session::class);

        if (is_null($key)) {
            return $session;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $session->set((string)$k, $v);
            }
            return true;
        }

        return $session->get((string)$key, $default);
    }
}

if (!function_exists('view')) {
    /**
     * @param array<string, mixed> $data
     */
    function view(string $template, array $data = []): string
    {
        /** @var \eftec\bladeone\BladeOne $blade */
        $blade = app(BladeOne::class);
        return $blade->run($template, $data);
    }
}

if (!function_exists('back')) {
    function back(): RedirectResponse
    {
        /** @var string $referer */
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return redirect($referer);
    }
}

if (!function_exists('current_empresa')) {
    function current_empresa(): mixed
    {
        /** @var \App\Core\Session $session */
        $session = session();
        $id = $session->empresaId();
        return \App\Models\Empresa::find($id) ?: (object)['id' => 1, 'nome' => 'Kuamanga'];
    }
}

if (!function_exists('all_empresas')) {
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Empresa>
     */
    function all_empresas(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Empresa::where('status', 'ativo')->get();
    }
}

if (!function_exists('storage_uploads_path')) {
    function storage_uploads_path(string $relative = ''): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $path = $base . '/storage/uploads';
        if ($relative !== '') {
            $path .= '/' . trim($relative, '/');
        }
        return $path;
    }
}

if (!function_exists('upload_file')) {
    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string caminho relativo guardado (ex: employees/12/abc123.pdf)
     * @throws \RuntimeException
     */
    function upload_file(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $base = storage_uploads_path();
        $dir = storage_uploads_path($directory);

        if (!is_dir($dir)) {
            @mkdir($dir, 0o775, true);
        }

        if (!is_dir($dir)) {
            throw new \RuntimeException('Não foi possível criar o directório de upload.');
        }

        $extension = $file->getClientOriginalExtension();
        $filename = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . $extension : '');
        $relative = trim($directory, '/') . '/' . $filename;
        $dest = $base . '/' . $relative;

        $file->move(dirname($dest), basename($dest));

        return $relative;
    }
}

if (!function_exists('download_file')) {
    /**
     * @return \Illuminate\Http\Response
     */
    function download_file(string $relative, string $downloadName, bool $inline = false): \Illuminate\Http\Response
    {
        $base = storage_uploads_path();
        $full = $base . '/' . ltrim($relative, '/');

        if (!is_file($full)) {
            $_SESSION['flash_error'] = 'Ficheiro não encontrado.';
            return response('Ficheiro não encontrado.', 404);
        }

        $size = filesize($full);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($full);
        $mime = is_string($mime) ? $mime : 'application/octet-stream';

        return response(file_get_contents($full) ?: '', 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => $inline ? 'inline' : 'attachment; filename="' . $downloadName . '"',
            'Content-Length'      => (string) ($size !== false ? $size : 0),
        ]);
    }
}
