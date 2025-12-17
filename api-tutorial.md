# Espresso Calibration API Tutorial

## Introduction

This tutorial will guide you through using the Espresso Calibration API to track and optimize your espresso extraction parameters. The API allows coffee enthusiasts and professionals to log their espresso shots, track bean and grinder information, and analyze the results to achieve the perfect extraction.

## Getting Started

### 1. Authentication Flow

Before using the API, you need to authenticate. Here's the complete flow:

#### Step 1: Register a New Account
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Your Name",
    "email": "your.email@example.com",
    "password": "yourpassword123",
    "password_confirmation": "yourpassword123"
  }'
```

Expected Response:
```json
{
  "user": {
    "id": 1,
    "name": "Your Name",
    "email": "your.email@example.com",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  },
  "token": "your_personal_access_token",
  "message": "Registration successful"
}
```

#### Step 2: Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your.email@example.com",
    "password": "yourpassword123"
  }'
```

Expected Response:
```json
{
  "user": {
    "id": 1,
    "name": "Your Name",
    "email": "your.email@example.com",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  },
  "token": "your_personal_access_token",
  "message": "Login successful"
}
```

**Important**: Save the token from the response. You'll need it for all authenticated requests.

### 2. Using API Tokens

Include your token in the Authorization header for all protected endpoints:

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

## Complete Espresso Calibration Workflow

### Part 1: Set Up Your Coffee Information

#### Create Coffee Bean Information

First, add the bean you're using:

```bash
curl -X POST http://localhost:8000/api/beans \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ethiopian Natural Yirgacheffe",
    "roaster": "Local Coffee Roasters",
    "roast_level": "light",
    "origin": "Ethiopia",
    "processing_method": "natural",
    "variety": "Heirloom"
  }'
```

Expected Response:
```json
{
  "id": 1,
  "name": "Ethiopian Natural Yirgacheffe",
  "roaster": "Local Coffee Roasters",
  "roast_level": "light",
  "origin": "Ethiopia",
  "processing_method": "natural",
  "variety": "Heirloom",
  "user_id": 1,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

#### Create Grinder Information

Next, add your grinder details:

```bash
curl -X POST http://localhost:8000/api/grinders \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mazzer Mini",
    "brand": "Mazzer",
    "model": "Mini",
    "type": "burr"
  }'
```

Expected Response:
```json
{
  "id": 1,
  "name": "Mazzer Mini",
  "brand": "Mazzer",
  "model": "Mini",
  "type": "burr",
  "user_id": 1,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### Part 2: Start a Calibration Session

Create a new calibration session to begin testing:

```bash
curl -X POST http://localhost:8000/api/calibration-sessions \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "bean_id": 1,
    "grinder_id": 1,
    "title": "Yirgacheffe Extraction Test",
    "notes": "Testing for optimal extraction of Ethiopian beans",
    "target_grind": "25 clicks",
    "target_dose": 18.5,
    "target_yield": 37.0,
    "target_time": 25
  }'
```

Expected Response:
```json
{
  "id": 1,
  "bean_id": 1,
  "grinder_id": 1,
  "title": "Yirgacheffe Extraction Test",
  "notes": "Testing for optimal extraction of Ethiopian beans",
  "target_grind": "25 clicks",
  "target_dose": 18.5,
  "target_yield": 37.0,
  "target_time": 25,
  "user_id": 1,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### Part 3: Log Individual Shots

For each shot you make during the session, log the parameters:

```bash
curl -X POST http://localhost:8000/api/calibration-sessions/1/shots \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "grind_setting": "25 clicks",
    "dose": 18.5,
    "yield": 37.0,
    "time_seconds": 25,
    "taste_notes": "Bright acidity, floral notes",
    "action_taken": "Adjust grind finer by 1 click"
  }'
