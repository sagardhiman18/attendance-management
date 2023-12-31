<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        h1 {
            color: #3498db;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .attractive-button {
            padding: 10px 20px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            border-radius: 5px;
            background-color: #3498db;
            color: #fff;
            border: none;
            transition: background-color 0.3s;
        }

        .attractive-button:hover {
            background-color: #2980b9;
        }

        /* Styled Select Box Styles */
        .styled-select {
            width: 200px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #3498db;
            border-radius: 5px;
            appearance: none;
            background-color: #fff;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#3498db" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z" /><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
        }

        /* Styled Calendar Input Styles */
        .styled-calendar {
            width: 200px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #3498db;
            border-radius: 5px;
            appearance: none;
            background-color: #fff;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#3498db" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3H5.01C3.9 3 3 3.9 3 5s.9 2 2.01 2H19c1.1 0 2-.9 2-2s-.9-2-2-2zM2 21h2v-2H2v2zm4 0h2v-2H6v2zm4 0h2v-2h-2v2zm4 0h2v-2h-2v2zm4 0h2v-2h-2v2z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
        }
        #apply-leave {
            padding: 20px;
        }
        #biometric-today {
            padding: 5px;
        }
    </style>
</head>
<body>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div id="biometric-today">
            <form method="POST" action="{{ route('attendance.today') }}">
                @csrf
                <select name="employee_id" class="styled-select">
                    <option value="">--Sandwich Case--</option>
                    @foreach($allEmp as $allEmp1)
                        <option value="{{ $allEmp1->id }}">({{ $allEmp1->id }}) {{ $allEmp1->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="attractive-button" name="biometric" value="present">Present</button>
                <button type="submit" class="attractive-button" name="biometric" value="absent">Absent</button>
            </form>
        </div>

        <div id="biometric-today">
            <form method="POST" action="{{ route('attendance.withoutsandwich') }}">
                @csrf
                <select name="employee_id" class="styled-select">
                    <option value="">--Without Sandwich Case--</option>
                    @foreach($allEmp as $allEmp1)
                        <option value="{{ $allEmp1->id }}">({{ $allEmp1->id }}) {{ $allEmp1->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="attractive-button" name="biometric" value="present">Present</button>
                <button type="submit" class="attractive-button" name="biometric" value="absent">Absent</button>
            </form>
        </div>
        {{-- <div id="apply-leave"> 
            <form method="POST" action="{{ route('applyLeave.save') }}" >
                @csrf
                <select name="employee_id" class="styled-select">
                    @foreach($allEmp as $allEmp2)
                        <option value="{{ $allEmp2->id }}">({{ $allEmp2->id }}) {{ $allEmp2->name }}</option>
                    @endforeach
                </select>
                <input type="date" class="styled-calendar" name="start_date" required>
                <input type="date" class="styled-calendar" name="end_date" required>
                <button type="submit" class="attractive-button" id="apply-leave-button">Apply Leave</button>
            </form>
        </div> --}}
    </div>
    <h1>Employee Attendance</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Date</th>
                <th>Day</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->emp_id }}</td>
                    <td>{{ $employee->date }}</td>
                    <td>{{ $employee->day }}</td>
                    <td>{{ $employee->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
