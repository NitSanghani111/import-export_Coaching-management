# import-export_Coaching-management-
this is a Dynamic  Website for my client to manage and markating its import/export coaching

## Docker services
- PHP app: http://localhost:8000
- phpMyAdmin: http://localhost:8080 (hosted against `mysql` service)
- MySQL: exposed on host 3306 (internal 3306)

## Usage (Windows PowerShell)
Make sure Docker Desktop is running, then from the project folder:

```powershell
# Start app + DB + phpMyAdmin
docker compose up -d mysql php phpmyadmin

# Stop stack
docker compose down
```

### Switch between this project and a demo container
Use the helper script to ensure only one of the two runs at a time.

```powershell
# Ensure this project is up and stop a container named 'demo'
./scripts/switch.ps1 -mode project -DemoContainerName demo

# Stop this project's PHP and start your 'demo' container (if it exists)
./scripts/switch.ps1 -mode demo -DemoContainerName demo
```

Notes:
- Adjust `-DemoContainerName` to match your demo container name.
- The script stops only the PHP service to free port 8000; DB and phpMyAdmin can stay up. Uncomment the `docker compose down` line in the script if you want to stop everything.
