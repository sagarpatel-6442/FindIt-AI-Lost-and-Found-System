Get-ChildItem -Path .. -Recurse -Filter *.php | ForEach-Object {
    php -l $_.FullName
}
