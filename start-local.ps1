$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot
$phpExecutable = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $phpExecutable) {
    foreach ($candidate in @('D:/xampp/php/php.exe', 'C:/xampp/php/php.exe')) {
        if (Test-Path -LiteralPath $candidate) { $phpExecutable = $candidate; break }
    }
}
if (-not $phpExecutable) { throw 'PHP tidak ditemukan. Pasang PHP atau XAMPP terlebih dahulu.' }
Write-Host 'Buka http://127.0.0.1:8000/login dan biarkan terminal ini terbuka.'
& $phpExecutable artisan serve --host=127.0.0.1 --port=8000 --no-reload
