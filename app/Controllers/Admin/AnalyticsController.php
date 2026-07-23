<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AnalyticsService;

class AnalyticsController extends BaseController
{
    private function sidebarLinks(): array
    {
        return [
            ['label' => 'Início', 'icon' => 'bi-house-door', 'url' => '/admin/dashboard'],
            ['label' => 'Cursos', 'icon' => 'bi-book', 'url' => '/admin/dashboard/cursos'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/admin/dashboard/estudantes'],
            ['label' => 'Matrículas', 'icon' => 'bi-journal-check', 'url' => '/admin/dashboard/matriculas'],
            ['label' => 'Instrutores', 'icon' => 'bi-person-badge', 'url' => '/admin/dashboard/instrutores'],
            ['label' => 'Finanças', 'icon' => 'bi-cash-coin', 'url' => '/admin/dashboard/financas'],
            ['label' => 'Analytics', 'icon' => 'bi-graph-up-arrow', 'url' => '/admin/dashboard/analytics'],
            ['label' => 'Notificações', 'icon' => 'bi-bell', 'url' => '/admin/dashboard/notificacoes'],
            ['label' => 'Perfil', 'icon' => 'bi-person-circle', 'url' => '/admin/dashboard/perfil'],
        ];
    }

    public function index()
    {
        $user = service('auth')->user();
        $days = (int) ($this->request->getGet('days') ?? 30);
        $days = max(7, min(90, $days));

        $data = (new AnalyticsService())->dashboard($days);

        return view('pages/admin/analytics', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'days' => $days,
            'analytics' => $data,
        ]);
    }

    public function data()
    {
        $days = (int) ($this->request->getGet('days') ?? 30);
        $days = max(7, min(90, $days));

        return $this->response->setJSON((new AnalyticsService())->dashboard($days));
    }

    public function table()
    {
        $days = (int) ($this->request->getGet('days') ?? 30);
        $days = max(7, min(90, $days));
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 5);
        $perPage = max(5, min(50, $perPage));
        $section = strtolower(trim((string) ($this->request->getGet('section') ?? '')));

        $allowed = ['routes', 'clicks', 'entries', 'recent'];
        if (! in_array($section, $allowed, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Secção inválida.',
                'items' => [],
                'pagination' => ['page' => 1, 'per_page' => $perPage, 'total' => 0, 'total_pages' => 1],
            ]);
        }

        return $this->response->setJSON(
            (new AnalyticsService())->paginateList($section, $days, $page, $perPage)
        );
    }
}
