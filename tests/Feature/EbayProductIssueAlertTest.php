<?php

namespace Tests\Feature;

use App\Mail\EbayProductIssueAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EbayProductIssueAlertTest extends TestCase
{
    use DatabaseTransactions, \Illuminate\Foundation\Testing\WithoutMiddleware;

    protected User $adminUser;
    protected string $tempFolderName;
    protected string $tempFolderPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_ebay_alert_test@example.com'],
            [
                'name' => 'Admin Ebay Tester',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        $this->tempFolderName = 'test_issue_alert_' . uniqid();
        $this->tempFolderPath = public_path('ebay-product/' . $this->tempFolderName);
        File::ensureDirectoryExists($this->tempFolderPath);
        File::put($this->tempFolderPath . '/metadata.json', json_encode([
            'product_no' => '2297',
            'folder_name' => 'Both',
            'category' => 'Laptops',
            'location' => 'F1',
            'serial_number' => 'SN1783868108',
            'quantity' => 10,
            'blinking' => false,
        ]));
    }

    protected function tearDown(): void
    {
        if (File::isDirectory($this->tempFolderPath)) {
            File::deleteDirectory($this->tempFolderPath);
        }

        parent::tearDown();
    }

    public function test_send_issue_alert_sends_email_and_returns_mail_sent_true(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->adminUser)->postJson(
            route('admin.ebay-products.send-issue-alert', $this->tempFolderName),
            [
                'recipient' => 'sal@it-investmentrecoveries.com',
                'subject' => '[Product Issue Alert] Product #2297 - Both',
                'message' => 'Screen hinges are loose.',
                'blinking' => true,
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'mail_sent' => true,
                'blinking' => true,
            ]);

        Mail::assertSent(EbayProductIssueAlert::class, function (EbayProductIssueAlert $mail) {
            return $mail->hasTo('sal@it-investmentrecoveries.com')
                && $mail->customSubject === '[Product Issue Alert] Product #2297 - Both'
                && str_contains($mail->render(), 'Screen hinges are loose.');
        });
    }

    public function test_send_issue_alert_returns_error_when_mail_transport_fails(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->with(['sal@it-investmentrecoveries.com'])
            ->andThrow(new \RuntimeException('SMTP connection refused'));

        $response = $this->actingAs($this->adminUser)->postJson(
            route('admin.ebay-products.send-issue-alert', $this->tempFolderName),
            [
                'recipient' => 'sal@it-investmentrecoveries.com',
                'subject' => '[Product Issue Alert] Product #2297 - Both',
                'message' => 'Broken port.',
                'blinking' => true,
            ]
        );

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'mail_sent' => false,
                'mail_error' => 'SMTP connection refused',
            ]);
    }

    public function test_send_issue_alert_rejects_invalid_recipient(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->adminUser)->postJson(
            route('admin.ebay-products.send-issue-alert', $this->tempFolderName),
            [
                'recipient' => 'not-an-email',
                'subject' => 'Test',
                'message' => 'Test issue',
                'blinking' => true,
            ]
        );

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'mail_sent' => false,
            ]);

        Mail::assertNothingSent();
    }

    public function test_mailable_falls_back_to_smtp_username_when_from_address_is_default_example(): void
    {
        config([
            'mail.from.address' => 'hello@example.com',
            'mail.mailers.smtp.username' => 'customerservice@it-investmentrecoveries.com',
        ]);

        $mailable = new EbayProductIssueAlert(
            ['product_no' => '2297', 'product_name' => 'Both', 'folder' => $this->tempFolderName],
            'Issue details'
        );

        $mailable->build();

        $this->assertTrue($mailable->hasFrom('customerservice@it-investmentrecoveries.com'));
    }
}
