# Development FAQ

This file provides some guidance on best practice, tips and tricks for developing against LoadPartner TMS

## Obtaining fresh testing data
The artisan command dev:refresh will clear your database and run the demo seeder to give you a test environment.

```bash
sail artisan dev:refresh
// or 
php artisan dev:refresh
```


The following test user will be created

Email:  `test@test.com`

Pass:   `password`


## Running pre-commit & other dev environment checks

This will run PHPStan and generate IDE completions for any actions.
``` bash
sail artisan dev:check
```

## Adding permissions
1. Add your new permission enum to `app/Enums/Permission.php`. Make sure you include an entry under `label()`!
2. Create a new database migration, call it whatever you want. In the migration, run `App\Enums\Permission::syncToDatabase();` for both up and down.
3. Run migrations to add your new permissions to the database!