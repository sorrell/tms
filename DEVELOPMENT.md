# Development FAQ

This file provides some guidance on best practice, tips and tricks for developing against LoadPartner TMS

## Adding permissions
1. Add your new permission enum to `app/Enums/Permission.php`. Make sure you include an entry under `label()`!
2. Create a new database migration, call it whatever you want. In the migration, run `App\Enums\Permission::syncToDatabase();` for both up and down.
3. Run migrations to add your new permissions to the database!