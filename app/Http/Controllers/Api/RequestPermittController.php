<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;
use App\Models\Control;
use App\Models\HazardIdentification;
use App\Models\History;
use App\Models\Permitt;
use App\Models\User;
use App\Models\WorkPreparation;
use App\Notifications\PermitNotification;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Stmt\Else_;
use Spatie\FlareClient\Api;

class RequestPermittController extends Controller
{
    public function store(Request $request)
    {
        $requestData = $request->all();

        try {
            $userRole = User::where('id', $request->input('user_id'))->first();

            // Create a new Permitt instance
            $permitt = new Permitt();
            $permitt->user_id = $request->input('user_id');
            $permitt->permitt_number = $request->input('permitt_number');
            $permitt->work_category = $request->input('work_category');
            $permitt->project_name = $request->input('project_name');
            $permitt->date = $request->input('date');
            $permitt->time = $request->input('time');
            $permitt->type_of_work = $request->input('type_of_work');
            $permitt->kontrol_pengendalian = $request->input('kontrol_pengendalian');
            $permitt->organic = $request->input('organic');
            $permitt->workers = $request->input('workers');
            $permitt->description = $request->input('description');
            $permitt->location = $request->input('location');
            $permitt->tools_used = $request->input('tools_used');
            $permitt->lifting_distance = $request->input('lifting_distance');
            $permitt->gas_measurements = $request->input('gas_measurements');
            $permitt->oksigen = (float) $request->input('oksigen');
            $permitt->karbon_dioksida = (float) $request->input('karbon_dioksida');
            $permitt->hidrogen_sulfida = (float) $request->input('hidrogen_sulfida');
            $permitt->lel = (float) $request->input('lel');
            $permitt->aman_masuk = $request->input('aman_masuk');
            $permitt->aman_hotwork = $request->input('aman_hotwork');
            $permitt->worker_name = $request->input('worker_name');

            if ($userRole->role == 'Pelaksana Kerja') {
                $permitt->is_approve_pelaksana = true;
            } elseif ($userRole->role == 'Supervisi') {
                $permitt->is_approve_supervisi = true;
            } elseif ($userRole->role == 'Manager') {
                $permitt->is_approve_manager = true;
            }

            // Save the Permitt instance
            $permitt->save();

            $user = User::find($permitt->user_id);

            History::create([
                'permitt_id' => $permitt->id,
                'name' => $user->name . ' (' . $user->role . ')',
                'action' => 'Telah melakukan Request Permit'
            ]);

            $message = "Data successfully stored.";
            $statusCode = 200;

            foreach ($requestData['working'] as $workItem) {
                WorkPreparation::create([
                    'permitt_id' => $permitt->id,
                    'pertanyaan' => $workItem['pertanyaan'],
                    'value' => $workItem['value'],
                ]);
            }

            foreach ($requestData['bahaya'] as $hazardItem) {
                HazardIdentification::create([
                    'permitt_id' => $permitt->id,
                    'pertanyaan' => $hazardItem['pertanyaan'],
                    'value' => $hazardItem['value'],
                ]);
            }

            foreach ($requestData['kontrol'] as $controlItem) {
                Control::create([
                    'permitt_id' => $permitt->id,
                    'pertanyaan' => $controlItem['pertanyaan'],
                    'value' => $controlItem['value'],
                ]);
            }

            if ($userRole->role == 'Pelaksana Kerja') {
                $supervisi = User::where('role', 'Supervisi')->get();
                $supervisi->each(function ($supervisi) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $supervisi->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $supervisi->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $e->getMessage());
                    }
                });
            } elseif ($userRole->role == 'Supervisi') {
                $pelaksana = User::where('role', 'Pelaksana Kerja')->get();
                $pelaksana->each(function ($pelaksana) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $pelaksana->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $pelaksana->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $e->getMessage());
                    }
                });
            } elseif ($userRole->role == 'Manager') {
                $pelaksana = User::where('role', 'Pelaksana Kerja')->get();
                $pelaksana->each(function ($pelaksana) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $pelaksana->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $pelaksana->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $e->getMessage());
                    }
                });
            }

            // Use ApiFormatter to format the response
            return ApiFormatter::createApi($statusCode, $message);
        } catch (\Throwable $th) {
            return ApiFormatter::createApi(400, $th);
        }
    }

    public function getAllPermittByUser(Request $request)
    {
        $userId = $request->input('user_id');
        $role = $request->input('role');

        if ($role != 'Supervisi') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control', 'document')
                ->orderBy('updated_at', 'DESC')->get();
        } else {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control', 'document')
                ->where('user_id', $userId)
                ->orderBy('updated_at', 'DESC')
                ->get()
                ->map(function ($item) {
                    $item->oksigen = number_format((float) $item->oksigen, 1);
                    return $item;
                });
        }

        if ($data) {
            return ApiFormatter::createApi(200, 'Success', $data);
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function getOpenPermit(Request $request)
    {
        $userId = $request->input('user_id');
        $role = $request->input('role');

        if ($role != 'Supervisi') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('status', 'Aktif')
                ->orderBy('updated_at', 'DESC')->get();
        } else {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('status', 'Aktif')
                ->where('user_id', $userId)->orderBy('updated_at', 'DESC')->get();
        }

        if ($data) {
            return ApiFormatter::createApi(200, 'Success', $data);
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function getApprovePermit(Request $request)
    {
        $role = $request->input('role');
        if ($role == 'Manager') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('is_approve_supervisi', true)->where('is_approve_manager', false)->where('status', '!=', 'Ditolak')->orderBy('updated_at', 'DESC')->get();
        } else if ($role == 'HSE') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('is_approve_manager', true)->where('is_approve_hse', false)->where('status', '!=', 'Ditolak')->orderBy('updated_at', 'DESC')->get();
        } else if ($role == 'Supervisi') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('is_approve_pelaksana', true)->where('is_approve_supervisi', false)->where('status', '!=', 'Ditolak')->orderBy('updated_at', 'DESC')->get();
        } elseif ($role == 'Pelaksana Kerja') {
            $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
                ->where('is_approve_pelaksana', false)->where('status', '!=', 'Ditolak')->orderBy('updated_at', 'DESC')->get();
        }

        if ($data) {
            return ApiFormatter::createApi(200, 'Success', $data);
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function getDetailPermit(Request $request)
    {
        $permit_id = $request->input('permit_id');
        $data = Permitt::with('user', 'workPreparation', 'hazard', 'control')
            ->where('id', $permit_id)->first();

        if ($data) {
            return ApiFormatter::createApi(200, 'Success', $data);
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function confirmPermit(Request $request)
    {
        $role = $request->role;
        $permit_id = $request->permit_id;
        $value = $request->value;
        $message = $request->message;

        $userId = $request->input('user_id');
        $user = User::find($userId);

        if ($value == 'Setuju') {
            if ($role == 'Manager') {
                $data = Permitt::where('id', $permit_id)->update([
                    'is_approve_manager' => true,
                    'manager_name' => $user->name
                ]);
                History::create([
                    'permitt_id' => $permit_id,
                    'name' => $user->name . ' (' . $user->role . ')',
                    'action' => 'Telah menyetujui Request Permit',
                    'date' => Date::now()
                ]);
            } else if ($role == 'Pelaksana Kerja') {
                $data = Permitt::where('id', $permit_id)->update([
                    'is_approve_pelaksana' => true
                ]);
                History::create([
                    'permitt_id' => $permit_id,
                    'name' => $user->name . ' (' . $user->role . ')',
                    'date' => Date::now(),
                    'action' => 'Telah menyetujui Request Permit'
                ]);
            } else if ($role == 'Supervisi') {
                $data = Permitt::where('id', $permit_id)->update([
                    'is_approve_supervisi' => true
                ]);
                History::create([
                    'permitt_id' => $permit_id,
                    'name' => $user->name . ' (' . $user->role . ')',
                    'date' => Date::now(),
                    'action' => 'Telah menyetujui Request Permit'
                ]);
            } else {
                $data = Permitt::where('id', $permit_id)->update([
                    'is_approve_hse' => true,
                    'status' => 'Aktif',
                    'hse_name' => $user->name
                ]);
                History::create([
                    'permitt_id' => $permit_id,
                    'name' => $user->name . ' (' . $user->role . ')',
                    'date' => Date::now(),
                    'action' => 'Telah menyetujui Request Permit'
                ]);
                // PrintController::print($permit_id);
                $permitt = Permitt::find($permit_id);
                $userSpv = User::find($permitt->user_id);
                $userSpv->each(function ($userSpv) {
                    $notification = new PermitNotification('Request permit anda telah disetujui');
                    try {
                        $response = $userSpv->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $userSpv->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $userSpv->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $userSpv->id . ': ' . $e->getMessage());
                    }
                });
            }
        } else {
            $data = Permitt::where('id', $permit_id)->update([
                'status' => 'Ditolak',
                'message' => $message
            ]);
            History::create([
                'permitt_id' => $permit_id,
                'name' => $user->name . ' (' . $user->role . ')',
                'date' => Date::now(),
                'action' => 'Telah menolak Request Permit'
            ]);
            $permitt = Permitt::find($permit_id);
            $userSpv = User::find($permitt->user_id);
            $userSpv->each(function ($userSpv) {
                $notification = new PermitNotification('Request permit anda telah ditolak');
                try {
                    $response = $userSpv->notify($notification);
                    if ($response['success']) {
                        json_encode('Notification sent successfully to user ' . $userSpv->id);
                    } else {
                        json_encode('Failed to send notification to user ' . $userSpv->id . ': ' . $response['error']);
                    }
                } catch (\Exception $e) {
                    json_encode('Failed to send notification to user ' . $userSpv->id . ': ' . $e->getMessage());
                }
            });
        }

        if ($data) {
            return ApiFormatter::createApi(200, 'Success');
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function openPermit(Request $request)
    {
        $permit_id = $request->input('permit_id');
        $role = $request->input('role');
        $value = $request->input('value');
        $work_done = $request->input('work_done');
        $need_permit = $request->input('need_permit');

        $userId = $request->input('user_id');
        $user = User::find($userId);

        if ($value == 'Endorse') {
            $data = Permitt::where('id', $permit_id)->update([
                'status' => 'Menunggu',
                'status_permit' => $value,
                'is_approve_supervisi' => false,
                'is_approve_hse' => false
            ]);

            $supervisi = User::where('role', 'Supervisi')->get();
            $value = $request->input('value');
            $supervisi->each(function ($supervisi) use ($value) {
                $notification = new PermitNotification('Permintaan ' . $value . ' pada permit!');
                try {
                    $response = $supervisi->notify($notification);
                    if ($response['success']) {
                        json_encode('Notification sent successfully to user ' . $supervisi->id);
                    } else {
                        json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $response['error']);
                    }
                } catch (\Exception $e) {
                    json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $e->getMessage());
                }
            });
        } elseif ($value == 'Close') {
            $data = Permitt::where('id', $permit_id)->update([
                'status' => 'Selesai',
                'status_permit' => $value
            ]);
        }

        History::create([
            'permitt_id' => $permit_id,
            'name' => $user->name . ' (' . $user->role . ')',
            'date' => Date::now(),
            'action' => 'Telah melakukan ' . $value . ' Permit'
        ]);

        if ($data) {
            return ApiFormatter::createApi(200, 'Success');
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }

    public function update(Request $request)
    {
        $permitId = $request->input('permitId');
        $requestData = $request->all();

        try {
            $userRole = User::where('id', $request->input('user_id'))->first();
            $is_approve_pelaksana =  false;
            $is_approve_supervisi =  false;
            $is_approve_manager =  false;

            if ($userRole == 'Pelaksana Kerja') {
                $is_approve_pelaksana = true;
            } else if ($userRole == 'Supervisi') {
                $is_approve_supervisi = true;
            } else if ($userRole == 'Manager') {
                $is_approve_manager = true;
            }

            try {
                $permitt = Permitt::where('id', $permitId)->update([
                    'work_category' => $request->input('work_category'),
                    'project_name' => $request->input('project_name'),
                    'date' => $request->input('date'),
                    'time' => $request->input('time'),
                    'type_of_work' => $request->input('type_of_work'),
                    'kontrol_pengendalian' => $request->input('kontrol_pengendalian'),
                    'organic' => $request->input('organic'),
                    'workers' => $request->input('workers'),
                    'description' => $request->input('description'),
                    'location' => $request->input('location'),
                    'tools_used' => $request->input('tools_used'),
                    'lifting_distance' => $request->input('lifting_distance'),
                    'gas_measurements' => $request->input('gas_measurements'),
                    'oksigen' => $request->input('oksigen'),
                    'karbon_dioksida' => $request->input('karbon_dioksida'),
                    'hidrogen_sulfida' => $request->input('hidrogen_sulfida'),
                    'lel' => $request->input('lel'),
                    'aman_masuk' => $request->input('aman_masuk'),
                    'aman_hotwork' => $request->input('aman_hotwork'),
                    'worker_name' => $request->input('worker_name'),
                    'is_approve_pelaksana' => $is_approve_pelaksana,
                    'is_approve_supervisi' => $is_approve_supervisi,
                    'is_approve_manager' => $is_approve_manager,
                    'is_approve_hse' => false,
                    'status' => 'Menunggu'
                ]);
            } catch (\Throwable $th) {
                return ApiFormatter::createApi(200, $th->getMessage());
            }


            History::create([
                'permitt_id' => $permitId,
                'name' => $userRole->name . ' (' . $userRole->role . ')',
                'action' => 'Telah melakukan Edit Permit'
            ]);

            $message = "Data successfully stored.";
            $statusCode = 200;

            foreach ($requestData['working'] as $workItem) {
                WorkPreparation::where('permitt_id', $permitId)->where('pertanyaan', $workItem['pertanyaan'])->update([
                    'value' => $workItem['value'],
                ]);
            }

            foreach ($requestData['bahaya'] as $hazardItem) {
                HazardIdentification::where('permitt_id', $permitId)
                    ->where('id', $hazardItem['id'])
                    ->update([
                        'pertanyaan' => $hazardItem['pertanyaan'],
                        'value' => $hazardItem['value'],
                    ]);
            }

            foreach ($requestData['kontrol'] as $controlItem) {
                Control::where('permitt_id', $permitId)
                    ->where('id', $controlItem['id'])
                    ->update([
                        'pertanyaan' => $controlItem['pertanyaan'],
                        'value' => $controlItem['value'],
                    ]);
            }

            if ($userRole->role == 'Pelaksana Kerja') {
                $supervisi = User::where('role', 'Supervisi')->get();
                $supervisi->each(function ($supervisi) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $supervisi->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $supervisi->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $supervisi->id . ': ' . $e->getMessage());
                    }
                });
            } elseif ($userRole->role == 'Supervisi') {
                $pelaksana = User::where('role', 'Pelaksana Kerja')->get();
                $pelaksana->each(function ($pelaksana) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $pelaksana->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $pelaksana->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $e->getMessage());
                    }
                });
            } elseif ($userRole->role == 'Manager') {
                $pelaksana = User::where('role', 'Pelaksana Kerja')->get();
                $pelaksana->each(function ($pelaksana) {
                    $notification = new PermitNotification('Ada request permit baru');
                    try {
                        $response = $pelaksana->notify($notification);
                        if ($response['success']) {
                            json_encode('Notification sent successfully to user ' . $pelaksana->id);
                        } else {
                            json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $response['error']);
                        }
                    } catch (\Exception $e) {
                        json_encode('Failed to send notification to user ' . $pelaksana->id . ': ' . $e->getMessage());
                    }
                });
            }

            return ApiFormatter::createApi($statusCode, $message, $permitt);
            // Use ApiFormatter to format the response
        } catch (\Throwable $th) {
            return ApiFormatter::createApi(200, $th->getMessage());
        }
    }
}
