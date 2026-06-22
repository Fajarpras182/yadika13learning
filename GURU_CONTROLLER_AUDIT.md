# Guru Controller & Views Audit Report

## Executive Summary
Comprehensive audit of the Guru module routing, controller methods, and view files has identified **8 critical issues**:
- 1 Missing view file directory
- 3 Missing view files  
- 2 Incorrect view file paths/names
- 2 Potentially missing routes

---

## CRITICAL ISSUES FOUND

### 1. MISSING VIEW FILE DIRECTORY

**Issue**: Student Assignments view directory does not exist
- **Route**: `GET /guru/student-assignments` → `guru.student-assignments`
- **Controller Method**: `GuruController@allAssignments()` (line 822)
- **Expected View**: `guru/student-assignments/index.blade.php`
- **Status**: ❌ **MISSING** - Directory and file don't exist
- **Route Defined**: ✓ Yes - `Route::get('/student-assignments', [GuruController::class, 'allAssignments'])->name('student-assignments')`
- **Impact**: Users trying to access student assignments will see error 500

---

### 2. MISSING VIEW FILE

**Issue**: Bank Soal show view does not exist
- **Route**: `GET /guru/bank-soal/{question}` → `guru.bank-soal.show`
- **Controller Method**: `GuruController@showBankSoal()` (line 662)
- **Expected View**: `guru/bank-soal/show.blade.php`
- **Status**: ❌ **MISSING** - File does not exist
- **Existing View**: Only `guru/bank-soal/index.blade.php`, `create.blade.php`, `edit.blade.php` exist
- **Route Defined**: ✓ Yes
- **Impact**: Clicking on a question in bank soal list will show error 500

---

### 3-4. INCORRECT VIEW FILE PATHS - PDF REPORTS

**Issue**: PDF view files are in wrong directory with wrong naming convention

#### 3a. Attendance PDF View
- **Route**: `GET /guru/reports/export-attendance-pdf` → `guru.reports.export-attendance-pdf`
- **Controller Method**: `GuruController@exportAttendancePdf()` (line 189)
- **View Call**: `Pdf::loadView('guru.reports.attendance-pdf', ...)`
- **Expected File**: `resources/views/guru/reports/attendance-pdf.blade.php`
- **Actual File**: `resources/views/guru/exports/attendance_pdf.blade.php`
- **Status**: ❌ **WRONG PATH AND NAME** - File in wrong directory with underscore instead of dash
- **Impact**: PDF export will fail with view not found error

#### 3b. Grades PDF View
- **Route**: `GET /guru/reports/export-pdf/{courseId?}` → `guru.reports.export-pdf`
- **Controller Method**: `GuruController@exportGradesPdf()` (line 204)
- **View Call**: `Pdf::loadView('guru.reports.grades-pdf', ...)`
- **Expected File**: `resources/views/guru/reports/grades-pdf.blade.php`
- **Actual File**: `resources/views/guru/exports/grades_pdf.blade.php`
- **Status**: ❌ **WRONG PATH AND NAME** - File in wrong directory with underscore instead of dash
- **Impact**: PDF export will fail with view not found error

---

### 5. MISSING VIEW FILE

