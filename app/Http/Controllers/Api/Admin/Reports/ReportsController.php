<?php

namespace App\Http\Controllers\Api\Admin\Reports;

use App\Http\Controllers\Api\Helpers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends BaseController
{
    //
    public function GetAreaAndSection(Request $request)
    {


        $filter = $request->all();

        $rawQuery = DB::table('areas')
            ->selectRaw('areas.id as area_id')
            ->selectRaw('areas.name as area_name')
            ->selectRaw('sections.id as section_id')
            ->selectRaw('sections.name as section_name')
            ->selectRaw('sections.rent_per_month')
            ->join('sections', 'sections.area_id', '=', 'areas.id')
            // ->selectRaw('COUNT(DISTINCT bets.transactionId) as TotalVoidCount')
            // ->selectRaw('SUM(bets.betAmount) As totalVoid')
            // ->leftJoin('draws', 'draws.id', '=', 'bets.drawId')
            // ->leftJoin('tellers', 'tellers.id', '=', 'bets.tellerId')
            // // ->where('draws.created_at', '>=', $from)
            // // ->where('draws.created_at', '<', $to)
            // ->groupBy('tellers.id', 'draws.id')
            // ->groupBy('tellers.id')
            ->orderBy('areas.name', 'ASC')
            ->get();
        $Querydata = json_decode($rawQuery, true);

        return $this->sendResponse($Querydata, 'Reports retrieved successfully.');
    }


    public function OverAllDispenseCashTickets(Request $request)
    {


        $filter = $request->all();
        $from = $filter['from'];
        $to = $filter['to'];

        $rawQuery = DB::table('cash_tickets')
            ->selectRaw('SUM(cash_tickets.price) as total_dispensed')
            ->join('dispense_tickets', 'dispense_tickets.cash_ticket_id', '=', 'cash_tickets.id')
            ->whereRaw("dispense_tickets.is_void = 0 AND  dispense_tickets.created_at >= '" . $from . "' AND dispense_tickets.created_at < '" . $to . "'")
            ->get();
        $Querydata = json_decode($rawQuery, true);

        return $this->sendResponse($Querydata, 'Reports retrieved successfully.');
    }

    public function OverAllDispenseCashTicketsPerName(Request $request)
    {


        $filter = $request->all();
        $from = $filter['from'];
        $to = $filter['to'];

        $rawQuery = DB::table('cash_tickets')
            ->selectRaw('SUM(cash_tickets.price) as total_dispensed')
            ->selectRaw('cash_tickets.name')
            ->join('dispense_tickets', 'dispense_tickets.cash_ticket_id', '=', 'cash_tickets.id')
            ->whereRaw("dispense_tickets.is_void = 0 AND  dispense_tickets.created_at >= '" . $from . "' AND dispense_tickets.created_at < '" . $to . "'")
            ->groupBy('cash_tickets.id')
            ->get();
        $Querydata = json_decode($rawQuery, true);

        return $this->sendResponse($Querydata, 'Reports retrieved successfully.');
    }

    public function OverAllDispenseCashTicketsPerCollector(Request $request)
    {


        $filter = $request->all();
        $from = $filter['from'];
        $to = $filter['to'];

        $rawQuery = DB::table('cash_tickets')
            ->selectRaw('SUM(cash_tickets.price) as total_dispensed')
            ->selectRaw('cash_tickets.name')
            ->selectRaw('collectors.full_name')
            ->join('dispense_tickets', 'dispense_tickets.cash_ticket_id', '=', 'cash_tickets.id')
            ->join('collectors', 'collectors.id', '=', 'dispense_tickets.collector_id')
            ->whereRaw("dispense_tickets.is_void = 0 AND  dispense_tickets.created_at >= '" . $from . "' AND dispense_tickets.created_at < '" . $to . "'")
            ->groupBy('collectors.id')
            ->get();
        $Querydata = json_decode($rawQuery, true);

        return $this->sendResponse($Querydata, 'Reports retrieved successfully.');
    }
}
