# Local XAMPP setup

1. Extract the folder as `C:\xampp\htdocs\findit`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/findit/setup.php`.
4. Keep the default MySQL settings for normal XAMPP: host `127.0.0.1`, port `3306`, database `findit`, user `root`, blank password.
5. Create the administrator account and click Install FindIt.
6. Double-click `start_ai.bat` to create the local Python environment, install requirements and start the AI matcher.
7. Open `http://localhost/findit/`.

The PHP fallback matcher still allows matching if the Python service is not running, but image similarity requires the Python AI service.

The project automatically detects its folder URL, so the application does not need hard-coded `/findit` links in the PHP configuration.
