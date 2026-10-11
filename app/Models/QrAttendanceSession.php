<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QrAttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'status',
        'scanned_by_employee_id',
        'scanned_at',
        'consumed_at',
        'expires_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'consumed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scannedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'scanned_by_employee_id');
    }

    /**
     * Get or create active QR token session.
     * Keeps token active until scanned by an employee.
     */
    public static function getActiveSession(int $validitySeconds = 86400): self
    {
        // Expire older active tokens that passed 24h
        self::where('status', 'active')
            ->where('expires_at', '<=', Carbon::now())
            ->update(['status' => 'expired']);

        // Check if there is an existing active session
        $activeSession = self::where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if ($activeSession) {
            return $activeSession;
        }

        // Generate new session if no active session found
        return self::createNewSession($validitySeconds);
    }

    /**
     * Create a brand new active token session.
     */
    public static function createNewSession(int $validitySeconds = 86400): self
    {
        // Expire any existing active tokens
        self::where('status', 'active')->update(['status' => 'expired']);

        return self::create([
            'token' => Str::random(40),
            'status' => 'active',
            'expires_at' => Carbon::now()->addSeconds($validitySeconds),
        ]);
    }

    /**
     * Mark token as scanned by an employee.
     */
    public function markAsScanned(int $employeeId): bool
    {
        if ($this->status !== 'active' || $this->isExpired()) {
            return false;
        }

        $this->update([
            'status' => 'scanned',
            'scanned_by_employee_id' => $employeeId,
            'scanned_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Mark token as fully consumed after attendance punch.
     */
    public function markAsConsumed(): bool
    {
        if ($this->status === 'consumed') {
            return false;
        }

        $this->update([
            'status' => 'consumed',
            'consumed_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Check if session is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->expires_at);
    }
}