```

Expected Response:
```json
{
  "id": 1,
  "calibration_session_id": 1,
  "shot_number": 1,
  "grind_setting": "25 clicks",
  "dose": 18.5,
  "yield": 37.0,
  "time_seconds": 25,
  "taste_notes": "Bright acidity, floral notes",
  "action_taken": "Adjust grind finer by 1 click",
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### Part 4: Review and Update

#### Get All Shots for a Session

Retrieve all shots from your calibration session:

```bash
curl -X GET http://localhost:8000/api/calibration-sessions/1/shots \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

#### Get a Specific Shot

```bash
curl -X GET http://localhost:8000/api/calibration-sessions/1/shots/1 \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

#### Update a Shot (if you made an error)

```bash
curl -X PUT http://localhost:8000/api/calibration-sessions/1/shots/1 \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "taste_notes": "Bright acidity, floral notes, slight underextraction"
  }'
```

## Complete Example Workflow

Here's a complete example of a typical calibration session:

1. **Setup Phase**:
   - Register/login (if new user)
   - Add bean information
   - Add grinder information

2. **Calibration Session**:
   - Create session with targets
   - Make first shot with target parameters
   - Log shot results
   - Make adjustments based on taste
   - Log next shot with adjusted parameters
   - Repeat until optimal extraction found

3. **Analysis Phase**:
   - Review all shots in the session
   - Identify optimal parameters
   - Record findings for future use

## Error Handling Examples

### Authentication Error (401)
```bash
curl -X GET http://localhost:8000/api/beans \
  -H "Authorization: Bearer invalid_token"
```

Response:
```json
{
  "message": "Unauthenticated.",
  "error": "Token required or invalid"
}
```

### Validation Error (422)
```bash
curl -X POST http://localhost:8000/api/beans \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "",
    "roaster": "Roaster Name"
  }'
```

Response:
```json
{
  "errors": {
    "name": [
      "The name field is required."
    ],
    "roast_level": [
      "The roast level field is required."
    ],
    "origin": [
      "The origin field is required."
    ],
    "processing_method": [
      "The processing method field is required."
    ]
  }
}
```

## Data Analysis Tips

### Recommended Session Structure
1. Start with your target parameters
2. Make 3-5 shots initially to establish baseline
3. Use 1-click adjustments for fine-tuning
4. Focus on one variable at a time (grind, dose, yield ratio)
5. Document taste notes and extraction quality

### Key Parameters to Track
- **Dose to Yield Ratio**: Typically 1:2 for espresso
- **Extraction Time**: Usually between 20-30 seconds
- **TDS/Brix**: If you have tools to measure
- **Taste Notes**: Acidity, body, sweetness, balance

## Advanced Usage

### Get All Your Calibration Sessions

```bash
curl -X GET http://localhost:8000/api/calibration-sessions \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

### Update Bean Information

```bash
curl -X PUT http://localhost:8000/api/beans/1 \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json" \
  -d '{
    "origin": "Ethiopia, Yirgacheffe"
  }'
```

### Delete a Shot (if incorrectly logged)

```bash
curl -X DELETE http://localhost:8000/api/calibration-sessions/1/shots/1 \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

## Logging Out

When you're finished, you can log out to invalidate your token:

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer your_personal_access_token" \
  -H "Content-Type: application/json"
```

Response:
```json
{
  "message": "Successfully logged out"
}
```

## Best Practices

1. **Always include the Authorization header** for protected endpoints
2. **Validate your data** before sending requests to avoid 422 errors
3. **Use consistent naming** for beans and grinders to make them easier to find later
4. **Take detailed notes** about taste and extraction quality
5. **Start with reasonable targets** based on your bean and equipment
6. **Save your tokens securely** and don't expose them in client-side code

## Troubleshooting

### Common Issues

**Issue: 401 Unauthorized**
- Solution: Check that your token is correct and hasn't expired
- Solution: Make sure the Authorization header format is correct

**Issue: 422 Validation Error**
- Solution: Check all required fields are present
- Solution: Ensure data types and formats are correct

**Issue: 404 Not Found**
- Solution: Verify the ID in the URL exists
- Solution: Ensure you own the resource (not someone else's)

This tutorial provides a complete walkthrough of using the Espresso Calibration API to track and optimize your espresso extraction parameters. Follow these steps to get the most out of your coffee calibration process!