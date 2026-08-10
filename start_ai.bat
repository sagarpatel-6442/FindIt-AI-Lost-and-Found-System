@echo off
setlocal
cd /d "%~dp0python_ai"
if not exist ".venv\Scripts\python.exe" (
  echo Creating Python virtual environment...
  py -m venv .venv 2>nul || python -m venv .venv
)
echo Installing or checking AI requirements...
".venv\Scripts\python.exe" -m pip install -r requirements.txt
if errorlevel 1 (
  echo Failed to install Python requirements.
  pause
  exit /b 1
)
echo Starting FindIt AI service at http://127.0.0.1:5000
".venv\Scripts\python.exe" app.py
pause
