<?php

namespace App;

require_once '../MinhaFramework/Init/Bootstrap.php';

use MF\BootStrap;

class Route extends BootStrap
{
    function InitRoutes()
    {
        $routes['login'] = array(
            'route' => '/',
            'controller' => 'AuthController',
            'action' => 'index',
            'middleware' => ''
        );

        $routes['autenticar'] = array(
            'route' => '/autenticar',
            'controller' => 'AuthController',
            'action' => 'autenticar',
            'middleware' => ''
        );

        $routes['sair'] = array(
            'route' => '/sair',
            'controller' => 'AuthController',
            'action' => 'sair',
            'middleware' => ''
        );

        $routes['dashboard'] = array(
            'route' => '/dashboard',
            'controller' => 'DashboardController',
            'action' => 'index',
            'middleware' => 'AuthMiddleware'
        );

        $routes['livros'] = array(
            'route' => '/livros',
            'controller' => 'LivrosController',
            'action' => 'index',
            'middleware' => 'AuthMiddleware'
        );

        $routes['livros_cadastrar'] = array(
            'route' => '/livros/cadastrar',
            'controller' => 'LivrosController',
            'action' => 'cadastrar',
            'middleware' => 'AuthMiddleware'
        );

        $routes['livros_editar'] = array(
            'route' => '/livros/editar',
            'controller' => 'LivrosController',
            'action' => 'editar',
            'middleware' => 'AuthMiddleware'
        );

        $routes['livros_excluir'] = array(
            'route' => '/livros/excluir',
            'controller' => 'LivrosController',
            'action' => 'excluir',
            'middleware' => 'AuthMiddleware'
        );

        $routes['usuarios'] = array(
            'route' => '/usuarios',
            'controller' => 'UsuariosController',
            'action' => 'index',
            'middleware' => 'AuthMiddleware'
        );

        $routes['usuarios_cadastrar'] = array(
            'route' => '/usuarios/cadastrar',
            'controller' => 'UsuariosController',
            'action' => 'cadastrar',
            'middleware' => 'AuthMiddleware'
        );

        $routes['usuarios_editar'] = array(
            'route' => '/usuarios/editar',
            'controller' => 'UsuariosController',
            'action' => 'editar',
            'middleware' => 'AuthMiddleware'
        );

        $routes['usuarios_excluir'] = array(
            'route' => '/usuarios/excluir',
            'controller' => 'UsuariosController',
            'action' => 'excluir',
            'middleware' => 'AuthMiddleware'
        );

        $routes['emprestimos'] = array(
            'route' => '/emprestimos',
            'controller' => 'EmprestimosController',
            'action' => 'index',
            'middleware' => 'AuthMiddleware'
        );

        $routes['emprestimos_novo'] = array(
            'route' => '/emprestimos/novo',
            'controller' => 'EmprestimosController',
            'action' => 'novo',
            'middleware' => 'AuthMiddleware'
        );

        $routes['emprestimos_registrar'] = array(
            'route' => '/emprestimos/registrar',
            'controller' => 'EmprestimosController',
            'action' => 'registrar',
            'middleware' => 'AuthMiddleware'
        );

        $routes['emprestimos_devolver'] = array(
            'route' => '/emprestimos/devolver',
            'controller' => 'EmprestimosController',
            'action' => 'devolver',
            'middleware' => 'AuthMiddleware'
        );

        $routes['historico'] = array(
            'route' => '/historico',
            'controller' => 'HistoricoController',
            'action' => 'index',
            'middleware' => 'AuthMiddleware'
        );

        $this->setRoutes($routes);
    }
}