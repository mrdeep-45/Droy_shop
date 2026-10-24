<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\WhatsappController;

class ShopNotificationService
{
    /**
     * Send a multi-channel notification (Database, Email, WhatsApp).
     *
     * @param string $userType 'customer', 'vendor', 'delivery', 'admin'
     * @param int $userId ID of customer, vendor, delivery staff, or admin
     * @param string $title
     * @param string $message
     * @param array $options [
     *    'type' => 'order_placed',
     *    'icon' => 'bx bx-package',
     *    'color' => 'primary',
     *    'link' => '/shop/order/show/1',
     *    'data' => [...],
     *    'send_email' => true,
     *    'email' => 'customer@example.com',
     *    'send_wa' => true,
     *    'phone' => '9876543210'
     * ]
     */
    public static function send(string $userType, int $userId, string $title, string $message, array $options = []): void
    {
        try {
            // 1. In-App Database Notification
            DB::table('tbl_shop_notifications')->insert([
                'user_type'  => $userType,
                'user_id'    => $userId,
                'title'      => $title,
                'message'    => $message,
                'type'       => $options['type'] ?? 'general',
                'icon'       => $options['icon'] ?? 'bx bx-bell',
                'color'      => $options['color'] ?? 'primary',
                'data'       => isset($options['data']) ? json_encode($options['data']) : null,
                'link'       => $options['link'] ?? null,
                'is_read'    => 0,
                'created_at' => now(),
            ]);

            // 2. Email Notification (if email provided or enabled)
            $email = $options['email'] ?? null;
            if (!$email && $userType === 'customer') {
                $email = DB::table('shop_customers')->where('customer_id', $userId)->value('email');
            } elseif (!$email && $userType === 'vendor') {
                $email = DB::table('shop_vendors')->where('vendor_id', $userId)->value('email');
            } elseif (!$email && $userType === 'delivery') {
                $email = DB::table('shop_delivery_staff')->where('delivery_staff_id', $userId)->value('email');
            }

            if (!empty($options['send_email']) && !empty($email)) {
                self::sendEmailNotification($email, $title, $message, $options['link'] ?? null);
            }

            // 3. WhatsApp Notification (if phone provided or enabled)
            $phone = $options['phone'] ?? null;
            if (!$phone && $userType === 'customer') {
                $phone = DB::table('shop_customers')->where('customer_id', $userId)->value('phone');
            } elseif (!$phone && $userType === 'delivery') {
                $phone = DB::table('shop_delivery_staff')->where('delivery_staff_id', $userId)->value('phone');
            } elseif (!$phone && $userType === 'vendor') {
                $phone = DB::table('shop_vendors')->where('vendor_id', $userId)->value('phone');
            }

            if (!empty($options['send_wa']) && !empty($phone)) {
                self::sendWhatsAppNotification($phone, "*{$title}*\n\n{$message}");
            }
        } catch (\Throwable $e) {
            Log::error('ShopNotificationService send error: ' . $e->getMessage());
        }
    }

    /**
     * Helper to dispatch simple email
     */
    public static function sendEmailNotification(string $recipientEmail, string $subject, string $bodyText, ?string $actionUrl = null): void
    {
        try {
            Mail::raw("{$bodyText}\n\n" . ($actionUrl ? "View Details: {$actionUrl}" : ""), function ($message) use ($recipientEmail, $subject) {
                $message->to($recipientEmail)
                        ->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning("Notification email delivery failed: " . $e->getMessage());
        }
    }

    /**
     * Helper to dispatch WhatsApp message via ERP WhatsappController
     */
    public static function sendWhatsAppNotification(string $phoneNumber, string $text): void
    {
        try {
            // Standardize phone (10-12 digits)
            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (strlen($cleanPhone) === 10) {
                $cleanPhone = '91' . $cleanPhone;
            }

            $wa = new WhatsappController();
            $wa->sendText($cleanPhone, $text);
        } catch (\Throwable $e) {
            Log::warning("Notification WhatsApp delivery failed: " . $e->getMessage());
        }
    }
}
