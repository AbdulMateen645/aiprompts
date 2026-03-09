# User Prompt Management - Edit & Delete Feature

## Overview
Secure implementation allowing users to edit and delete their own submitted prompts with proper authorization and business rules.

---

## Security Features Implemented

### 1. **Ownership Verification** ✅
- Users can only access their own prompts
- Query filters by `submitted_by = user->id`
- Returns 404 if prompt not found or doesn't belong to user

### 2. **Status-Based Restrictions** ✅
- **Approved Prompts**: Cannot be edited or deleted
- **Pending Prompts**: Can be edited and deleted
- **Rejected Prompts**: Can be edited (resets to pending) and deleted

### 3. **Authorization** ✅
- All routes protected by `auth:sanctum` middleware
- User must be authenticated
- Token-based authentication

---

## API Endpoints

### Get Single Prompt
```
GET /api/user/prompts/{id}
Authorization: Bearer {token}

Response:
{
  "id": 1,
  "title": "Prompt Title",
  "prompt_text": "...",
  "category": {...},
  "status": "pending",
  "submitted_by": 10,
  ...
}
```

### Update Prompt
```
PUT /api/user/prompts/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body:
- title (required)
- prompt_text (required)
- category_id (required)
- how_to_use (optional)
- image (optional, only if changing)

Response (Success):
{
  "message": "Prompt updated successfully!",
  "prompt": {...}
}

Response (Approved Prompt):
{
  "message": "Cannot edit approved prompts. Please contact admin."
}
```

### Delete Prompt
```
DELETE /api/user/prompts/{id}
Authorization: Bearer {token}

Response (Success):
{
  "message": "Prompt deleted successfully!"
}

Response (Approved Prompt):
{
  "message": "Cannot delete approved prompts. Please contact admin."
}
```

---

## Business Rules

### Edit Rules:
1. ✅ User must own the prompt
2. ✅ Cannot edit approved prompts
3. ✅ Can edit pending prompts
4. ✅ Can edit rejected prompts (status resets to pending)
5. ✅ Image is optional (only update if new image provided)
6. ✅ Old image deleted if new image uploaded
7. ✅ All inputs sanitized

### Delete Rules:
1. ✅ User must own the prompt
2. ✅ Cannot delete approved prompts
3. ✅ Can delete pending prompts
4. ✅ Can delete rejected prompts
5. ✅ Associated image file deleted from storage

---

## Frontend Implementation Guide

### 1. Display Edit/Delete Buttons
```javascript
// Only show for user's own prompts
{prompt.submitted_by === currentUser.id && (
  <div className="actions">
    {prompt.status !== 'approved' && (
      <>
        <button onClick={() => handleEdit(prompt.id)}>Edit</button>
        <button onClick={() => handleDelete(prompt.id)}>Delete</button>
      </>
    )}
    {prompt.status === 'approved' && (
      <span className="badge">Approved - Contact admin to modify</span>
    )}
  </div>
)}
```

### 2. Edit Prompt
```javascript
const handleEdit = async (promptId) => {
  try {
    // Fetch prompt details
    const response = await fetch(`/api/user/prompts/${promptId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    const prompt = await response.json();
    
    // Open edit form with pre-filled data
    setEditingPrompt(prompt);
    setShowEditModal(true);
  } catch (error) {
    console.error('Error fetching prompt:', error);
  }
};

