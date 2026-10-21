Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$sourceDir = "D:\japan project\wordpress-theme\kaitoriakiba-theme"
$zipPath = "D:\japan project\kaitoriakiba-theme.zip"

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
$files = Get-ChildItem -Path $sourceDir -Recurse -File

foreach ($file in $files) {
    $rel = $file.FullName.Substring($sourceDir.Length + 1).Replace("\", "/")
    $entryName = "kaitoriakiba-theme/" + $rel
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $entryName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
}
$zip.Dispose()

Write-Host "Zip creation complete. Verifying entries..."
$zipRead = [System.IO.Compression.ZipFile]::OpenRead($zipPath)
$count = $zipRead.Entries.Count
Write-Host ("Total entries: " + $count)
$hasStyle = $zipRead.Entries | Where-Object { $_.FullName -eq "kaitoriakiba-theme/style.css" }
if ($hasStyle) {
    Write-Host "SUCCESS: kaitoriakiba-theme/style.css found in zip!"
} else {
    Write-Host "ERROR: style.css NOT found!"
}
$zipRead.Dispose()
