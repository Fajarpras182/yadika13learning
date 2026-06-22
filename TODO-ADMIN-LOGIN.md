# Fix Admin Login Issue

## Steps to Complete:

### 1. Reset and Seed Database ✅ (Run first)
```
php artisan migrate:fresh --seed
```
**Explanation:** This drops all tables, re-runs all migrations, then runs seeders to create admin users:
- admin@example.com / admin123 (from AdminUserSeeder)
- admin@smkyadika13.sch.id / admin123 (from UserSeeder)

### 2. Start Laravel Server
```
php artisan serve
```
Visit http://127.0.0.1:8000/login

### 3. Test Login Credentials
| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@example.com` | `admin123` |
| Admin | `admin@smkyadika13.sch.id` | `admin123` |

### 4. Verify Admin Users in DB
```
php artisan tinker
```
Then:
```php
User::where('role','admin')->get(['id','name','email','role','is_active']);
exit
```

### 5. Check Logs if Still Fails
```
type storage\logs\laravel.log | findstr "Login attempt"
```
Look for debug lines showing what failed (user not found, password mismatch, inactive).

### 6. Optional: Add Demo Creds to Login Page
Edit `resources/views/auth/login.blade.php` to show credentials table.

## Expected Result
- Successful login redirects to /admin/dashboard
- Both admin emails work with password `admin123`

**Note:** Change passwords after first login for security.

Progress: 0/6 steps complete