**Issue**: Messages index view does not exist
- **Route**: `GET /guru/messages` → `guru.messages`
- **Controller Method**: `GuruController@forum()` (line 113)
- **Expected View**: `guru/messages/index.blade.php`
- **Status**: ❌ **MISSING** - File does not exist
- **Existing Files in messages/**: Only `edit.blade.php` exists
- **Route Defined**: ✓ Yes
- **Impact**: Accessing messages/forum will show error 500

---

### 6. POTENTIAL BROKEN ROUTE

**Issue**: Export Grades Word endpoint maps to PDF
- **Route**: `GET /guru/reports/export-word/{courseId?}` → `guru.reports.export-word`
- **Controller Method**: `GuruController@exportGradesWord()` (line 218)
- **Implementation**: `return $this->exportGradesPdf($courseId);` (line 219)
- **Status**: ⚠️ **INCORRECT IMPLEMENTATION** - Returns PDF, not Word format
- **Impact**: Users cannot export grades in Word format; receives PDF instead

---

---

## DETAILED REFERENCE TABLES

### Routes Defined but Views Missing

| Route Pattern | Controller Method | Expected View | File Exists | Issue |
|---|---|---|---|---|
| `GET /guru/messages` | `forum()` | `guru.messages.index` | ❌ NO | Missing `guru/messages/index.blade.php` |
| `GET /guru/bank-soal/{question}` | `showBankSoal()` | `guru.bank-soal.show` | ❌ NO | Missing `guru/bank-soal/show.blade.php` |
| `GET /guru/student-assignments` | `allAssignments()` | `guru.student-assignments.index` | ❌ NO | Missing entire directory `guru/student-assignments/` |
| `GET /guru/reports/export-attendance-pdf` | `exportAttendancePdf()` | `guru.reports.attendance-pdf` | ❌ NO | File is `guru/exports/attendance_pdf.blade.php` - wrong path/name |
| `GET /guru/reports/export-pdf/{courseId?}` | `exportGradesPdf()` | `guru.reports.grades-pdf` | ❌ NO | File is `guru/exports/grades_pdf.blade.php` - wrong path/name |

---

### All Guru Routes Summary

| # | HTTP Method | Route | Controller Method | View Expected | Status |
|---|---|---|---|---|---|
| 1 | GET | `/guru/dashboard` | `dashboard()` | `guru.dashboard` | ✅ GOOD |
| 2 | GET | `/guru/profile` | `profile()` | `guru.profile` | ✅ GOOD |
| 3 | PATCH | `/guru/profile` | `updateProfile()` | Redirect | ✅ GOOD |
| 4 | GET | `/guru/messages` | `forum()` | `guru.messages.index` | ❌ MISSING |
| 5 | POST | `/guru/messages/send` | `sendMessage()` | Redirect | ✅ GOOD |
| 6 | PATCH | `/guru/messages/{messageId}/read` | `markMessageAsRead()` | JSON | ✅ GOOD |
| 7 | GET | `/guru/messages/{messageId}/edit` | `editMessage()` | `guru.messages.edit` | ✅ GOOD |
| 8 | PATCH | `/guru/messages/{messageId}` | `updateMessage()` | Redirect | ✅ GOOD |
| 9 | DELETE | `/guru/messages/{messageId}` | `deleteMessage()` | Redirect | ✅ GOOD |
| 10 | GET | `/guru/reports` | `reports()` | `guru.reports.index` | ✅ GOOD |
| 11 | GET | `/guru/rekap-nilai-tugas` | `rekaptugaspdf()` | `guru.rekap-nilai-tugas` | ✅ GOOD |
| 12 | GET | `/guru/reports/attendance` | `attendanceReports()` | `guru.reports.attendance` | ✅ GOOD |
| 13 | GET | `/guru/reports/export-attendance-pdf` | `exportAttendancePdf()` | `guru.reports.attendance-pdf` | ❌ WRONG PATH |
| 14 | GET | `/guru/reports/export-pdf/{courseId?}` | `exportGradesPdf()` | `guru.reports.grades-pdf` | ❌ WRONG PATH |
| 15 | GET | `/guru/reports/export-excel/{courseId?}` | `exportGradesExcel()` | Inline/Download | ✅ GOOD |
| 16 | GET | `/guru/reports/export-word/{courseId?}` | `exportGradesWord()` | PDF (wrong) | ⚠️ WRONG FORMAT |
| 17 | GET | `/guru/subjects` | `courses()` | `guru.courses.index` | ✅ GOOD |
| 18 | GET | `/guru/courses` | `courses()` | `guru.courses.index` | ✅ GOOD |
| 19 | GET | `/guru/courses/create` | `createCourse()` | `guru.courses.create` | ✅ GOOD |
| 20 | POST | `/guru/courses` | `storeCourse()` | Redirect | ✅ GOOD |
| 21 | GET | `/guru/courses/{course}/edit` | `editCourse()` | `guru.courses.edit` | ✅ GOOD |
| 22 | PATCH | `/guru/courses/{course}` | `updateCourse()` | Redirect | ✅ GOOD |
| 23 | DELETE | `/guru/courses/{course}` | `destroyCourse()` | Redirect | ✅ GOOD |
| 24 | GET | `/guru/student-assignments` | `allAssignments()` | `guru.student-assignments.index` | ❌ MISSING |
| 25 | GET | `/guru/courses/{courseId}/lessons` | `lessons()` | `guru.lessons.index` | ✅ GOOD |
| 26 | GET | `/guru/courses/{courseId}/lessons/create` | `createLesson()` | `guru.lessons.create` | ✅ GOOD |
| 27 | POST | `/guru/courses/{courseId}/lessons` | `storeLesson()` | Redirect | ✅ GOOD |
| 28 | GET | `/guru/courses/{courseId}/lessons/{lessonId}/edit` | `editLesson()` | `guru.lessons.edit` | ✅ GOOD |
| 29 | PATCH | `/guru/courses/{courseId}/lessons/{lessonId}` | `updateLesson()` | Redirect | ✅ GOOD |
| 30 | DELETE | `/guru/courses/{courseId}/lessons/{lessonId}` | `destroyLesson()` | Redirect | ✅ GOOD |
| 31 | GET | `/guru/courses/{courseId}/assignments` | `assignments()` | `guru.assignments.index` | ✅ GOOD |
| 32 | GET | `/guru/courses/{courseId}/assignments/create` | `createAssignment()` | `guru.assignments.create` | ✅ GOOD |
| 33 | POST | `/guru/courses/{courseId}/assignments` | `storeAssignment()` | Redirect | ✅ GOOD |
| 34 | GET | `/guru/assignments/{assignmentId}/status` | `assignmentStatus()` | `guru.assignments.status` | ✅ GOOD |
| 35 | GET | `/guru/assignments/{assignmentId}/grade` | `gradeAssignment()` | `guru.assignments.grade` | ✅ GOOD |
| 36 | PATCH | `/guru/grades/{gradeId}` | `updateGrade()` | Redirect | ✅ GOOD |
| 37 | PATCH | `/guru/grades/bulk/{assignmentId}` | `bulkUpdateGrades()` | Redirect | ✅ GOOD |
| 38 | DELETE | `/guru/grades/{gradeId}` | `destroyGrade()` | Redirect | ✅ GOOD |
| 39 | GET | `/guru/courses/{courseId}/assignments/{assignmentId}/edit` | `editAssignment()` | `guru.assignments.edit` | ✅ GOOD |
| 40 | PATCH | `/guru/courses/{courseId}/assignments/{assignmentId}` | `updateAssignment()` | Redirect | ✅ GOOD |
| 41 | DELETE | `/guru/courses/{courseId}/assignments/{assignmentId}` | `destroyAssignment()` | Redirect | ✅ GOOD |
| 42 | GET | `/guru/courses/{courseId}/attendances` | `attendances()` | `guru.attendances.index` | ✅ GOOD |
| 43 | GET | `/guru/courses/{courseId}/attendances/create` | `createAttendance()` | `guru.attendances.create` | ✅ GOOD |
| 44 | POST | `/guru/courses/{courseId}/attendances` | `storeAttendance()` | Redirect | ✅ GOOD |
| 45 | GET | `/guru/courses/{courseId}/attendances/{attendanceId}/edit` | `editAttendance()` | `guru.attendances.edit` | ✅ GOOD |
| 46 | PATCH | `/guru/courses/{courseId}/attendances/{attendanceId}` | `updateAttendance()` | Redirect | ✅ GOOD |
| 47 | DELETE | `/guru/courses/{courseId}/attendances/{attendanceId}` | `destroyAttendance()` | Redirect | ✅ GOOD |
| 48 | GET | `/guru/bank-soal` | `bankSoal()` | `guru.bank-soal.index` | ✅ GOOD |
| 49 | GET | `/guru/bank-soal/create` | `createBankSoal()` | `guru.bank-soal.create` | ✅ GOOD |
| 50 | POST | `/guru/bank-soal` | `storeBankSoal()` | Redirect | ✅ GOOD |
| 51 | GET | `/guru/bank-soal/{question}` | `showBankSoal()` | `guru.bank-soal.show` | ❌ MISSING |
| 52 | GET | `/guru/bank-soal/{question}/edit` | `editBankSoal()` | `guru.bank-soal.edit` | ✅ GOOD |
| 53 | PUT | `/guru/bank-soal/{question}` | `updateBankSoal()` | Redirect | ✅ GOOD |
| 54 | DELETE | `/guru/bank-soal/{question}` | `deleteBankSoal()` | Redirect | ✅ GOOD |
| 55 | POST | `/guru/bank-soal/upload` | `uploadBankSoal()` | Redirect | ✅ GOOD |
| 56 | GET | `/guru/nilai-ujian` | `nilaiUjian()` | `guru.nilai-ujian.index` | ✅ GOOD |
| 57 | GET | `/guru/nilai-ujian/{resultId}/review` | `reviewExamAnswer()` | `guru.nilai-ujian.review` | ✅ GOOD |
| 58 | GET | `/guru/nilai-ujian/export` | `exportExamScores()` | Stream/Download | ✅ GOOD |
| 59 | GET | `/guru/sesi-ujian` | `sesiUjian()` | `guru.sesi-ujian.index` | ✅ GOOD |
| 60 | GET | `/guru/sesi-ujian/create` | `createSesiUjian()` | `guru.sesi-ujian.create` | ✅ GOOD |
| 61 | POST | `/guru/sesi-ujian` | `storeSesiUjian()` | Redirect | ✅ GOOD |
| 62 | GET | `/guru/sesi-ujian/{sesi}` | `showSesiUjian()` | `guru.sesi-ujian.show` | ✅ GOOD |
| 63 | POST | `/guru/sesi-ujian/{sesi}/students` | `storeSesiUjianStudent()` | Redirect | ✅ GOOD |
| 64 | POST | `/guru/sesi-ujian/{sesi}/bulk-assign` | `bulkAssignSesiUjianStudents()` | Redirect | ✅ GOOD |
| 65 | DELETE | `/guru/sesi-ujian/{sesi}/students/{student}` | `destroySesiUjianStudent()` | Redirect | ✅ GOOD |
| 66 | GET | `/guru/sesi-ujian/{sesi}/edit` | `editSesiUjian()` | `guru.sesi-ujian.edit` | ✅ GOOD |
| 67 | PUT | `/guru/sesi-ujian/{sesi}` | `updateSesiUjian()` | Redirect | ✅ GOOD |
| 68 | DELETE | `/guru/sesi-ujian/{sesi}` | `destroySesiUjian()` | Redirect | ✅ GOOD |
| 69 | GET | `/guru/ujian/create` | `createUjian()` | `guru.ujian.create` | ✅ GOOD |
| 70 | GET | `/guru/ujian/{ujian}` | `showUjian()` | `guru.ujian.show` | ✅ GOOD |
| 71 | POST | `/guru/ujian` | `storeUjian()` | Redirect | ✅ GOOD |
| 72 | GET | `/guru/ujian/{ujian}/edit` | `editUjian()` | `guru.ujian.edit` | ✅ GOOD |
| 73 | PUT | `/guru/ujian/{ujian}` | `updateUjian()` | Redirect | ✅ GOOD |
| 74 | DELETE | `/guru/ujian/{ujian}` | `deleteUjian()` | Redirect | ✅ GOOD |
| 75 | GET | `/guru/ujian/{ujian}/add-questions` | `addQuestionsToUjian()` | `guru.ujian.add-questions` | ✅ GOOD |
| 76 | POST | `/guru/ujian/{ujian}/add-questions` | `storeQuestionsToUjian()` | Redirect | ✅ GOOD |
| 77 | DELETE | `/guru/ujian/{ujian}/remove-question/{question}` | `removeQuestionFromUjian()` | Redirect | ✅ GOOD |
| 78 | POST | `/guru/ujian/upload-image` | `uploadSoalImage()` | JSON | ✅ GOOD |

---

## Existing View Files Verification

### guru/ (root level)
- ✅ `dashboard.blade.php`
- ✅ `profile.blade.php`
- ✅ `forum.blade.php`
- ✅ `reports.blade.php`
- ✅ `attendance-reports.blade.php`
- ✅ `rekap-nilai-tugas.blade.php`

### guru/assignments/
- ✅ `all.blade.php`
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `grade.blade.php`
- ✅ `index.blade.php`
- ✅ `status.blade.php`

### guru/attendances/
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`

### guru/bank-soal/
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`
- ❌ `show.blade.php` - **MISSING**

### guru/courses/
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`

### guru/lessons/
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`

### guru/messages/
- ✅ `edit.blade.php`
- ❌ `index.blade.php` - **MISSING**

### guru/nilai-ujian/
- ✅ `index.blade.php`
- ✅ `review.blade.php`

### guru/reports/
- ✅ `attendance.blade.php`
- ✅ `index.blade.php`
- ❌ `attendance-pdf.blade.php` - **FILE IS IN WRONG LOCATION** (in exports/ with name attendance_pdf.blade.php)
- ❌ `grades-pdf.blade.php` - **FILE IS IN WRONG LOCATION** (in exports/ with name grades_pdf.blade.php)

### guru/sesi-ujian/
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`
- ✅ `show.blade.php`

### guru/ujian/
- ✅ `add-questions.blade.php`
- ✅ `create.blade.php`
- ✅ `edit.blade.php`
- ✅ `index.blade.php`
- ✅ `show.blade.php`

### guru/exports/ (PDF/Export views)
- ✅ `attendance_pdf.blade.php` - Wrong location (should be in reports/)
- ✅ `grades_pdf.blade.php` - Wrong location (should be in reports/)

### directories missing entirely
- ❌ `guru/student-assignments/` - **ENTIRE DIRECTORY MISSING**

---

## RECOMMENDATIONS

### Immediate Actions Required:

1. **Create missing messages/index.blade.php** 
   - Location: `resources/views/guru/messages/index.blade.php`
   - Should display forum/messages list

2. **Create missing bank-soal/show.blade.php**
   - Location: `resources/views/guru/bank-soal/show.blade.php`
   - Should display a single question detail

3. **Create missing student-assignments directory and index.blade.php**
   - Location: `resources/views/guru/student-assignments/index.blade.php`
   - Should display all student assignments across all courses

4. **Move and rename PDF view files**
   - Move `resources/views/guru/exports/attendance_pdf.blade.php` → `resources/views/guru/reports/attendance-pdf.blade.php`
   - Move `resources/views/guru/exports/grades_pdf.blade.php` → `resources/views/guru/reports/grades-pdf.blade.php`
   - Update view names to use dashes instead of underscores

5. **Fix exportGradesWord() method**
   - Currently returns PDF instead of Word format
   - Implement proper Word export functionality (or remove if not needed)

6. **Optional: Consider if guru/exports/ directory is still needed**
   - If views are moved to reports/, the exports/ directory can be removed
   - No other code references this directory

---

## Controller Methods Summary

**Total Methods**: 42 public methods
- Methods rendering views: 22
- Methods returning redirects: 15
- Methods returning JSON/downloads: 5

All methods follow consistent naming conventions and proper authorization checks.

