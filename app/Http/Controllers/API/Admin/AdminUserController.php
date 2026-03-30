<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    // GET /api/admin/users
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $role = strtolower(trim((string) $request->query('role', '')));
        $status = strtolower(trim((string) $request->query('status', '')));
        $perPage = (int) $request->query('per_page', 10);

        if ($perPage < 1) {
            $perPage = 10;
        }

        if ($perPage > 50) {
            $perPage = 50;
        }

        $query = User::with(['city:id,name'])
            ->withCount('properties')
            ->where(function ($q) {
                $q->where('is_admin', 0)->orWhereNull('is_admin');
            });

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role !== '' && $role !== 'all') {
            $query->where(function ($q) use ($role) {
                if ($role === 'agent') {
                    $q->where('role', 'agent')
                      ->orWhere('is_agent', 1);
                }

                if ($role === 'user') {
                    $q->where(function ($sub) {
                        $sub->where('role', 'user')
                            ->orWhere(function ($nested) {
                                $nested->where(function ($n) {
                                    $n->whereNull('role')
                                      ->orWhere('role', '');
                                })->where(function ($n) {
                                    $n->where('is_agent', 0)
                                      ->orWhereNull('is_agent');
                                });
                            });
                    });
                }
            });
        }

        if ($status !== '' && $status !== 'all') {
            if ($status === 'active') {
                $query->where(function ($q) {
                    $q->where('status', 'active')
                      ->orWhereNull('status')
                      ->orWhere('status', '');
                });
            } else {
                $query->where('status', $status);
            }
        }

        $users = $query->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $users->getCollection()->transform(function ($user) {
            return $this->transformUser($user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully',
            'data' => [
                'filters' => [
                    'search' => $search,
                    'role' => $role,
                    'status' => $status,
                    'per_page' => $perPage,
                ],
                'role_options' => ['all', 'agent', 'user'],
                'status_options' => ['all', 'active', 'suspended', 'inactive'],
                'list' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ],
        ]);
    }

    // GET /api/admin/users/{id}
    public function show($id)
    {
        $user = User::with(['city:id,name'])
            ->withCount('properties')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User details fetched successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $this->displayRole($user),
                'status' => $this->displayStatus($user->status),
                'city_id' => $user->city_id,
                'city' => optional($user->city)->name,
                'listings' => (int) $user->properties_count,
                'phone' => $user->phone,
                'mobile' => $user->mobile,
                'whatsapp' => $user->whatsapp,
                'address' => $user->address,
                'agency_name' => $user->agency_name,
                'is_agent' => (int) ($user->is_agent ?? 0),
                'created_at' => optional($user->created_at)?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    // PUT /api/admin/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', 'string', Rule::in(['user', 'agent'])],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'inactive'])],
            'city_id' => 'nullable|integer',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'agency_name' => 'nullable|string|max:255',
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'city_id' => $validated['city_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'address' => $validated['address'] ?? null,
            'agency_name' => $validated['agency_name'] ?? null,
            'is_agent' => $validated['role'] === 'agent' ? 1 : 0,
        ];

        $user->forceFill($payload)->save();

        $freshUser = User::with(['city:id,name'])
            ->withCount('properties')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $this->transformUser($freshUser),
        ]);
    }

    // POST /api/admin/users/{id}/suspend
    public function suspend($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill([
            'status' => 'suspended',
        ])->save();

        $freshUser = User::with(['city:id,name'])
            ->withCount('properties')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User suspended successfully',
            'data' => $this->transformUser($freshUser),
        ]);
    }

    // POST /api/admin/users/{id}/activate
    public function activate($id)
    {
        $user = User::findOrFail($id);

        $user->forceFill([
            'status' => 'active',
        ])->save();

        $freshUser = User::with(['city:id,name'])
            ->withCount('properties')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User activated successfully',
            'data' => $this->transformUser($freshUser),
        ]);
    }

    private function transformUser($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $this->displayRole($user),
            'city' => optional($user->city)->name,
            'listings' => (int) $user->properties_count,
            'status' => $this->displayStatus($user->status),
            'status_label' => ucfirst($this->displayStatus($user->status)),
            'created_at' => optional($user->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function displayRole($user): string
    {
        if (($user->role ?? null) === 'agent' || (int) ($user->is_agent ?? 0) === 1) {
            return 'Agent';
        }

        return 'User';
    }

    private function displayStatus($status): string
    {
        if ($status === null || $status === '') {
            return 'active';
        }

        return strtolower((string) $status);
    }
}