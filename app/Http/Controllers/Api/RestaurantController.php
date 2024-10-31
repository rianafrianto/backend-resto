<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\OperatingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Restaurant::with('operatingHours');

        // Mengambil filter dari request
        $name = $request->query('name');
        $day = $request->query('day');
        $openingTime = $request->query('opening_time');
        $closingTime = $request->query('closing_time');

        // Menambahkan filter berdasarkan parameter yang diberikan
        if ($name) {
            $query->where('name', 'LIKE', "%$name%");
        }

        if ($day) {
            $query->whereHas('operatingHours', function ($q) use ($day) {
                $q->where('day', $day);
            });
        }

        if ($openingTime) {
            $query->whereHas('operatingHours', function ($q) use ($openingTime) {
                $q->where('opening_time', '>=', $openingTime);
            });
        }

        if ($closingTime) {
            $query->whereHas('operatingHours', function ($q) use ($closingTime) {
                $q->where('closing_time', '<=', $closingTime);
            });
        }

        $restaurants = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Get All Restaurants',
            'data' => $restaurants
        ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'operating_hours' => 'required|array|min:1',
            'operating_hours.*.day' => 'required|string',
            'operating_hours.*.opening_time' => 'required|date_format:H:i:s',
            'operating_hours.*.closing_time' => 'required|date_format:H:i:s',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buat restoran baru
        $restaurant = Restaurant::create($request->only('name'));

        // Simpan jam operasional 
        foreach ($request->operating_hours as $operatingHour) {
            OperatingHour::create([
                'restaurant_id' => $restaurant->id,
                'day' => $operatingHour['day'],
                'opening_time' => $operatingHour['opening_time'],
                'closing_time' => $operatingHour['closing_time'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Restoran berhasil ditambahkan',
            'data' => $restaurant
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
