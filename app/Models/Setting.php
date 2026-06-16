<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type'];

    /**
     * Helper to get a setting value
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        $value = $setting->value;
        
        switch ($setting->type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            default:
                return $value;
        }
    }

    /**
     * Helper to set a setting value
     */
    public static function set($key, $value, $group = 'general', $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }

    /**
     * Apply email settings to Laravel config dynamically
     */
    public static function configureMailer()
    {
        $emailSettings = self::where('group', 'email')->get()->pluck('value', 'key');
        
        if ($emailSettings->isEmpty()) return;

        $mailer = $emailSettings['mail_mailer'] ?? config('mail.default', 'smtp');

        config([
            'mail.default' => $mailer,
            "mail.mailers.{$mailer}.transport" => $mailer,
            "mail.mailers.{$mailer}.host" => $emailSettings['mail_host'] ?? config("mail.mailers.{$mailer}.host"),
            "mail.mailers.{$mailer}.port" => $emailSettings['mail_port'] ?? config("mail.mailers.{$mailer}.port"),
            "mail.mailers.{$mailer}.encryption" => $emailSettings['mail_encryption'] ?? config("mail.mailers.{$mailer}.encryption"),
            "mail.mailers.{$mailer}.username" => $emailSettings['mail_username'] ?? config("mail.mailers.{$mailer}.username"),
            "mail.mailers.{$mailer}.password" => $emailSettings['mail_password'] ?? config("mail.mailers.{$mailer}.password"),
            'mail.from.address' => $emailSettings['mail_from_address'] ?? config('mail.from.address'),
            'mail.from.name' => $emailSettings['mail_from_name'] ?? config('mail.from.name'),
        ]);
    }

    /**
     * Get all settings as a key-value array
     */
    public static function getAllSettings()
    {
        return self::all()->pluck('value', 'key')->toArray();
    }
}
