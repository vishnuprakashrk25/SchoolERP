# School ERP System API

Laravel 10 REST API backend for School ERP Management System.

## Features
- **Core Systems & Auth:** User & Role Management (Spatie Laravel Permission), Sanctum Token Authentication, 2FA.
- **Student Enrollment:** Student Admissions, `student_code` unique indexing, custom fields (`apaar_id`, `aadharshila_no`, `pen_no`), and Parent-Child associations.
- **Staff & HR:** Staff/Teacher records (`staff_code`, `category`), unified multi-role assignment (`teacher` + `parent`).
- **Parent Portal:** `GET /api/parent/children` for multi-child management.
- **Dashboard & Stats:** Entity count cards (`students`, `teachers`, `parents`, `staffs`), institution metrics (`suic_code`, `zone`, `category_of_institution`).
- **Direct Public Uploads:** Storage uploads handled directly under `public/uploads/...` for 100% shared hosting & cPanel compatibility.
- **Swagger Documentation:** OpenAPI specs generated via L5-Swagger.

## API Documentation
Access Swagger UI at `/api/documentation`.
