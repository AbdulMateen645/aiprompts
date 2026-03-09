# API Testing Examples - User Prompt Management

## Setup
```bash
# Get user token first (login or register)
POST http://localhost:8000/api/login
{
  "email": "user@example.com",
  "password": "password"
}

# Save the token from response
TOKEN="your-token-here"
```

---

## Test Scenarios

### 1. Get User's Prompts
```bash
curl -X GET http://localhost:8000/api/user/prompts \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "My Prompt",
      "status": "pending",
      "submitted_by": 10,
      ...
    }
  ]
}
```

---

### 2. Get Single Prompt (Own)
```bash
curl -X GET http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

**Expected Response (Success):**
```json
{
  "id": 1,
  "title": "My Prompt",
  "prompt_text": "...",
  "category": {...},
  "status": "pending"
}
```

**Expected Response (Not Owned):**
```json
404 Not Found
```

---

### 3. Update Prompt (Pending)
```bash
curl -X PUT http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -F "title=Updated Title" \
  -F "prompt_text=Updated prompt text" \
  -F "category_id=1" \
  -F "how_to_use=Updated instructions"
```

**Expected Response (Success):**
```json
{
  "message": "Prompt updated successfully!",
  "prompt": {...}
}
```

**Expected Response (Approved Prompt):**
```json
{
  "message": "Cannot edit approved prompts. Please contact admin."
}
```

---

### 4. Update Prompt with New Image
```bash
curl -X PUT http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -F "title=Updated Title" \
  -F "prompt_text=Updated text" \
  -F "category_id=1" \
  -F "image=@/path/to/new-image.jpg"
```

---

### 5. Delete Prompt (Pending)
```bash
curl -X DELETE http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

**Expected Response (Success):**
```json
{
  "message": "Prompt deleted successfully!"
}
```

**Expected Response (Approved Prompt):**
```json
{
  "message": "Cannot delete approved prompts. Please contact admin."
}
```

---

### 6. Try to Edit Other User's Prompt
```bash
curl -X PUT http://localhost:8000/api/user/prompts/999 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -F "title=Hacked"
```

**Expected Response:**
```json
404 Not Found
```

---

### 7. Try Without Authentication
```bash
curl -X GET http://localhost:8000/api/user/prompts/1 \
  -H "Accept: application/json"
```

**Expected Response:**
```json
401 Unauthorized
```

---

## JavaScript/Axios Examples

### Get Prompt
```javascript
const getPrompt = async (promptId) => {
  try {
    const response = await axios.get(
      `http://localhost:8000/api/user/prompts/${promptId}`,
      {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      }
    );
    return response.data;
  } catch (error) {
    if (error.response.status === 404) {
      console.error('Prompt not found or not owned');
    }
    throw error;
  }
};
```

### Update Prompt
```javascript
const updatePrompt = async (promptId, formData) => {
  try {
    const response = await axios.put(
      `http://localhost:8000/api/user/prompts/${promptId}`,
      formData,
      {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      }
    );
    return response.data;
  } catch (error) {
    if (error.response.status === 403) {
      alert('Cannot edit approved prompts');
    }
    throw error;
  }
};

// Usage
const formData = new FormData();
formData.append('title', 'Updated Title');
formData.append('prompt_text', 'Updated text');
formData.append('category_id', '1');
if (newImage) {
  formData.append('image', newImage);
}
await updatePrompt(1, formData);
```

### Delete Prompt
```javascript
const deletePrompt = async (promptId) => {
  if (!confirm('Are you sure?')) return;
  
  try {
    const response = await axios.delete(
      `http://localhost:8000/api/user/prompts/${promptId}`,
      {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      }
    );
    alert(response.data.message);
    return true;
  } catch (error) {
    if (error.response.status === 403) {
      alert('Cannot delete approved prompts');
    }
    return false;
  }
};
```

---

## React Component Example

```jsx
import { useState, useEffect } from 'react';
import axios from 'axios';

