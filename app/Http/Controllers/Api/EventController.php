<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GoogleEvent;

class EventController extends Controller
{
    /**
     * Helper method to get an authorized Google Client for the user
     */
    private function getGoogleClient($user)
    {
        if (!$user || !$user->google_refresh_token) {
            return null;
        }

        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));

        $client->setAccessToken([
            'access_token'  => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in'    => $user->google_token_expires_at ? $user->google_token_expires_at->diffInSeconds(now()) : 0,
        ]);

        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            if (isset($newToken['access_token'])) {
                $user->update([
                    'google_access_token'     => $newToken['access_token'],
                    'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                ]);
            }
        }

        return $client;
    }

    /**
     * @OA\Get(
     *      path="/api/events",
     *      operationId="getEventsList",
     *      tags={"Dashboard - Events"},
     *      summary="Get list of calendar events",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="start", in="query", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="end", in="query", @OA\Schema(type="string", format="date")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');
        $role  = $request->query('role');

        $query = Event::query()->with('user:id,name,email');

        if ($start && $end) {
            $query->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            });
        }

        if ($role) {
            $query->where(function($q) use ($role) {
                $q->where('role', $role)->orWhere('role', 'All');
            });
        }

        $events = $query->orderBy('start_date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/events",
     *      operationId="storeEvent",
     *      tags={"Dashboard - Events"},
     *      summary="Store new calendar event",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "start_date", "description", "role", "location"},
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="start_date", type="string", format="date-time"),
     *              @OA\Property(property="end_date", type="string", format="date-time"),
     *              @OA\Property(property="role", type="string"),
     *              @OA\Property(property="location", type="string"),
     *              @OA\Property(property="url", type="string"),
     *              @OA\Property(property="color", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'description' => 'required|string',
            'role'        => 'required|string',
            'location'    => 'required|string',
            'url'         => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $eventData = $request->all();
        $user = auth()->user();
        if ($user) {
            $eventData['user_id'] = $user->id;
        }

        $event = Event::create($eventData);

        // Sync to Google Calendar if connected
        if ($user && $user->google_refresh_token) {
            try {
                $client = $this->getGoogleClient($user);
                if ($client) {
                    $service = new Calendar($client);
                    $endDate = $request->end_date ?: $request->start_date;

                    $gEvent = new GoogleEvent([
                        'summary'     => $request->title,
                        'location'    => $request->location,
                        'description' => $request->description,
                        'start'       => ['dateTime' => \Carbon\Carbon::parse($request->start_date)->toRfc3339String()],
                        'end'         => ['dateTime' => \Carbon\Carbon::parse($endDate)->toRfc3339String()],
                    ]);

                    $createdGEvent = $service->events->insert('primary', $gEvent);
                    $event->update(['google_event_id' => $createdGEvent->getId()]);
                }
            } catch (\Exception $e) {
                \Log::error('Google Calendar Sync Error (Store): ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data'    => $event
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/events/{id}",
     *      operationId="getEventById",
     *      tags={"Dashboard - Events"},
     *      summary="Get event details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $event
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/events/{id}",
     *      operationId="updateEvent",
     *      tags={"Dashboard - Events"},
     *      summary="Update existing calendar event",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|string|max:255',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'sometimes|nullable|date|after_or_equal:start_date',
            'color'       => 'sometimes|nullable|string|max:20',
            'description' => 'sometimes|string',
            'role'        => 'sometimes|string',
            'location'    => 'sometimes|string',
            'url'         => 'sometimes|nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        // Update in Google Calendar if synced
        $user = auth()->user();
        if ($user && $user->google_refresh_token && $event->google_event_id) {
            try {
                $client = $this->getGoogleClient($user);
                if ($client) {
                    $service = new Calendar($client);
                    $gEvent = $service->events->get('primary', $event->google_event_id);

                    if ($request->has('title'))       $gEvent->setSummary($request->title);
                    if ($request->has('location'))    $gEvent->setLocation($request->location);
                    if ($request->has('description')) $gEvent->setDescription($request->description);
                    if ($request->has('start_date')) {
                        $gEvent->setStart(['dateTime' => \Carbon\Carbon::parse($request->start_date)->toRfc3339String()]);
                    }
                    if ($request->has('end_date') || $request->has('start_date')) {
                        $endDate = $request->end_date ?: $event->end_date;
                        $gEvent->setEnd(['dateTime' => \Carbon\Carbon::parse($endDate)->toRfc3339String()]);
                    }

                    $service->events->update('primary', $event->google_event_id, $gEvent);
                }
            } catch (\Exception $e) {
                \Log::error('Google Calendar Sync Error (Update): ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data'    => $event
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/events/{id}",
     *      operationId="deleteEvent",
     *      tags={"Dashboard - Events"},
     *      summary="Delete existing calendar event",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Delete from Google Calendar if synced
        $user = auth()->user();
        if ($user && $user->google_refresh_token && $event->google_event_id) {
            try {
                $client = $this->getGoogleClient($user);
                if ($client) {
                    $service = new Calendar($client);
                    $service->events->delete('primary', $event->google_event_id);
                }
            } catch (\Exception $e) {
                \Log::error('Google Calendar Sync Error (Delete): ' . $e->getMessage());
            }
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
