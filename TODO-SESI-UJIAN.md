# Fix Manajemen Sesi Ujian Errors

## Issues:
- Query/paginate failure (log fallback)
- N+1 queries in index.blade.php class loop

## Plan:
- [ ] 1. Read guru/sesi-ujian/create.blade.php, edit.blade.php, show.blade.php
- [ ] 2. Add proper eager loading to GuruController::sesiUjian()
- [ ] 3. Add schoolClasses relation or accessor to Ujian
- [ ] 4. Optimize index.blade.php - use eager loaded classes
- [ ] 5. Test /guru/sesi-ujian - no errors, pagination works

**Progress:** 0/5

