<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AutoLoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Evita loop: não tenta auto-login nas rotas de autenticação
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, 'auth/')) {
            return;
        }

        // Se não está logado, tenta o auto-login
        $session = session();
        if (!$session->get('logged_in')) {
            $auth = new \App\Controllers\Auth();
            $auth->autoLogin();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer
    }
}