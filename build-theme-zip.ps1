Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$sourceDir = "D:\japan project\wordpress-theme\kaitoriakiba-theme"
$zipPath = "D:\japan project\kaitoriakiba-theme.zip"

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)

# 1. Add root theme directory entry
$zip.CreateEntry("kaitoriakiba-theme/") | Out-Null

# 2. Add all subdirectories explicitly with trailing slash
$dirs = Get-ChildItem -Path $sourceDir -Recurse -Directory
foreach ($dir in $dirs) {
    $relDir = $dir.FullName.Substring($sourceDir.Length + 1).Replace("\", "/") + "/"
    $entryDirName = "kaitoriakiba-theme/" + $relDir
    $zip.CreateEntry($entryDirName) | Out-Null
}

# 3. Add all files with forward slashes
$files = Get-ChildItem -Path $sourceDir -Recurse -File
foreach ($file in $files) {
    $relFile = $file.FullName.Substring($sourceDir.Length + 1).Replace("\", "/")
    $entryFileName = "kaitoriakiba-theme/" + $relFile
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $entryFileName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
}

$zip.Dispose()

Write-Host "Zip successfully created with full directory structure."
