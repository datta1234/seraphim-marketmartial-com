
## Commands
- `php artisan mm:reset`:       Clear All Market Request Data
- `php artisan mm:populate`:    Generate Userbase
- `php artisan mm:sample`:      Generate Sample Data



## Response Formats
```json
{
    "message": "Relevant Success Message",
    "data": { 
        /* Relevant Object Data*/ 
    }
}
```

## Error Formats
```json
{
    "message": "Relevant Error Message",
    "errors": {
        "field": "error message"
    }
}
```