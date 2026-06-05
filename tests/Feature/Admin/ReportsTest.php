<?php

namespace Tests\Feature\Admin;

use App\Models\User;

class ReportsTest extends AdminTestCase
{
    public function test_admin_can_view_reports_page()
    {
        $response = $this->get('/admin/reports');
        $response->assertStatus(200);
    }

    public function test_admin_can_export_users_csv()
    {
        User::factory()->count(5)->create();
        
        $response = $this->get('/admin/reports/export/users');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }

    public function test_admin_can_export_transactions_csv()
    {
        $response = $this->get('/admin/reports/export/transactions');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
    }
}
