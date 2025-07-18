<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class AuthController
{
    protected $view;
    protected $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    public function showLoginForm(Request $request, Response $response): Response
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'auth/login.twig', [
            'flash' => $flash
        ]);
    }

    public function showRegisterForm(Request $request, Response $response): Response
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'auth/register.twig', [
            'flash' => $flash
        ]);
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $firstName = trim($data['first_name'] ?? '');
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';

        if (empty($firstName) || empty($username) || empty($password) || $password !== $confirm) {
            $_SESSION['flash'] = 'Data tidak valid atau password tidak cocok.';
            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        $existing = $this->db->get('tbl_users', '*', ['username' => $username]);
        if ($existing) {
            $_SESSION['flash'] = 'Username sudah digunakan.';
            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        $this->db->insert('tbl_users', [
            'first_name' => $firstName,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['flash'] = 'Registrasi berhasil. Silakan login.';
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        $user = $this->db->get('tbl_users', '*', ['username' => $username]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['first_name']
            ];
            $_SESSION['flash'] = 'Login berhasil!';
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $_SESSION['flash'] = 'Username atau password salah.';
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        session_destroy();
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
