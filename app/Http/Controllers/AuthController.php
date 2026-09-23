<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterCustomerRequest;
use App\Http\Requests\RegisterRestaurantRequest;
use App\Models\ActivityLog;
use App\Models\DeliverySetting;
use App\Models\Restaurant;
use App\Models\RestaurantBusinessHour;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $this->activeUser($request);
        $this->recordActivity($request, $user, 'login');

        return $this->dashboard($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user instanceof User) {
            $this->recordActivity($request, $user, 'logout');
        }

        return redirect()->route('login')->with('status', 'You have been signed out securely.');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRestaurantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($request, $data): User {
            $user = new User(Arr::only($data, ['name', 'email', 'password', 'phone']));
            $user->forceFill(['role' => 'vendor', 'is_active' => true, 'restaurant_id' => null])->save();

            $restaurant = new Restaurant;
            $restaurant->forceFill([
                'owner_id' => $user->getKey(),
                'name' => $data['restaurant_name'],
                'slug' => $this->restaurantSlug($data['restaurant_name']),
                'status' => 'pending',
                'email' => $data['restaurant_email'] ?? $data['email'],
                'phone' => $data['restaurant_phone'] ?? $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'postal_code' => $data['postal_code'],
            ])->save();

            $user->forceFill(['restaurant_id' => $restaurant->getKey()])->save();

            $delivery = new DeliverySetting;
            $delivery->forceFill(['restaurant_id' => $restaurant->getKey()])->save();

            foreach (range(0, 6) as $day) {
                $hours = new RestaurantBusinessHour;
                $hours->forceFill([
                    'restaurant_id' => $restaurant->getKey(),
                    'day' => $day,
                    'is_closed' => false,
                    'opens_at' => '10:00:00',
                    'closes_at' => '22:00:00',
                ])->save();
            }

            $plan = SubscriptionPlan::query()->where('is_active', true)->orderBy('id')->first();

            if ($plan !== null) {
                $subscription = new Subscription;
                $subscription->forceFill([
                    'restaurant_id' => $restaurant->getKey(),
                    'subscription_plan_id' => $plan->getKey(),
                    'status' => 'trial',
                    'starts_at' => now(),
                    'ends_at' => now()->addDays(14),
                ])->save();
            }

            $this->recordActivity($request, $user, 'restaurant.created', ['status' => 'pending']);

            return $user;
        });

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        event(new Registered($user));

        return $this->dashboard($user)->with('status', 'Your restaurant is pending approval. Verify your email and start setting up your menu.');
    }

    public function showCustomerRegister(): View
    {
        return view('auth.customer-register');
    }

    public function registerCustomer(RegisterCustomerRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = new User(Arr::only($request->validated(), ['name', 'email', 'password', 'phone']));
            $user->forceFill(['role' => 'customer', 'restaurant_id' => null, 'is_active' => true])->save();

            return $user;
        });

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        event(new Registered($user));

        return $this->dashboard($user)->with('status', 'Welcome! Please check your inbox to verify your email address.');
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $credentials = $request->validate(['email' => ['required', 'string', 'email', 'max:255']]);

        Password::sendResetLink($credentials);

        return back()->with('status', 'If an account exists for this email, a password reset link has been sent. Please check your inbox.');
    }

    public function resetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => is_string($request->query('email')) ? $request->query('email') : '',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $credentials = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255', 'confirmed', PasswordRule::min(8)->letters()->mixedCase()->numbers()],
        ]);

        $status = Password::reset($credentials, function (User $user, string $password): void {
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', __($status));
    }

    public function verificationNotice(Request $request): View|RedirectResponse
    {
        $user = $this->activeUser($request);

        return $user->hasVerifiedEmail() ? $this->dashboard($user) : view('auth.verify-email');
    }

    public function sendVerification(Request $request): RedirectResponse
    {
        $user = $this->activeUser($request);

        if ($user->hasVerifiedEmail()) {
            return $this->dashboard($user);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $this->activeUser($request);
        $request->fulfill();

        return $this->dashboard($user)->with('status', 'Your email address has been verified.');
    }

    private function activeUser(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->is_active, 403);

        return $user;
    }

    private function dashboard(User $user): RedirectResponse
    {
        $route = match ($user->role) {
            'super_admin' => 'admin.dashboard',
            'vendor' => 'vendor.dashboard',
            'staff' => 'staff.dashboard',
            'customer' => 'customer.dashboard',
            default => abort(403),
        };

        return redirect()->route($route);
    }

    private function restaurantSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'restaurant';

        do {
            $slug = $base.'-'.Str::lower(Str::random(10));
        } while (Restaurant::query()->withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }

    private function normalizeEmail(Request $request): void
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => Str::lower(trim($request->input('email')))]);
        }
    }

    /** @param array<string, string> $metadata */
    private function recordActivity(Request $request, User $user, string $action, array $metadata = []): void
    {
        if (! class_exists(ActivityLog::class)) {
            return;
        }

        $log = new ActivityLog;
        $log->forceFill([
            'restaurant_id' => $user->restaurant_id,
            'user_id' => $user->getKey(),
            'action' => $action,
            'module' => 'auth',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'metadata' => ['role' => $user->role, ...$metadata],
        ])->save();
    }
}
