<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function getAll() {
        $areas = Area::where('allowed', 1)->get();

        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $result = [];

        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);
            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach ($dayList as $day) {
                if (intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }

                $lastDay = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];

            $dates = '';
            $close = 0;

            foreach ($dayGroups as $group) {
                if ($close === 0) {
                    $dates .= $group;
                } else {
                    $dates .='-'.$group.',';
                }

                $close = 1 - $close;
            }

            $dates = explode(',', $dates);

            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $key => $value) {
                $dates[$key] .= ' '.$start.' às '.$end;
            }

            $result[] = [
                'id' => $area['id'],
                'cover' => asset('storage/'.$area['cover']),
                'title' => $area['title'],
                'dates' => $dates,
            ];
        }

        return setSuccessResponse('', [
            'areas' => $result
        ]);
    }

    public function create(Request $request, $areaId) {
        $fields = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);

        $unit = Unit::find($fields['property']);
        $area = Area::find($areaId);

        if (!$unit || !$area) {
            return setErrorResponse('Dados incorretos', 422);
        }

        $hasPermission = true;

        $weekDay = date('w', strtotime($fields['date']));

        $allowedDays = explode(',', $area['days']);

        if (!in_array($weekDay, $allowedDays)) {
            $hasPermission = false;
        } else {
            $start = strtotime($area['start_time']);
            $end = strtotime('-1 hour', strtotime($area['end_time']));

            $reservationTime = strtotime($fields['time']);

            if ($reservationTime < $start || $reservationTime > $end) {
                $hasPermission = false;
            }
        }

        $existingDisabledDay = AreaDisabledDay::where('area_id', $areaId)
            ->where('day', $fields['date'])
        ->count();

        if ($existingDisabledDay > 0) {
            $hasPermission = false;
        }

        $existingReservations = Reservation::where('area_id', $areaId)
            ->where('reservation_at', $fields['date'].' '.$fields['time'])
        ->count();

        if ($existingReservations > 0) {
            $hasPermission = false;
        }

        if (!$hasPermission) {
            return setErrorResponse('Reserva não permitida neste dia/horário', 422);
        }

        $newReservation = Reservation::create([
            'unit_id' => $fields['property'],
            'area_id' => $areaId,
            'reservation_at' => $fields['date'].' '.$fields['time'],
        ]);

        return setSuccessResponse('', [
            'reservation' => $newReservation
        ]);
    }

    public function getDisabledDates(Request $request, $areaId) {
        $area = Area::find($areaId);

        if (!$area) {
            return setErrorResponse('Area inexistente', 404);
        }

        $disabledDays = AreaDisabledDay::where('area_id', $areaId)->get();
        $disabledDaysList = [];

        foreach ($disabledDays as $disabledDay) {
            $disabledDaysList[] = $disabledDay['day'];
        }

        $allowedDays = explode(',', $area['days']);
        $notAllowedDays = [];

        for ($q = 0; $q < 7; $q++) {
            if (!in_array($q, $allowedDays)) {
                $notAllowedDays[] = $q;
            }
        }

        $start = time();
        $end = strtotime('+3 months');

        for (
            $current = $start;
            $current < $end;
            $current = strtotime('+1 day', $current)
        ) {
            $weekDay = date('w', $current);

            if (in_array($weekDay, $notAllowedDays)) {
                $disabledDaysList[] = date('Y-m-d', $current);
            }
        }

        return setSuccessResponse('', [
            'disabled_days' => $disabledDaysList,
        ]);
    }

    public function getAvailableTimes(Request $request, $areaId) {
        $fields = $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $area = Area::find($areaId);

        if (!$area) {
            return setErrorResponse('Area inexistente', 404);
        }

        $hasPermission = true;

        $existingDisabledDay = AreaDisabledDay::where('area_id', $areaId)
            ->where('day', $fields['date'])
        ->count();

        if ($existingDisabledDay > 0) {
            $hasPermission = false;
        }

        $allowedDays = explode(',', $area['days']);
        $weekDay = date('w', strtotime($fields['date']));

        if (!in_array($weekDay, $allowedDays)) {
            $hasPermission = false;
        }

        if (!$hasPermission) {
            return setErrorResponse('Não há horário disponível neste dia', 422);
        }

        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);
        $times = [];

        for (
            $lastTime = $start;
            $lastTime < $end;
            $lastTime = strtotime('+1 hour', $lastTime)
        ) {
            $times[] = $lastTime;
        }

        $timeList = [];

        foreach ($times as $time) {
            $timeList[] = [
                'id' => date('H:i:s', $time),
                'title' => date('H:i:', $time).' - '.date('H:i', strtotime('+1 hour', $time)),
            ];
        }

        $reservations = Reservation::where('area_id', $areaId)
            ->whereBetween('reservation_at', [
                $fields['date'].' 00:00:00',
                $fields['date'].' 23:59:59',
            ])
        ->get();

        $toRemove = [];

        foreach ($reservations as $reservation) {
            $time = date('H:i:s', strtotime($reservation['reservation_at']));
            $toRemove[] = $time;
        }

        foreach ($timeList as $timeItem) {
            if (!in_array($timeItem['id'], $toRemove)) {
                $timeList[] = $timeItem;
            }
        }

        return setSuccessResponse('', [
            'available_times' => $timeList
        ]);
    }

    public function getLoggedUserReservations(Request $request) {
        $fields = $request->validate([
            'property' => 'required'
        ]);

        $unit = Unit::find($fields['property']);

        if (!$unit) {
            return setErrorResponse('Propriedade inexistente', 404);
        }

        $reservations = Reservation::where('unit_id', $fields['property'])
            ->orderBy('reservation_at', 'desc')
        ->get();

        $reservationsList = [];

        foreach ($reservations as $reservation) {
            $area = Area::find($reservation['area_id']);

            $reservationAt = date('d/m/Y H:i', strtotime($reservation['reservation_at']));

            $afterTime = date('H:i', strtotime('+1 hour', strtotime($reservationAt)));

            $reservationAt .= ' à '.$afterTime;

            $reservationsList[] = [
                'id' => $reservation['id'],
                'area_id' => $reservation['area_id'],
                'title' => $area['title'],
                'cover' => asset('storage/'.$area['cover']),
                'reserved_at' => $reservationAt
            ];
        }

        return setSuccessResponse('', [
            'reservations' => $reservationsList
        ]);
    }

    public function deleteLoggedUserReservation($id) {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return setErrorResponse('Reserva inexistente', 404);
        }

        $unit = Unit::find($reservation['unit_id'])
            ->where('owner_id', Auth::id())
        ->first();

        if (!$unit) {
            return setErrorResponse('Esta reserva não é sua');
        }

        if ($unit) {
            $reservation->delete();
        }

        return setSuccessResponse('Reserva removida com sucesso!');
    }
}