const UserPrompts = () => {
  const [prompts, setPrompts] = useState([]);
  const [editingPrompt, setEditingPrompt] = useState(null);
  const token = localStorage.getItem('token');

  const fetchPrompts = async () => {
    const response = await axios.get('/api/user/prompts', {
      headers: { Authorization: `Bearer ${token}` }
    });
    setPrompts(response.data.data);
  };

  const handleEdit = async (promptId) => {
    const response = await axios.get(`/api/user/prompts/${promptId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    setEditingPrompt(response.data);
  };

  const handleUpdate = async (formData) => {
    try {
      await axios.put(
        `/api/user/prompts/${editingPrompt.id}`,
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      alert('Updated successfully!');
      setEditingPrompt(null);
      fetchPrompts();
    } catch (error) {
      alert(error.response.data.message);
    }
  };

  const handleDelete = async (promptId) => {
    if (!confirm('Delete this prompt?')) return;
    
    try {
      await axios.delete(`/api/user/prompts/${promptId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      alert('Deleted successfully!');
      fetchPrompts();
    } catch (error) {
      alert(error.response.data.message);
    }
  };

  useEffect(() => {
    fetchPrompts();
  }, []);

  return (
    <div>
      {prompts.map(prompt => (
        <div key={prompt.id} className="prompt-card">
          <h3>{prompt.title}</h3>
          <span className={`status ${prompt.status}`}>
            {prompt.status}
          </span>
          
          {prompt.status !== 'approved' && (
            <div className="actions">
              <button onClick={() => handleEdit(prompt.id)}>
                Edit
              </button>
              <button onClick={() => handleDelete(prompt.id)}>
                Delete
              </button>
            </div>
          )}
          
          {prompt.status === 'approved' && (
            <p className="info">
              Approved prompts cannot be modified
            </p>
          )}
        </div>
      ))}
    </div>
  );
};
```

---

## Postman Collection

### Collection Variables:
- `base_url`: http://localhost:8000
- `token`: (set after login)

### Requests:

1. **Login**
   - POST `{{base_url}}/api/login`
   - Body: `{"email": "user@example.com", "password": "password"}`
   - Tests: `pm.environment.set("token", pm.response.json().token);`

2. **Get User Prompts**
   - GET `{{base_url}}/api/user/prompts`
   - Headers: `Authorization: Bearer {{token}}`

3. **Get Single Prompt**
   - GET `{{base_url}}/api/user/prompts/1`
   - Headers: `Authorization: Bearer {{token}}`

4. **Update Prompt**
   - PUT `{{base_url}}/api/user/prompts/1`
   - Headers: `Authorization: Bearer {{token}}`
   - Body (form-data):
     - title: "Updated Title"
     - prompt_text: "Updated text"
     - category_id: 1

5. **Delete Prompt**
   - DELETE `{{base_url}}/api/user/prompts/1`
   - Headers: `Authorization: Bearer {{token}}`

---

## Quick Test Script

```bash
#!/bin/bash

# Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' \
  | jq -r '.token')

echo "Token: $TOKEN"

# Get user prompts
echo "\n=== User Prompts ==="
curl -s -X GET http://localhost:8000/api/user/prompts \
  -H "Authorization: Bearer $TOKEN" | jq

# Get single prompt
echo "\n=== Single Prompt ==="
curl -s -X GET http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" | jq

# Update prompt
echo "\n=== Update Prompt ==="
curl -s -X PUT http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" \
  -F "title=Updated Title" \
  -F "prompt_text=Updated text" \
  -F "category_id=1" | jq

# Delete prompt
echo "\n=== Delete Prompt ==="
curl -s -X DELETE http://localhost:8000/api/user/prompts/1 \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

## Expected Behavior Summary

| Action | Pending | Rejected | Approved |
|--------|---------|----------|----------|
| View   | ✅      | ✅       | ✅       |
| Edit   | ✅      | ✅ (→Pending) | ❌ (403) |
| Delete | ✅      | ✅       | ❌ (403) |

✅ = Allowed
❌ = Forbidden