const submitEdit = async (formData) => {
  try {
    const response = await fetch(`/api/user/prompts/${promptId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`
      },
      body: formData // multipart/form-data
    });
    
    const result = await response.json();
    
    if (response.ok) {
      showSuccess(result.message);
      refreshPrompts();
    } else {
      showError(result.message);
    }
  } catch (error) {
    showError('Failed to update prompt');
  }
};
```

### 3. Delete Prompt
```javascript
const handleDelete = async (promptId) => {
  if (!confirm('Are you sure you want to delete this prompt?')) {
    return;
  }
  
  try {
    const response = await fetch(`/api/user/prompts/${promptId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const result = await response.json();
    
    if (response.ok) {
      showSuccess(result.message);
      refreshPrompts();
    } else {
      showError(result.message);
    }
  } catch (error) {
    showError('Failed to delete prompt');
  }
};
```

---

## Status Flow

```
User Submits → PENDING
                  ↓
Admin Reviews → APPROVED or REJECTED
                  ↓                ↓
            (Cannot Edit)    (Can Edit → Back to PENDING)
            (Cannot Delete)  (Can Delete)
```

---

## Testing Checklist

### Edit Functionality:
- [ ] User can edit their own pending prompt
- [ ] User can edit their own rejected prompt
- [ ] User cannot edit approved prompt (403 error)
- [ ] User cannot edit other user's prompt (404 error)
- [ ] Image updates correctly when new image provided
- [ ] Old image deleted when new image uploaded
- [ ] Prompt data updates correctly
- [ ] Rejected prompt status changes to pending after edit

### Delete Functionality:
- [ ] User can delete their own pending prompt
- [ ] User can delete their own rejected prompt
- [ ] User cannot delete approved prompt (403 error)
- [ ] User cannot delete other user's prompt (404 error)
- [ ] Image file deleted from storage
- [ ] Prompt removed from database

### Security:
- [ ] Unauthenticated requests rejected (401)
- [ ] User cannot access other user's prompts
- [ ] Approved prompts protected from modification
- [ ] Input sanitization working
- [ ] File upload validation working

---

## Error Handling

### HTTP Status Codes:
- `200` - Success
- `401` - Unauthorized (no token)
- `403` - Forbidden (approved prompt)
- `404` - Not Found (prompt doesn't exist or not owned)
- `422` - Validation Error

### Error Messages:
```json
{
  "message": "Cannot edit approved prompts. Please contact admin."
}

{
  "message": "Cannot delete approved prompts. Please contact admin."
}

{
  "message": "Prompt updated successfully!",
  "prompt": {...}
}

{
  "message": "Prompt deleted successfully!"
}
```

---

## Database Queries

### Ownership Check:
```php
Prompt::where('id', $id)
    ->where('submitted_by', $request->user()->id)
    ->firstOrFail();
```

### Status Check:
```php
if ($prompt->status === 'approved') {
    return response()->json([...], 403);
}
```

---

## File Management

### Image Update:
1. Check if new image provided
2. Delete old image from storage
3. Upload new image
4. Update image_url in database

### Image Delete:
1. Get image path from database
2. Delete file from storage
3. Delete prompt record

---

## Frontend UI Recommendations

### Prompt Card:
```
┌─────────────────────────────────┐
│ Prompt Title                    │
│ Status: Pending                 │
│                                 │
│ [Edit] [Delete]                 │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Prompt Title                    │
│ Status: Approved ✓              │
│                                 │
│ Cannot be modified              │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Prompt Title                    │
│ Status: Rejected ✗              │
│ Reason: ...                     │
│                                 │
│ [Edit & Resubmit] [Delete]      │
└─────────────────────────────────┘
```

---

## Existing Functionality Preserved

✅ User can submit new prompts
✅ Admin approval workflow unchanged
✅ Public prompt display unchanged
✅ Stats endpoint working
✅ List user prompts working
✅ All existing routes functional

---

## Summary

**New Endpoints**: 3
- GET /api/user/prompts/{id}
- PUT /api/user/prompts/{id}
- DELETE /api/user/prompts/{id}

**Security Layers**: 3
- Authentication (Sanctum)
- Ownership Verification
- Status-Based Authorization

**Protected Actions**: 2
- Cannot edit approved prompts
- Cannot delete approved prompts

**Clean Implementation**: ✅
- No breaking changes
- Follows existing patterns
- Proper error handling
- Input sanitization
- File cleanup
