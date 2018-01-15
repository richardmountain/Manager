<?php

namespace App\Http\Controllers\Admin\Events;

use Illuminate\Http\Request;

use DB;
use Session;

use App\User;
use App\Event;
use App\EventTicket;
use App\EventSeating;
use App\EventParticipant;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class TicketsController extends Controller
{
	/**
	 * Show Tickets Index Page
	 * @param  Event  $event
	 * @return View
	 */
	public function index(Event $event)
	{
		return view('admin.events.tickets.index')->withEvent($event)->withUsers(User::all());
	}

	/**
	 * Show Tickets Page
	 * @param  Event       $event
	 * @param  EventTicket $ticket
	 * @return View
	 */
	public function show(Event $event, EventTicket $ticket)
	{
		return view('admin.events.tickets.show')->withEvent($event)->withTicket($ticket);
	}
	
	/**
	 * Store Ticket to Database
	 * @param  Request $request
	 * @param  Event   $event
	 * @return Redirect
	 */
	public function store(Request $request, Event $event)
	{
		$rules = [
			'name'            => 'required',
			'price'           => 'required|numeric',
			'sale_start_date' => 'date',
			'sale_start_time' => 'date_format:H:i',
			'sale_end_date'   => 'date',
			'sale_end_time'   => 'date_format:H:i', 
			'type'            => 'required',
			'seatable'        => 'boolean',
			'quantity'        => 'numeric',
		];
		$messages = [
			'name|required'               => 'A Ticket Name is required',
			'price|numeric'               => 'Price must be a number',
			'price|required'              => 'A Price is required',
			'sale_start_date|date'        => 'Sale dates must be valid Dates',
			'sale_start_time|date_format' => 'Sale times must be valid Times HH:MM',
			'sale_end_date|date'          => 'Sale dates must be valid Dates',
			'sale_end_time|date_format'   => 'Sale times must be valid Times HH:MM',
			'seatable|boolen'             => 'Seatable must be True/False',
			'quantity|numeric'            => 'Quantity must be a number',
		];
		$this->validate($request, $rules, $messages);

		if ($request->sale_start_date != '' || $request->sale_start_time != '') {
			$saleStart = date(
				"Y-m-d H:i:s", strtotime(
					$request->sale_start_date . $request->sale_start_time
				)
			);
		}

		if ($request->sale_end_date != '' || $request->sale_end_time != '') {
			$saleEnd = date(
				"Y-m-d H:i:s", strtotime(
					$request->sale_end_date . $request->sale_end_time
				)
			);
		}

		$ticket             = new EventTicket;
		$ticket->event_id   = $event->id;
		$ticket->name       = $request->name;
		$ticket->type       = $request->type;
		$ticket->price      = $request->price;
		$ticket->sale_start = @$saleStart;
		$ticket->sale_end   = @$saleEnd;
		$ticket->seatable   = false;
		$ticket->quantity   = @$request->quantity;
		if(@$ticket->seatable){
			$ticket->seatable = true;
		}

		if(!$ticket->save()){
			Session::flash('alert-danger', 'Cannot Create Ticket.');
			Redirect::back();
		} 

		Session::flash('alert-success', 'Ticket Created!');
		return Redirect::to('/admin/events/' . $event->id . '/tickets/' . $ticket->id);
	}

	/**
	 * Update Ticket
	 * @param  Request     $request
	 * @param  Event       $event
	 * @param  EventTicket $ticket
	 * @return Redirect
	 */
	public function update(Request $request, Event $event, EventTicket $ticket)
	{
		$rules = [
			'price'           => 'numeric',
			'sale_start_date' => 'date',
			'sale_start_time' => 'date_format:H:i',
			'sale_end_date'   => 'date',
			'sale_end_time'   => 'date_format:H:i', 
			'seatable'        => 'boolean',
			'quantity'        => 'numeric',
		];
		$messages = [
			'price|numeric'               => 'Price must be a number',
			'sale_start_date|date'        => 'Sale dates must be valid Dates',
			'sale_start_time|date_format' => 'Sale times must be valid Times HH:MM',
			'sale_end_date|date'          => 'Sale dates must be valid Dates',
			'sale_end_time|date_format'   => 'Sale times must be valid Times HH:MM',
			'seatable|boolen'             => 'Seatable must be True/False',
			'quantity|numeric'            => 'Quantity must be a number',
		];
		$this->validate($request, $rules, $messages);

		if ($request->sale_start_date != '' || $request->sale_start_time != '') {
			$saleStart = date(
				"Y-m-d H:i:s", strtotime(
					$request->sale_start_date . $request->sale_start_time
				)
			);
		}

		if ($request->sale_end_date != '' || $request->sale_end_time != '') {
			$saleEnd = date(
				"Y-m-d H:i:s", strtotime(
					$request->sale_end_date . $request->sale_end_time
				)
			);
		}

		if ($ticket->participants->isEmpty() && $ticket->price == $request->price) {
			$ticket->price      = @$request->price;
		}

		$ticket->sale_start = @$saleStart;
		$ticket->sale_end   = @$saleEnd;
		$ticket->quantity   = @$request->quantity;
		$ticket->seatable   = @$request->seatable;

		if(!$ticket->save()){
			Session::flash('alert-danger', 'Cannot Update Ticket!');
			return Redirect::back();
		} 
		Session::flash('alert-success', 'Ticket Updated!');
		return Redirect::back();
	}

	/**
	 * Delete Ticket from Database
	 * @param  Event       $event
	 * @param  EventTicket $ticket
	 * @return redirect
	 */
	public function destroy(Event $event, EventTicket $ticket)
	{
		if ($ticket->participants && $ticket->participants()->count() > 0) {
			Session::flash('alert-danger', 'Cannot Delete Ticket, Purchases have been made!');
			return Redirect::back();
		}
		if (!$ticket->delete()) {
			Session::flash('alert-danger', 'Cannot Delete Ticket!');
			return Redirect::back();
		}
		Session::flash('alert-success', 'Successfully deleted!');
		return Redirect::to('admin/events/' . $event->id . '/tickets');
	}
}