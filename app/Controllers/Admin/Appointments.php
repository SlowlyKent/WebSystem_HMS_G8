<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Appointments extends BaseController
{
    public function index()
    {
        helper(['form']);
        $db = db_connect();

        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to access Appointments.');
        }

        $session = session();
        $role = (string) $session->get('role');
        $canAssign = ($role === 'admin' || $role === 'receptionist');

        // Week filter (default: current week)
        $weekStartParam = (string) ($this->request->getGet('week_start') ?? '');
        $weekStart = $weekStartParam !== '' ? date('Y-m-d', strtotime($weekStartParam)) : date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        $patients = [];
        $schedules = [];
        $scheduleTypes = [];

        if ($canAssign) {
            // Patients dropdown
            $patients = $db->table('patients')
                ->select('id, first_name, last_name, email')
                ->orderBy('first_name', 'ASC')
                ->get()->getResultArray();

            // Available schedule slots for the week (status = available)
            $schedules = $db->table('doctor_schedules ds')
                ->select('ds.id as schedule_id, ds.date, ds.start_time, ds.end_time, ds.status, d.id as doctor_id, u.first_name, u.last_name, d.room_number, ds.schedule_type_id')
                ->join('doctors d', 'd.id = ds.doctor_id')
                ->join('users u', 'u.id = d.user_id')
                ->where('ds.date >=', $weekStart)
                ->where('ds.date <=', $weekEnd)
                ->where('ds.status', 'available')
                ->orderBy('ds.date', 'ASC')
                ->orderBy('ds.start_time', 'ASC')
                ->get()->getResultArray();

            // Schedule Types dropdown
            $scheduleTypes = $db->table('schedule_types')->select('id, type_name')->orderBy('id', 'ASC')->get()->getResultArray();
        }

        // Appointments list (limit to selected week)
        $appointmentsBuilder = $db->table('appointments a')
            ->select('a.id, a.appointment_date, a.start_time, a.end_time, a.status, p.first_name as p_first, p.last_name as p_last, u.first_name as d_first, u.last_name as d_last')
            ->join('patients p', 'p.id = a.patient_id', 'left')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'left')
            ->join('doctors d', 'd.id = ds.doctor_id', 'left')
            ->join('users u', 'u.id = d.user_id', 'left')
            ->where('a.appointment_date >=', $weekStart)
            ->where('a.appointment_date <=', $weekEnd)
            ->orderBy('a.appointment_date', 'ASC')
            ->orderBy('a.start_time', 'ASC')
            ->limit(20);

        // Doctors see only their own appointments
        if ($role === 'doctor') {
            $userId = (int) $session->get('user_id');
            $docRow = $db->table('doctors')->select('id')->where('user_id', $userId)->get()->getFirstRow('array');
            $doctorId = $docRow['id'] ?? 0;
            if ($doctorId) {
                $appointmentsBuilder->where('ds.doctor_id', $doctorId);
            } else {
                $appointmentsBuilder->where('1 = 0');
            }
        }

        $appointments = $appointmentsBuilder->get()->getResultArray();

        // Build Mon-Sun structure
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = date('Y-m-d', strtotime($weekStart . " +{$i} day"));
        }
        $appointmentsByDate = [];
        foreach ($weekDays as $dte) { $appointmentsByDate[$dte] = []; }
        foreach ($appointments as $a) {
            $d = $a['appointment_date'] ?? '';
            if (isset($appointmentsByDate[$d])) {
                $appointmentsByDate[$d][] = $a;
            }
        }

        return view('roles/admin/Appointment', [
            'title'        => 'Appointments',
            'canAssign'    => $canAssign,
            'patients'     => $patients,
            'schedules'    => $schedules,
            'scheduleTypes'=> $scheduleTypes,
            'appointments' => $appointments,
            'appointmentsByDate' => $appointmentsByDate,
            'weekDays'     => $weekDays,
            'weekStart'    => $weekStart,
            'weekEnd'      => $weekEnd,
            'role'         => $role,
        ]);
    }

    public function store()
    {
        helper(['form']);
        $db = db_connect();
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to create appointments.');
        }
        if (! in_array($session->get('role'), ['admin','receptionist'], true)) {
            return redirect()->to(base_url('appointments'))->with('error', 'Only admin/receptionist can create appointments.');
        }

        $patientId      = (int) $this->request->getPost('patient_id');
        $scheduleId     = (int) $this->request->getPost('doctor_schedule_id');
        $apptDate       = (string) $this->request->getPost('appointment_date');
        $status         = (string) ($this->request->getPost('status') ?? 'pending');
        $postedTypeId   = (int) ($this->request->getPost('schedule_type_id') ?? 0);

        if (! $patientId || ! $scheduleId || $apptDate === '') {
            return redirect()->back()->with('error', 'Please complete all required fields.');
        }

        // Validate schedule exists and is available on same date
        $schedule = $db->table('doctor_schedules')->where('id', $scheduleId)->get()->getFirstRow('array');
        if (! $schedule) {
            return redirect()->back()->with('error', 'Selected schedule not found.');
        }
        if ($schedule['status'] !== 'available') {
            return redirect()->back()->with('error', 'Selected schedule is not available.');
        }
        if ($apptDate !== $schedule['date']) {
            return redirect()->back()->with('error', 'Appointment date must match the schedule date.');
        }

        // Determine fields derived from schedule
        $doctorIdFromSchedule = (int) ($schedule['doctor_id'] ?? 0);
        $startTime = (string) ($schedule['start_time'] ?? '');
        $endTime   = (string) ($schedule['end_time'] ?? '');
        $scheduleTypeId = $postedTypeId > 0 ? $postedTypeId : (int) ($schedule['schedule_type_id'] ?? 0);
        // Room (optional): use doctor's current room_number if available
        $room = null;
        if ($doctorIdFromSchedule) {
            $roomRow = $db->table('doctors')->select('room_number')->where('id', $doctorIdFromSchedule)->get()->getFirstRow('array');
            $room = $roomRow['room_number'] ?? null;
        }

        if (! $doctorIdFromSchedule || ! $scheduleTypeId) {
            return redirect()->back()->with('error', 'Missing doctor or schedule type for the appointment.');
        }

        try {
            $db->transStart();
            // Create appointment
            $db->table('appointments')->insert([
                'patient_id'         => $patientId,
                'doctor_id'          => $doctorIdFromSchedule,
                'doctor_schedule_id' => $scheduleId,
                'schedule_type_id'   => $scheduleTypeId,
                'appointment_date'   => $apptDate,
                'start_time'         => $startTime ?: null,
                'end_time'           => $endTime ?: null,
                'room'               => $room,
                'status'             => $status,
                'created_by'         => (int) ($session->get('user_id') ?? 0),
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
            // Mark schedule as booked
            $db->table('doctor_schedules')->where('id', $scheduleId)->update(['status' => 'booked', 'updated_at' => date('Y-m-d H:i:s')]);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to save appointment.');
            }

            return redirect()->to(base_url('appointments'))->with('success', 'Appointment created successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save appointment: ' . $e->getMessage());
        }
    }
}
