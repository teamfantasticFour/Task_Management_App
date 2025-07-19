<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ContactController;
use App\Controllers\KanbanController;
use App\Controllers\TaskController;
use App\Controllers\TeamController;
use App\Controllers\BoardController;
use App\Controllers\MemberController;
use App\Controllers\StatusController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;



use Slim\App;

return function (App $app) {
    session_start();

    $app->get('/', [HomeController::class, 'index']);
    $app->get('/login', [AuthController::class, 'showLoginForm'])->setName('login');
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/register', [AuthController::class, 'showRegisterForm']);
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/logout', [AuthController::class, 'logout']);
    

    // Grup route yang dilindungi middleware login
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/dashboard', [KanbanController::class, 'index']);
        $group->post('/contact/submit', [ContactController::class, 'submit']);

        $group->get('/kanban/create', [KanbanController::class, 'showCreateForm']);
        $group->post('/kanban/store', [KanbanController::class, 'store']);
        $group->post('/kanban/update-status', [KanbanController::class, 'updateStatus']);
        $group->get('/kanban/edit/{id}', [KanbanController::class, 'edit'])->setName('kanban.edit');
        $group->post('/kanban/update/{id}', [KanbanController::class, 'update'])->setName('kanban.update');
        $group->get('/kanban/members/{team_id}', [KanbanController::class, 'getMembers']);
        $group->get('/kanban/list/{status}', [KanbanController::class, 'listByStatus'])->setName('kanban.list');


        $group->get('/tasks', [TaskController::class, 'index'])->setName('task.index');
        $group->get('/tasks/create', [TaskController::class, 'createForm'])->setName('task.create');
        $group->post('/tasks/create', [TaskController::class, 'store'])->setName('task.store');
        $group->get('/tasks/edit/{id}', [TaskController::class, 'editForm'])->setName('task.edit');
        $group->post('/tasks/edit/{id}', [TaskController::class, 'update'])->setName('task.update');
        $group->get('/tasks/delete/{id}', [TaskController::class, 'delete'])->setName('task.delete');
        $group->get('/tasks/detail/{id}', [TaskController::class, 'detail'])->setName('task.detail');

        $group->get('/teams', [TeamController::class, 'index']);
        $group->get('/teams/create', [TeamController::class, 'createForm']);
        $group->post('/teams/create', [TeamController::class, 'store']);
        $group->get('/teams/edit/{id}', [TeamController::class, 'editForm']);
        $group->post('/teams/edit/{id}', [TeamController::class, 'update']);
        $group->get('/teams/delete/{id}', [TeamController::class, 'delete']);
        $group->get('/teams/{id}/members', [TeamController::class, 'members']);
        $group->post('/teams/{id}/members/add', [TeamController::class, 'addMember']);
        $group->get('/teams/{id}/members/delete/{member_id}', [TeamController::class, 'deleteMember']);

        $group->get('/boards', [BoardController::class, 'index']);
        $group->post('/boards/create', [BoardController::class, 'store']);
        $group->get('/boards/delete/{id}', [BoardController::class, 'delete']);

        $group->get('/members', [MemberController::class, 'index']);
        $group->get('/members/create', [MemberController::class, 'createForm']);
        $group->post('/members/store', [MemberController::class, 'store']);
        $group->get('/members/edit/{id}', [MemberController::class, 'editForm']);
        $group->post('/members/update/{id}', [MemberController::class, 'update']);
        $group->get('/members/delete/{id}', [MemberController::class, 'delete']);

        $group->get('/statuses', [StatusController::class, 'index'])->setName('statuses.index');
        $group->post('/statuses/create', [StatusController::class, 'store'])->setName('statuses.store');
        $group->get('/statuses/edit/{id}', [StatusController::class, 'editForm'])->setName('statuses.editForm');
        $group->post('/statuses/update/{id}', [StatusController::class, 'update'])->setName('statuses.update');
        $group->get('/statuses/delete/{id}', [StatusController::class, 'delete'])->setName('statuses.delete');
        

     })->add(AuthMiddleware::class); // Tambahkan middleware di sini
};
