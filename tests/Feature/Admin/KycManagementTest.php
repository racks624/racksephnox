<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\KycDocument;

class KycManagementTest extends AdminTestCase
{
    public function test_admin_can_view_kyc_list()
    {
        KycDocument::create([
            'user_id' => User::factory()->create()->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        
        $response = $this->get('/admin/kyc');
        $response->assertStatus(200);
    }

    public function test_admin_can_approve_kyc()
    {
        $user = User::factory()->create(['is_verified' => false]);
        $document = KycDocument::create([
            'user_id' => $user->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/kyc/{$document->id}/approve");
        
        $response->assertRedirect();
        $document->refresh();
        $user->refresh();
        $this->assertEquals('verified', $document->status);
        $this->assertTrue($user->is_verified);
    }

    public function test_admin_can_reject_kyc()
    {
        $user = User::factory()->create();
        $document = KycDocument::create([
            'user_id' => $user->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/kyc/{$document->id}/reject", [
            'reason' => 'Document is blurry',
        ]);
        
        $response->assertRedirect();
        $document->refresh();
        $this->assertEquals('rejected', $document->status);
    }
}
