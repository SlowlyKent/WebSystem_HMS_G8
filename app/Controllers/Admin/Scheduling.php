<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Scheduling extends BaseController
{
    public function index()
    {
        helper(['form']);
        $db = db_connect();

        // Require authentication so header/sidebar render correctly
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to access Scheduling.');
        }

        $session = session();
        $role = (string) $session->get('role');
        $userId = (int) $session->get('user_id');

        $canAssign = ($role === 'admin' || $role === 'receptionist');

        // For admins/receptionists, ensure all active doctor profiles exist
        if ($canAssign) {
            $missing = $db->table('users u')
                ->select('u.id as user_id')
                ->where('u.role_id', 2)
                ->where('u.status', 'active')
                ->whereNotIn('u.id', function($builder){
                    $builder->select('d.user_id')->from('doctors d');
                })
                ->get()->getResultArray();

            foreach ($missing as $row) {
                $db->table('doctors')->insert([
                    'user_id'        => (int) $row['user_id'],
                    'specialization' => 'General',
                    'license_number' => 'AUTO-' . (int) $row['user_id'] . '-' . time(),
                    'room_number'    => null,
                    'status'         => 'active',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Build doctors list (only needed if canAssign)
        $doctors = [];
        if ($canAssign) {
            $doctors = $db->table('doctors')
                ->select('doctors.id as id, users.first_name, users.last_name, doctors.room_number')
                ->join('users', 'users.id = doctors.user_id')
                ->where('users.status', 'active')
                ->orderBy('users.first_name', 'ASC')
                ->get()->getResultArray();
        }

        // Schedules: doctors see only their own
        $builder = $db->table('doctor_schedules ds')
            ->select('ds.id, ds.date, ds.start_time, ds.end_time, ds.status, ds.created_at, d.id as doctor_id, u.first_name, u.last_name, d.room_number')
            ->join('doctors d', 'd.id = ds.doctor_id')
            ->join('users u', 'u.id = d.user_id')
            ->orderBy('ds.created_at', 'DESC');

        if ($role === 'doctor') {
            $doctorRow = $db->table('doctors')->select('id')->where('user_id', $userId)->get()->getFirstRow('array');
            $doctorId = $doctorRow['id'] ?? 0;
            $builder->where('ds.doctor_id', $doctorId);
        }

        $schedules = $builder->get()->getResultArray();

        return view('roles/admin/Doctor/Scheduling', [
            'title'     => 'Doctor Scheduling',
            'canAssign' => $canAssign,
            'doctors'   => $doctors,
            'schedules' => $schedules,
        ]);
    }

    public function store()
    {
        helper(['form']);
        $db = db_connect();
        $session = session();

        // Require authentication
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to submit schedules.');
        }

        // Disallow doctors from submitting schedules
        if ($session->get('role') === 'doctor') {
            return redirect()->to(base_url('scheduling'))->with('error', 'Doctors cannot assign schedules.');
        }

        $doctorId   = (int) $this->request->getPost('doctor_id');
        $dayOfWeek  = (string) $this->request->getPost('day_of_week');
        $startTime  = (string) $this->request->getPost('start_time');
        $endTime    = (string) $this->request->getPost('end_time');
        $roomNumber = (string) $this->request->getPost('room_number');
        $status     = (string) $this->request->getPost('status');

        if (!$doctorId || !$dayOfWeek || !$startTime || !$endTime || !$status) {
            return redirect()->back()->with('error', 'Please complete all required fields.');
        }

        $days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        $dayIndex = array_search(strtolower($dayOfWeek), $days, true);
        if ($dayIndex === false) {
            return redirect()->back()->with('error', 'Invalid day of week.');
        }
        $todayIndex = (int) date('w');
        $delta = ($dayIndex - $todayIndex + 7) % 7;
        $date = date('Y-m-d', strtotime("+{$delta} day"));

        $typeRow = $db->table('schedule_types')->select('id')->orderBy('id', 'ASC')->get()->getFirstRow('array');
        $scheduleTypeId = $typeRow['id'] ?? 1;

        try {
            $db->table('doctor_schedules')->insert([
                'doctor_id'        => $doctorId,
                'schedule_type_id' => $scheduleTypeId,
                'date'             => $date,
                'start_time'       => $startTime,
                'end_time'         => $endTime,
                'status'           => $status,
                'created_by'       => (int) ($session->get('user_id') ?? 0),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            if ($roomNumber !== '') {
                $db->table('doctors')->where('id', $doctorId)->update(['room_number' => $roomNumber]);
            }

            return redirect()->to(base_url('scheduling'))->with('success', 'Schedule assigned successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save schedule: ' . $e->getMessage());
        }
    }
}

