# College Fee Management System

## API Documentation

### Authentication

All API endpoints require authentication. Include valid session cookies or authentication headers.

### Base URL
```
http://localhost/college-fees-management-system/app/api/
```

### Response Format

All responses are in JSON format:

```json
{
  "success": true/false,
  "message": "Success or error message",
  "data": {}
}
```

## Student API

### List All Students
```
GET /student_api.php?action=list&page=1
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "student_id": 1,
      "roll_number": "2024001",
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "course_id": 1,
      "department_id": 1
    }
  ]
}
```

### Get Student by ID
```
GET /student_api.php?action=get&id=1
```

### Add New Student
```
POST /student_api.php?action=add

Body:
{
  "roll_number": "2024001",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "9876543210",
  "course_id": 1,
  "department_id": 1,
  "admission_date": "2024-06-15",
  "academic_year": "2024-25"
}
```

### Update Student
```
POST /student_api.php?action=update

Body:
{
  "student_id": 1,
  "first_name": "Jane",
  "email": "jane@example.com"
}
```

### Delete Student
```
POST /student_api.php?action=delete

Body:
{
  "student_id": 1
}
```

## Payment API

### List All Payments
```
GET /payment_api.php?action=list&page=1&status=Completed

Optional Parameters:
- status: Pending|Completed|Failed|Refunded
- student_id: Student ID
- start_date: YYYY-MM-DD
- end_date: YYYY-MM-DD
```

### Get Payment Summary
```
GET /payment_api.php?action=summary

Response:
{
  "success": true,
  "data": [
    {
      "payment_status": "Completed",
      "count": 50,
      "total_amount": "2500000.00"
    }
  ]
}
```

### Record Payment
```
POST /payment_api.php?action=record

Body:
{
  "student_id": 1,
  "fee_id": 1,
  "amount_paid": 50000,
  "payment_date": "2024-07-10",
  "payment_method": "Cash",
  "transaction_id": "TXN123456",
  "reference_number": "REF001"
}
```

### Get Student Payment History
```
GET /payment_api.php?action=history&student_id=1
```

## Receipt API

### List All Receipts
```
GET /receipt_api.php?action=list&page=1
```

### Get Receipt by ID
```
GET /receipt_api.php?action=get&id=1
```

### Issue Receipt
```
POST /receipt_api.php?action=issue

Body:
{
  "payment_id": 1
}

Response:
{
  "success": true,
  "message": "Receipt issued successfully",
  "data": {
    "receipt_id": 1,
    "receipt_number": "RCP20240710001"
  }
}
```

### Get Receipt by Number
```
GET /receipt_api.php?action=get_by_number&number=RCP20240710001
```

### Cancel Receipt
```
POST /receipt_api.php?action=cancel

Body:
{
  "receipt_id": 1,
  "reason": "Cancellation reason"
}
```

## Fee Structure API

### List All Fee Structures
```
GET /fee_api.php?action=list&academic_year=2024-25
```

### Add Fee Structure (Admin/Accounts)
```
POST /fee_api.php?action=add

Body:
{
  "fee_name": "Tuition Fee",
  "course_id": 1,
  "academic_year": "2024-25",
  "amount": 50000,
  "description": "Annual tuition fee"
}
```

## Course API

### List All Courses
```
GET /course_api.php?action=list&department_id=1
```

### Add Course (Admin/Manager)
```
POST /course_api.php?action=add

Body:
{
  "course_name": "B.Tech CSE",
  "course_code": "BTECH-CS",
  "department_id": 1,
  "duration_years": 4
}
```

## Department API

### List All Departments
```
GET /department_api.php?action=list
```

### Add Department (Admin Only)
```
POST /department_api.php?action=add

Body:
{
  "department_name": "Computer Science",
  "department_code": "CS",
  "head_of_department": "Dr. John"
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized access"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Access denied"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "An error occurred. Please contact administrator."
}
```

## Rate Limiting

API endpoints are rate limited to prevent abuse:
- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated requests

## Pagination

List endpoints support pagination:
```
?page=1&perPage=20
```

Default items per page: 20
Maximum items per page: 100
