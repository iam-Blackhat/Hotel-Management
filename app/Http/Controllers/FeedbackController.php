<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create($validated);

        return response()->json([
            'message' => 'Feedback submitted successfully!',
            'data' => $feedback,
        ], 201);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('limit', 10);

        $fleedbacks = Feedback::orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $fleedbacks,
        ], 200);
    }


    public function ratingsSummary()
{
    $feedbacks = Feedback::all();
    $total = $feedbacks->count();

    if ($total === 0) {
        return response()->json([
            'average_rating' => 0,
            'total_ratings' => 0,
            'breakdown' => [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0
            ]
        ]);
    }

    $average = round($feedbacks->avg('rating'), 2);

    $breakdown = $feedbacks->groupBy('rating')->map->count();
    $ratingsCount = [];

    for ($i = 1; $i <= 5; $i++) {
        $ratingsCount[$i] = $breakdown[$i] ?? 0;
    }

    return response()->json([
        'average_rating' => $average,
        'total_ratings' => $total,
        'breakdown' => $ratingsCount
    ]);
}

}

