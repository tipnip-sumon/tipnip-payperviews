<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display search results
     */
    public function results(Request $request)
    {
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 12;
        
        if (empty($query)) {
            return view('user.search.results', [
                'query' => $query,
                'results' => [],
                'totalResults' => 0
            ]);
        }

        // Perform search across different categories
        $results = [
            'videos' => $this->searchVideos($query, $page, $perPage),
            'users' => $this->searchUsers($query, $page, $perPage),
            'transactions' => $this->searchTransactions($query, $page, $perPage),
            'investments' => $this->searchInvestments($query, $page, $perPage)
        ];

        $totalResults = array_sum(array_map('count', $results));

        return view('user.search.results', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => max(1, ceil($totalResults / $perPage))
            ]
        ]);
    }

    /**
     * AJAX search for suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [
            'videos' => $this->searchVideos($query, 1, 3),
            'users' => $this->searchUsers($query, 1, 3),
            'transactions' => $this->searchTransactions($query, 1, 3),
            'investments' => $this->searchInvestments($query, 1, 3)
        ];

        return response()->json($suggestions);
    }

    /**
     * Search videos (sample data - replace with actual video model)
     */
    private function searchVideos($query, $page = 1, $limit = 12)
    {
        $queryLower = strtolower($query);
        
        // Sample video data - replace with actual database query
        $allVideos = [
            [
                'title' => 'How to Earn Money Online',
                'category' => 'Educational',
                'description' => 'Learn various ways to earn money online through legitimate methods.',
                'duration' => '12:45',
                'views' => '15.2K',
                'thumbnail' => asset('assets/images/videos/video1.jpg')
            ],
            [
                'title' => 'Investment Strategies 2024',
                'category' => 'Finance',
                'description' => 'Complete guide to smart investment strategies for beginners.',
                'duration' => '8:30',
                'views' => '8.7K',
                'thumbnail' => asset('assets/images/videos/video2.jpg')
            ],
            [
                'title' => 'Passive Income Ideas',
                'category' => 'Business',
                'description' => 'Discover multiple passive income streams that work.',
                'duration' => '15:20',
                'views' => '22.1K',
                'thumbnail' => asset('assets/images/videos/video3.jpg')
            ],
            [
                'title' => 'Cryptocurrency Basics',
                'category' => 'Finance',
                'description' => 'Understanding cryptocurrency and blockchain technology.',
                'duration' => '10:15',
                'views' => '11.3K',
                'thumbnail' => asset('assets/images/videos/video4.jpg')
            ],
            [
                'title' => 'Online Business Setup',
                'category' => 'Business',
                'description' => 'Step by step guide to start your online business.',
                'duration' => '18:40',
                'views' => '9.8K',
                'thumbnail' => asset('assets/images/videos/video5.jpg')
            ]
        ];

        // Filter videos based on query
        $filteredVideos = array_filter($allVideos, function($video) use ($queryLower) {
            return strpos(strtolower($video['title']), $queryLower) !== false ||
                   strpos(strtolower($video['category']), $queryLower) !== false ||
                   strpos(strtolower($video['description']), $queryLower) !== false;
        });

        return array_slice(array_values($filteredVideos), 0, $limit);
    }

    /**
     * Search users
     */
    private function searchUsers($query, $page = 1, $limit = 12)
    {
        $queryLower = strtolower($query);
        
        try {
            // Search actual users from database
            $users = User::where(function($q) use ($queryLower) {
                $q->whereRaw('LOWER(username) LIKE ?', ["%{$queryLower}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$queryLower}%"])
                  ->orWhereRaw('LOWER(firstname) LIKE ?', ["%{$queryLower}%"])
                  ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$queryLower}%"]);
            })
            ->limit($limit)
            ->get()
            ->map(function($user) {
                return [
                    'username' => $user->username,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url ?? asset('assets/images/users/16.jpg'),
                    'role' => $this->getUserRole($user),
                    'status' => 'Active',
                    'joined' => $user->created_at->format('Y')
                ];
            })
            ->toArray();

            return $users;
        } catch (\Exception $e) {
            // Fallback to sample data if database query fails
            return $this->getSampleUsers($queryLower, $limit);
        }
    }

    /**
     * Search transactions (sample data - replace with actual transaction model)
     */
    private function searchTransactions($query, $page = 1, $limit = 12)
    {
        $queryLower = strtolower($query);
        
        // Sample transaction data - replace with actual database query
        $allTransactions = [
            [
                'description' => 'Deposit Transaction',
                'type' => 'Deposit',
                'amount' => '$500.00',
                'status' => 'completed',
                'date' => '2024-12-10 14:30:00'
            ],
            [
                'description' => 'Withdrawal Request',
                'type' => 'Withdrawal',
                'amount' => '$250.00',
                'status' => 'pending',
                'date' => '2024-12-09 10:15:00'
            ],
            [
                'description' => 'Investment Return',
                'type' => 'Investment',
                'amount' => '$75.50',
                'status' => 'completed',
                'date' => '2024-12-08 16:45:00'
            ],
            [
                'description' => 'Referral Bonus',
                'type' => 'Bonus',
                'amount' => '$25.00',
                'status' => 'completed',
                'date' => '2024-12-07 09:20:00'
            ]
        ];

        // Filter transactions based on query
        $filteredTransactions = array_filter($allTransactions, function($transaction) use ($queryLower) {
            return strpos(strtolower($transaction['description']), $queryLower) !== false ||
                   strpos(strtolower($transaction['type']), $queryLower) !== false ||
                   strpos(strtolower($transaction['amount']), $queryLower) !== false;
        });

        return array_slice(array_values($filteredTransactions), 0, $limit);
    }

    /**
     * Search investments (sample data - replace with actual investment model)
     */
    private function searchInvestments($query, $page = 1, $limit = 12)
    {
        $queryLower = strtolower($query);
        
        // Sample investment data - replace with actual database query
        $allInvestments = [
            [
                'name' => 'Basic Plan',
                'return' => '5% Daily',
                'min_amount' => '$100',
                'duration' => '30 days',
                'description' => 'Perfect starter plan for new investors.'
            ],
            [
                'name' => 'Premium Plan',
                'return' => '8% Daily',
                'min_amount' => '$500',
                'duration' => '45 days',
                'description' => 'Enhanced returns for serious investors.'
            ],
            [
                'name' => 'VIP Plan',
                'return' => '12% Daily',
                'min_amount' => '$1000',
                'duration' => '60 days',
                'description' => 'Maximum returns for high-value investments.'
            ],
            [
                'name' => 'Enterprise Plan',
                'return' => '15% Daily',
                'min_amount' => '$5000',
                'duration' => '90 days',
                'description' => 'Exclusive plan for enterprise-level investments.'
            ]
        ];

        // Filter investments based on query
        $filteredInvestments = array_filter($allInvestments, function($investment) use ($queryLower) {
            return strpos(strtolower($investment['name']), $queryLower) !== false ||
                   strpos(strtolower($investment['return']), $queryLower) !== false ||
                   strpos(strtolower($investment['description']), $queryLower) !== false;
        });

        return array_slice(array_values($filteredInvestments), 0, $limit);
    }

    /**
     * Get user role based on user data
     */
    private function getUserRole($user)
    {
        // Determine user role based on your business logic
        if (isset($user->is_admin) && $user->is_admin) {
            return 'Administrator';
        }
        
        // Check deposit wallet to determine membership level
        $depositAmount = $user->deposit_wallet ?? 0;
        
        if ($depositAmount >= 5000) {
            return 'VIP Member';
        } elseif ($depositAmount >= 1000) {
            return 'Premium Member';
        } elseif ($depositAmount >= 100) {
            return 'Active Member';
        } else {
            return 'Basic Member';
        }
    }

    /**
     * Get sample users as fallback
     */
    private function getSampleUsers($queryLower, $limit)
    {
        $sampleUsers = [
            [
                'name' => 'John Investor',
                'email' => 'john@example.com',
                'avatar' => asset('assets/images/users/1.jpg'),
                'role' => 'Premium Member',
                'status' => 'Active',
                'joined' => '2024'
            ],
            [
                'name' => 'Sarah Trader',
                'email' => 'sarah@example.com',
                'avatar' => asset('assets/images/users/2.jpg'),
                'role' => 'VIP Member',
                'status' => 'Active',
                'joined' => '2023'
            ],
            [
                'name' => 'Mike Entrepreneur',
                'email' => 'mike@example.com',
                'avatar' => asset('assets/images/users/3.jpg'),
                'role' => 'Active Member',
                'status' => 'Active',
                'joined' => '2024'
            ]
        ];

        // Filter sample users based on query
        $filteredUsers = array_filter($sampleUsers, function($user) use ($queryLower) {
            return strpos(strtolower($user['name']), $queryLower) !== false ||
                   strpos(strtolower($user['email']), $queryLower) !== false ||
                   strpos(strtolower($user['role']), $queryLower) !== false;
        });

        return array_slice(array_values($filteredUsers), 0, $limit);
    }
}
