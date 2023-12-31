<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['emp_id', 'date', 'day', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    // check if user does not click on the same button twice
    public function checkAttendanceAlreadyExists($employeeId, $today)
    {
        return Attendance::where('emp_id', $employeeId)
                    ->where('date', $today)
                    ->exists();
    }

    // check if the user is absent or present on last friday
    public function checkAttendanceOnLastWorkingday($employeeId)
    {
        return Attendance::select('emp_id','date','day','status')
                            ->where('emp_id', $employeeId)
                            ->where('date', Carbon::today()->subDays(2)->toDateString())
                            ->first();
    }

    // save the data in attendance table
    public function updateAttendanceStatus($employeeId, $date, $previousDay, $status)
    {
        $attendance = new Attendance;
        $attendance->emp_id = $employeeId;
        $attendance->date = $date;
        $attendance->day = $previousDay;
        $attendance->status = $status;
        $attendance->save();
    } 

    // Check if the employee missed the second consecutive Saturday/Sunday
    public function checkSecondConsecutiveWeekendMissed($employeeId, $today)
    {
        
        $today = '2024-01-08';
        $missedWeekends = Attendance::where('emp_id', $employeeId)
            // ->whereIn('day', ['Saturday', 'Sunday'])
            // ->whereIn('status', ['lwp', 'absent', 'paid_leave'])
            // ->where('date', '<', $today)
            ->orderBy('date', 'desc')
            ->limit(2)
            ->get();
        dd($missedWeekends);
        return $missedWeekends->count() == 2;
    }
}
