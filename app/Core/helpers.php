<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use eftec\bladeone\BladeOne;

if (!function_exists('app')) {
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract);
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        $config = app('config.' . $file);
        
        if (!$config) {
            return $default;
        }

        foreach ($parts as $part) {
            if (!isset($config[$part])) {
                return $default;
            }
            $config = $config[$part];
        }

        return $config;
    }
}

if (!function_exists('response')) {
    function response($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }
}

if (!function_exists('request')) {
    function request($key = null, $default = null)
    {
        $instance = app('request');

        if (is_null($key)) {
            return $instance;
        }

        return $instance->input($key, $default);
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null)
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
    function view($template, $data = [])
    {
        $blade = app(BladeOne::class);
        return $blade->run($template, $data);
    }
}

if (!function_exists('back')) {
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return redirect($referer);
    }
}

if (!function_exists('current_empresa')) {
    function current_empresa()
    {
        $id = session()->empresaId();
        return \App\Models\Empresa::find($id) ?: (object)['id' => 1, 'nome' => 'Kuamanga'];
    }
}

if (!function_exists('all_empresas')) {
    function all_empresas()
    {
        return \App\Models\Empresa::where('status', 'ativo')->get();
    }
}
