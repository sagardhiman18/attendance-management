<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $employees = Attendance::select('emp_id', 'date', 'day', 'status')
            // ->groupBy('emp_id')
            ->get();
        $allEmp = Employee::all();

        return view('attendance.index', ['employees' => $employees, 'allEmp' => $allEmp]);
    }

    public function biometricToday(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $employeeId = $request->input('employee_id');
        $biometric = $request->input('biometric');
        $currentDay = now()->format('l');
        $currentDay = 'Monday'; 
        
        $attendanceObject = new Attendance;

        $currentAttendanceCheck = $attendanceObject->checkAttendanceAlreadyExists($employeeId, $today);

        if ($currentDay == 'Monday') {

            if ($biometric == 'absent') {

                // check user does not on the button twice for absent
                if ($currentAttendanceCheck) {
                    return redirect()->route('attendance.index')->with('error', 'Attendance already exists for today.');
                }

                // check if the user is absent or present on last friday
                $previousAttendanceCheck = $attendanceObject->checkAttendanceOnLastWorkingday($employeeId);
                
                if ($previousAttendanceCheck != null) { // user is present on friday
                    if ($previousAttendanceCheck->status == 'paid_leave') { // user is on PAID LEAVE on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDay(1), 'Saturday','lwp');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(2), 'Sunday','lwp');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(3), 'Monday', 'lwp');

                        return redirect()->route('attendance.index')->with('success', 'Attendance saved LWP.');

                    } else if($previousAttendanceCheck->status == 'absent') { // user is on LEAVE(absent) on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(1),'Saturday', 'absent');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(2),'Sunday','absent');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(3),'Monday', 'absent');

                        return redirect()->route('attendance.index')->with('success', 'Attendance saved ABSENT marked.');
                    }
                } else { // user is not present on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, $today, $currentDay, $biometric);
                        return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
                }

            } else {

                // check user does not on the button twice for present
                if ($currentAttendanceCheck) {
                    return redirect()->route('attendance.index')->with('error', 'Attendance already exists for today.');
                }

                // save the present attendance in the DB
                $attendanceObject->updateAttendanceStatus($employeeId, $today, $currentDay, $biometric);
                return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
            }
        } else {
            return redirect()->route('attendance.index')->with('success', 'Work In-Progress.');
        }

    }

    public function biometricWwithoutsandwich(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $employeeId = $request->input('employee_id');
        $biometric = $request->input('biometric');
        $currentDay = now()->format('l');
        $currentDay = 'Monday'; 
        
        $attendanceObject = new Attendance;

        $currentAttendanceCheck = $attendanceObject->checkAttendanceAlreadyExists($employeeId, $today);

            if ($biometric == 'absent') {

                // check user does not on the button twice for absent
                if ($currentAttendanceCheck) {
                    return redirect()->route('attendance.index')->with('error', 'Attendance already exists for today.');
                }

                // check if the user is absent or present on last friday
                // $previousAttendanceCheck = $attendanceObject->checkAttendanceOnLastWorkingday($employeeId);

                $secondConsecutiveWeekendMissed = $attendanceObject->checkSecondConsecutiveWeekendMissed($employeeId, $today);

                dd($secondConsecutiveWeekendMissed);
                if ($previousAttendanceCheck != null) { // user is present on friday
                    if ($previousAttendanceCheck->status == 'paid_leave') { // user is on PAID LEAVE on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDay(1), 'Saturday','lwp');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(2), 'Sunday','lwp');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(3), 'Monday', 'lwp');

                        return redirect()->route('attendance.index')->with('success', 'Attendance saved LWP.');

                    } else if($previousAttendanceCheck->status == 'absent') { // user is on LEAVE(absent) on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(1),'Saturday', 'absent');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(2),'Sunday','absent');
                        $attendanceObject->updateAttendanceStatus($employeeId, Carbon::parse($previousAttendanceCheck->date)->addDays(3),'Monday', 'absent');

                        return redirect()->route('attendance.index')->with('success', 'Attendance saved ABSENT marked.');
                    }
                } else { // user is not present on friday

                        $attendanceObject->updateAttendanceStatus($employeeId, $today, $currentDay, $biometric);
                        return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
                }

            } else {

                // check user does not on the button twice for present
                if ($currentAttendanceCheck) {
                    return redirect()->route('attendance.index')->with('error', 'Attendance already exists for today.');
                }

                // save the present attendance in the DB
                $attendanceObject->updateAttendanceStatus($employeeId, $today, $currentDay, $biometric);
                return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
            }
  

    }


    public function applyLeave(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $leaveStartDate = Carbon::parse($request->input('start_date'));
        $leaveEndDate = Carbon::parse($request->input('end_date'));

        $attendances = Attendance::where('emp_id', $employeeId)
            ->whereBetween('date', [$leaveStartDate, $leaveEndDate])
            ->get();

        foreach ($attendances as $attendance) {
            $checkPresentonMonday = $this->isEmployeeReportedOnMonday($employeeId, Carbon::parse($attendance->date)->addDays(3));

            dd($checkPresentonMonday);
            // Check if the employee was on Paid leave on Friday
            
            if ($attendance->day == 'Friday' && $attendance->status == 'paid_leave') {
                // dd('LWP');
                // Check if the employee is unable to report on Monday
                dd($checkPresentonMonday);
                if ($checkPresentonMonday) {
                    dd('inseide if');
                    // Update the attendance records for Saturday, Sunday, and Monday
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDay(), 'lwp');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(2), 'lwp');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(3), 'lwp');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(4), 'lwp');
                }
            } else if ($attendance->day == 'Friday' && $attendance->status == 'absent') {
                // dd('ABSENT');
                dd($checkPresentonMonday);
                if ($checkPresentonMonday) {
                    // If not reported, mark additional day as 'lwp'
                    dd(Carbon::parse($attendance->date)->addDays(3));
                    // Update the attendance records for Saturday, Sunday, and Monday
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDay(), 'absent');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(2), 'absent');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(3), 'absent');
                    $this->updateAttendanceStatus($employeeId, Carbon::parse($attendance->date)->addDays(4), 'absent');
                }
            }
        }
        return response()->json(['message' => 'Leave applied successfully']);
    }



}
